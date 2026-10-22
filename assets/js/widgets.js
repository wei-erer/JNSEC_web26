/* ============================================================
   JNSEC · 竹隐 —— 页面上的两个小工具
   ① 剖 URL：把地址按段拆开，顺带标出安全上的注意点
   ② 报文工坊：粘一段 HTTP 请求进来，帮你认它、挑毛病
   ============================================================ */
(function () {
  'use strict';

  var esc = function (s) {
    return String(s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  };

  /* ==========================================================
     ① 剖 URL
     ========================================================== */
  var urlIn = document.getElementById('url-in');
  if (urlIn) {
    var echo = document.getElementById('url-echo');
    var partsBox = document.getElementById('url-parts');
    var verdictBox = document.getElementById('url-verdict');

    // 按顺序扫描，保留原文的每一个字符，这样高亮不会把 %40 之类改写掉
    var scanUrl = function (raw) {
      var segs = [];
      var push = function (k, t) { if (t !== '' && t != null) segs.push({ k: k, t: t }); };
      var pos = 0;

      var m = /^([a-zA-Z][a-zA-Z0-9+.\-]*):\/\//.exec(raw);
      if (m) {
        push('scheme', m[1]);
        push('cut', '://');
        pos = m[0].length;
      }

      var authEnd = raw.length;
      for (var i = pos; i < raw.length; i++) {
        var ch = raw.charAt(i);
        if (ch === '/' || ch === '?' || ch === '#') { authEnd = i; break; }
      }
      var auth = raw.slice(pos, authEnd);

      if (auth) {
        var at = auth.lastIndexOf('@');
        if (at > -1) {
          var cred = auth.slice(0, at);
          var ci = cred.indexOf(':');
          if (ci > -1) {
            push('user', cred.slice(0, ci));
            push('cut', ':');
            push('pass', cred.slice(ci + 1));
          } else {
            push('user', cred);
          }
          push('cut', '@');
          auth = auth.slice(at + 1);
        }
        if (auth.charAt(0) === '[') {
          var close = auth.indexOf(']');
          if (close > -1) {
            push('host', auth.slice(0, close + 1));
            if (auth.charAt(close + 1) === ':') {
              push('cut', ':');
              push('port', auth.slice(close + 2));
            }
          } else {
            push('host', auth);
          }
        } else {
          var lc = auth.lastIndexOf(':');
          if (lc > -1 && /^\d*$/.test(auth.slice(lc + 1))) {
            push('host', auth.slice(0, lc));
            push('cut', ':');
            push('port', auth.slice(lc + 1));
          } else {
            push('host', auth);
          }
        }
      }
      pos = authEnd;

      var hashIdx = raw.indexOf('#', pos);
      var qIdx = raw.indexOf('?', pos);
      var pathEnd = raw.length;
      if (qIdx > -1 && qIdx < pathEnd) pathEnd = qIdx;
      if (hashIdx > -1 && hashIdx < pathEnd) pathEnd = hashIdx;
      push('path', raw.slice(pos, pathEnd));

      if (qIdx > -1 && (hashIdx === -1 || qIdx < hashIdx)) {
        push('cut', '?');
        push('query', raw.slice(qIdx + 1, hashIdx > -1 ? hashIdx : raw.length));
      }
      if (hashIdx > -1) {
        push('cut', '#');
        push('fragment', raw.slice(hashIdx + 1));
      }
      return segs;
    };

    var collect = function (segs) {
      var o = { scheme: '', user: '', pass: '', host: '', port: '', path: '', query: '', fragment: '', hasAuthority: false };
      segs.forEach(function (s) {
        if (s.k === 'cut') { if (s.t === '://') o.hasAuthority = true; return; }
        if (s.k in o) o[s.k] += s.t;
      });
      return o;
    };

    var LABELS = {
      scheme: ['协议', 'Protocol'],
      user: ['用户', 'User'],
      pass: ['口令', 'Password'],
      host: ['主机', 'Host / Domain'],
      port: ['端口', 'Port'],
      path: ['路径', 'Path'],
      query: ['查询串', 'Query String'],
      fragment: ['片段', 'Fragment']
    };

    var render = function () {
      var raw = urlIn.value.trim();
      if (!raw) {
        echo.textContent = '（在上面的框里写一个 URL）';
        partsBox.innerHTML = '';
        verdictBox.innerHTML = '';
        return;
      }
      var segs = scanUrl(raw);
      var o = collect(segs);

      // 彩色回显
      echo.innerHTML = segs.map(function (s) {
        return '<span class="seg s-' + s.k + '">' + esc(s.t) + '</span>';
      }).join('') || '<span class="seg s-cut">（空）</span>';

      // 逐段解释
      var order = ['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'];
      var rows = order.map(function (k) {
        var val = o[k];
        var has = val !== '';
        var label = LABELS[k];
        var hint = '';
        if (k === 'scheme') hint = has ? (val === 'https' ? '加密传输，默认 443' : (val === 'http' ? '明文传输，默认 80' : '非 HTTP 协议')) : '没写协议时，浏览器按当前页面补，抓包工具按你填的补';
        if (k === 'host') hint = has ? (/\d+\.\d+\.\d+\.\d+/.test(val) ? '直接写了 IP' : '域名，最终由 DNS 解析成 IP') : '必填项；缺了它，TCP 都不知道去哪';
        if (k === 'port') hint = has ? '非默认端口，别忘在 Host 头里带上' : '省略则用协议默认端口';
        if (k === 'path') hint = has ? '资源在服务器上的位置' : '一般是 /';
        if (k === 'query') hint = has ? '会和请求一起发给服务器' : '没有参数';
        if (k === 'fragment') hint = has ? '只留在浏览器，不发给服务器' : '没有锚点';
        if (k === 'user') hint = has ? '登录用的用户名' : '没有内嵌账号';
        if (k === 'pass') hint = has ? '登录用的密码' : '没有内嵌口令';

        return '<li class="' + (has ? 'hit' : 'miss') + '">' +
          '<span class="k">' + label[0] + '<em>' + label[1] + '</em></span>' +
          '<span class="v">' + (has ? '<code>' + esc(val) + '</code>' : '<span>—</span>') +
          '<br><span style="color:var(--ink-ghost);font-size:.8rem">' + hint + '</span></span></li>';
      });
      partsBox.innerHTML = rows.join('');

      // 安全提示
      var tips = [];
      if (o.pass) tips.push('URL 里带着口令。这段明文会被写进浏览器历史、代理日志、服务端 access.log，跨站请求时还可能随 <code>Referer</code> 送出去 —— 这是真实存在的泄露面。');
      else if (o.user) tips.push('URL 里有 userinfo（<code>user@host</code>）。很多解析器会把 <code>@</code> 之前的内容当账号，伪造链接时就在这儿做文章。');
      if (o.scheme === 'http') tips.push('走的是 <code>http</code>，全程明文，同网段抓包就能看到内容和 Cookie。');
      if (o.query && /(^|&)(id|url|file|path|page|next|callback)=/i.test(decodeURIComponent(o.query))) tips.push('查询串里有 <code>id</code>/<code>url</code>/<code>file</code> 这类名字 —— 后端若直接拼进 SQL、拼进文件路径、或拿去发起请求，就分别是注入、路径穿越、SSRF 的常见入口。');
      if (o.path.indexOf('..') > -1 || /%2e%2e/i.test(o.path)) tips.push('路径里有 <code>..</code>，这是目录穿越的经典长相，注意看服务端有没有规范化后再校验。');
      if (o.port && ['22', '3306', '6379', '27017', '8080', '9200'].indexOf(o.port) > -1) tips.push('端口 <code>' + esc(o.port) + '</code> 常出现在内部服务上，能从外网访问到就是信息泄露点。');
      if (o.fragment && /<|javascript:/i.test(o.fragment)) tips.push('片段里带了疑似脚本。片段本身不发服务器，但会被前端 JS 取出来拼接 —— 那就能打 DOM XSS。');

      verdictBox.innerHTML = '<span class="tag">此刻能看出的东西</span>' +
        (tips.length
          ? '<ul>' + tips.map(function (t) { return '<li>' + t + '</li>'; }).join('') + '</ul>'
          : '<p style="margin:0">没什么异常。接着往下看，URL 只是这封信的信封，里面的内容在 HTTP 报文那一节。</p>');
    };

    urlIn.addEventListener('input', render);
    render();
  }

  /* ==========================================================
     ② 报文工坊
     ========================================================== */
  var packetIn = document.getElementById('packet-in');
  if (packetIn) {
    var out = document.getElementById('packet-out');
    var findings = document.getElementById('packet-findings');

    var PRESETS = {
      plain: 'GET /index.html HTTP/1.1\r\nHost: shop.example.com\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\nAccept: text/html,application/xhtml+xml\r\nConnection: close\r\n',
      cookie: 'GET /user/profile HTTP/1.1\r\nHost: shop.example.com\r\nCookie: PHPSESSID=298zf09hf012fh2; role=user\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\nAccept: */*\r\n',
      form: 'POST /api/login HTTP/1.1\r\nHost: shop.example.com\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: 31\r\nCookie: PHPSESSID=298zf09hf012fh2\r\n\r\nusername=admin&password=123456',
      shady: 'GET /admin/../../etc/passwd HTTP/1.1\r\nHost: 127.0.0.1:8080\r\nX-Forwarded-For: 127.0.0.1\r\nReferer: http://shop.example.com/\r\nCookie: role=admin\r\n'
    };

    var parsePacket = function (text) {
      var norm = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
      var splitAt = norm.indexOf('\n\n');
      var headRaw = splitAt > -1 ? norm.slice(0, splitAt) : norm;
      var body = splitAt > -1 ? norm.slice(splitAt + 2) : '';
      var lines = headRaw.split('\n');
      var startLine = (lines.shift() || '').trim();
      var headers = [];
      lines.forEach(function (line) {
        if (!line.trim()) return;
        var idx = line.indexOf(':');
        if (idx < 0) { headers.push({ name: line.trim(), value: '', broken: true }); return; }
        headers.push({ name: line.slice(0, idx).trim(), value: line.slice(idx + 1).trim(), broken: false });
      });
      return { startLine: startLine, headers: headers, body: body };
    };

    var head = function (name, pkt) {
      var found = pkt.headers.filter(function (h) { return h.name.toLowerCase() === name.toLowerCase(); });
      return found.length ? found : null;
    };

    var renderPacket = function () {
      var raw = packetIn.value;
      if (!raw.trim()) {
        out.innerHTML = '<span class="ln-dim">（左边输入或点一个预设）</span>';
        findings.innerHTML = '';
        return;
      }
      var pkt = parsePacket(raw);
      var html = [];
      var sl = pkt.startLine;
      var m = /^([A-Za-z]+)\s+(\S+)\s+(HTTP\/[\d.]+)$/.exec(sl);
      if (m) {
        html.push('<span class="ln-start">' + esc(m[1]) + ' <b>' + esc(m[2]) + '</b> ' + esc(m[3]) + '</span>');
      } else if (sl) {
        html.push('<span class="ln-bad">' + esc(sl) + '   ← 起始行不像 "方法 路径 版本"</span>');
      }
      pkt.headers.forEach(function (h) {
        if (h.broken) html.push('<span class="ln-bad">' + esc(h.name) + '   ← 这行没有冒号</span>');
        else html.push('<span class="ln-head"><b>' + esc(h.name) + '</b>: ' + esc(h.value) + '</span>');
      });
      if (pkt.body) {
        html.push('<span class="ln-dim">（空行）</span>');
        html.push('<span class="ln-body">' + esc(pkt.body) + '</span>');
      }
      out.innerHTML = html.join('\n');

      /* ---- 挑毛病 ---- */
      var notes = [];
      var add = function (warn, tag, text) { notes.push({ warn: warn, tag: tag, text: text }); };

      if (!m) {
        add(true, '起始行', '请求行应该是 <code>方法 路径 HTTP/版本</code> 三段，空格分隔。');
      } else {
        var method = m[1].toUpperCase();
        if (method === 'GET' && pkt.body) add(true, '方法', 'GET 方法带了请求体。规范没禁止，但很多中间件、缓存、日志会把它丢掉，也常被用来做请求走私。');
        if (['PUT', 'DELETE', 'PATCH'].indexOf(method) > -1) add(false, '方法', '<code>' + esc(method) + '</code> 直接修改或删除服务端资源。很多站点只在页面上做了权限，忘了这些方法 —— 用 OPTIONS 探一下 <code>Allow</code> 看看。');
        if (/\.\.|\/\.\.|%2e%2e/i.test(m[2])) add(true, '路径', '路径里有 <code>..</code>。这是目录穿越：服务端拼接路径读文件，没做规范化就会越过根目录。');
        if (m[3] !== 'HTTP/1.1' && m[3] !== 'HTTP/2') add(false, '版本', '<code>' + esc(m[3]) + '</code>。HTTP/1.1 起 Host 变成必填；HTTP/1.0 可能会让后端走另一套处理逻辑，有时能绕过校验。');
      }

      var hostAll = head('host', pkt);
      if (!hostAll) add(true, 'Host', '没有 Host 头。HTTP/1.1 要求必须有，缺了服务端一般直接回 400。');
      else if (hostAll.length > 1) add(true, 'Host', '出现了 ' + hostAll.length + ' 个 Host。不同组件取的值可能不一样（前端取第一个、后端取最后一个），这就是 Host 头注入/缓存投毒的温床。');
      var hostVal = hostAll && hostAll[0] ? hostAll[0].value : '';
      if (hostVal && (/^127\.|^localhost|^0\.0\.0\.0/i.test(hostVal) || /:(\d{2,5})$/.test(hostVal))) {
        add(false, 'Host', 'Host 指向本机或非标端口。密码重置链接、Webhook 回调这类功能如果直接拿 Host 拼绝对地址，攻击者换掉它就能钓走 token。');
      }

      var xff = head('x-forwarded-for', pkt);
      if (xff) add(false, 'XFF', '<code>X-Forwarded-For</code> 是客户端能自己写的。只信任它的服务端，等于把"我是谁"交给攻击者填 —— 后台白名单、限流、日志都能被它骗过去。');

      var referer = head('referer', pkt);
      if (referer) add(false, 'Referer', '带着 Referer。有的站点只检查它来判断"是不是从自家页面来的"，但请求头全都可以伪造，Referer 校验拦不住真正想打的人。');

      var cookieH = head('cookie', pkt);
      if (cookieH) {
        var names = cookieH[0].value.split(';').map(function (p) { return p.split('=')[0].trim(); }).filter(Boolean);
        add(false, 'Cookie', '带了 ' + names.length + ' 个 cookie：<code>' + esc(names.join(', ')) + '</code>。Cookie 是会话的凭证，谁拿到谁就是你 —— 回头去 <code>Set-Cookie</code> 那边看看 HttpOnly 和 SameSite 有没有配。');
        if (names.some(function (n) { return /role|admin|is_?vip|level|debug/i.test(n); })) {
          add(true, '提权', 'cookie 名字里有 <code>role</code>/<code>admin</code>/<code>level</code> 这类字段。如果它是明文存权限、并且服务端直接信它，改成 <code>admin</code> 就上岸了 —— 这叫水平越权/垂直越权。');
        }
      }

      var cl = head('content-length', pkt);
      if (cl && pkt.body) {
        var declared = parseInt(cl[0].value, 10);
        var actual = new TextEncoder().encode(pkt.body).length;
        if (!isNaN(declared) && declared !== actual) {
          add(true, '长度', '声明 <code>Content-Length: ' + declared + '</code>，实际请求体 ' + actual + ' 字节，对不上。真实环境里这种情况要么被拒绝，要么被按不同方式截断 —— 请求走私最爱这种不一致。');
        }
      }
      if (pkt.body && /^\s*\{/.test(pkt.body)) {
        add(false, 'JSON', '请求体是 JSON。注意后端有没有把里面的字段直接拼进查询、模板或文件路径，JSON 不等于安全。');
      }

      findings.innerHTML = notes.map(function (n) {
        return '<div class="finding' + (n.warn ? ' warn' : '') + '"><span class="icon">' + esc(n.tag) + '</span><span>' + n.text + '</span></div>';
      }).join('');
    };

    packetIn.addEventListener('input', renderPacket);

    Array.prototype.slice.call(document.querySelectorAll('[data-preset]')).forEach(function (btn) {
      btn.addEventListener('click', function () {
        var key = btn.getAttribute('data-preset');
        if (PRESETS[key]) packetIn.value = PRESETS[key];
        document.querySelectorAll('[data-preset]').forEach(function (b) { b.classList.remove('on'); });
        btn.classList.add('on');
        renderPacket();
      });
    });

    // 默认来一段普通的
    packetIn.value = PRESETS.plain;
    document.querySelectorAll('[data-preset="plain"]').forEach(function (b) { b.classList.add('on'); });
    renderPacket();
  }
})();
