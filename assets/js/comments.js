/* ============================================================
   JNSEC · 竹隐 —— 评论区（茶寮）
   两套皮：
     mode = "local"   本机留痕，只存在自己浏览器里（默认，方便本地预览）
     mode = "giscus"  接 GitHub Discussions，所有人共享（上线后切这个）
   切换只需改 assets/js/site-config.js 里的一个字段。
   ============================================================ */
(function () {
  'use strict';

  var mount = document.getElementById('comments-mount');
  if (!mount) return;

  var CFG = (window.JNSEC_CONFIG && window.JNSEC_CONFIG.comments) || { mode: 'local' };
  var PAGE_KEY = 'jnsec.memo:' + location.pathname.replace(/\/+$/, '/');
  var NICK_KEY = 'jnsec.nick';

  /* ---------------- giscus ---------------- */
  var bootGiscus = function (g) {
    if (!g || !g.repo || !g.repoId) {
      mount.innerHTML = '<div class="comments-boot">配置里 <code>mode</code> 写的是 giscus，但 <code>repo</code> / <code>repoId</code> 还空着。' +
        '去 <code>giscus.app</code> 填好仓库拿到那四个值，抄进 <code>assets/js/site-config.js</code> 就行。</div>';
      return;
    }
    mount.innerHTML = '';
    var s = document.createElement('script');
    s.src = 'https://giscus.app/client.js';
    s.async = true;
    s.crossOrigin = 'anonymous';
    s.setAttribute('data-repo', g.repo);
    s.setAttribute('data-repo-id', g.repoId);
    s.setAttribute('data-category', g.category || 'General');
    s.setAttribute('data-category-id', g.categoryId || '');
    s.setAttribute('data-mapping', g.mapping || 'pathname');
    s.setAttribute('data-strict', g.strict || '0');
    s.setAttribute('data-reactions-enabled', '1');
    s.setAttribute('data-emit-metadata', '0');
    s.setAttribute('data-input-position', 'top');
    s.setAttribute('data-theme', g.theme || 'light');
    s.setAttribute('data-lang', g.lang || 'zh-CN');
    s.setAttribute('data-loading', 'lazy');
    mount.appendChild(s);
  };

  /* ---------------- 本机留痕 ---------------- */
  var readMemos = function () {
    try {
      var arr = JSON.parse(localStorage.getItem(PAGE_KEY) || '[]');
      return Array.isArray(arr) ? arr : [];
    } catch (e) { return []; }
  };
  var writeMemos = function (arr) {
    try { localStorage.setItem(PAGE_KEY, JSON.stringify(arr)); return true; }
    catch (e) { return false; }
  };

  var fmtTime = function (ts) {
    var d = new Date(ts);
    var p = function (n) { return n < 10 ? '0' + n : String(n); };
    return d.getFullYear() + '-' + p(d.getMonth() + 1) + '-' + p(d.getDate()) + ' ' + p(d.getHours()) + ':' + p(d.getMinutes());
  };

  var bootLocal = function () {
    mount.innerHTML =
      '<div class="comments-boot" style="margin-bottom:1.2rem">' +
        '<b>眼下是「本机留痕」模式。</b>你写的话只存在这台电脑的浏览器里，刷新还在，换台机器就没了，同门看不到。' +
        '等站子挂上 GitHub Pages、在 <code>site-config.js</code> 里填好 giscus 的四个参数，这里就会变成所有人共用的茶寮。' +
      '</div>' +
      '<form class="memo-form" novalidate>' +
        '<div class="memo-row">' +
          '<input type="text" name="nick" maxlength="16" placeholder="署名（随便起）" autocomplete="nickname">' +
          '<input type="text" name="text" maxlength="120" placeholder="写完敲回车就发出去">' +
        '</div>' +
        '<textarea name="body" rows="3" maxlength="800" placeholder="说点什么：没听懂的地方、踩到的坑、补充的思路……"></textarea>' +
        '<div class="memo-actions">' +
          '<span class="memo-hint">Ctrl / ⌘ + Enter 也能发 · 存在浏览器 localStorage</span>' +
          '<span><button type="button" class="btn ghost" data-clear>清空本页</button> <button type="submit" class="btn">投笺</button></span>' +
        '</div>' +
      '</form>' +
      '<ul class="memo-list"></ul>';

    var form = mount.querySelector('form');
    var list = mount.querySelector('.memo-list');
    var nickIn = form.nick;
    var bodyIn = form.body;

    nickIn.value = (function () { try { return localStorage.getItem(NICK_KEY) || ''; } catch (e) { return ''; } })();

    var draw = function () {
      var memos = readMemos();
      if (!memos.length) {
        list.innerHTML = '<li class="memo-empty">还没有人说话。第一盏茶，你来倒。</li>';
        return;
      }
      list.innerHTML = '';
      memos.slice().reverse().forEach(function (m) {
        var li = document.createElement('li');
        li.className = 'memo';

        var top = document.createElement('div');
        top.className = 'memo-top';
        var name = document.createElement('span');
        name.className = 'memo-name';
        name.textContent = m.nick || '过路人';
        var time = document.createElement('span');
        time.className = 'memo-time';
        time.textContent = fmtTime(m.at);
        top.appendChild(name);
        top.appendChild(time);

        var body = document.createElement('div');
        body.className = 'memo-body';
        body.textContent = m.text;   // 一律按纯文本，不让谁把标签塞进来

        var del = document.createElement('button');
        del.type = 'button';
        del.className = 'memo-del';
        del.textContent = '抹去';
        del.addEventListener('click', function () {
          var next = readMemos().filter(function (x) { return x.id !== m.id; });
          writeMemos(next);
          draw();
        });

        li.appendChild(del);
        li.appendChild(top);
        li.appendChild(body);
        list.appendChild(li);
      });
    };

    var submit = function () {
      var text = bodyIn.value.trim();
      if (!text) { bodyIn.focus(); return; }
      var nick = nickIn.value.trim() || '过路人';
      try { localStorage.setItem(NICK_KEY, nickIn.value.trim()); } catch (e) {}

      var memos = readMemos();
      memos.push({ id: 'm' + Date.now() + Math.random().toString(36).slice(2, 6), nick: nick, text: text, at: Date.now() });
      if (!writeMemos(memos)) {
        alert('浏览器不让写本地存储（可能是隐私模式），这条留不下来。');
        return;
      }
      bodyIn.value = '';
      draw();
      list.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      submit();
    });
    nickIn.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); bodyIn.focus(); } });
    bodyIn.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) { e.preventDefault(); submit(); }
    });

    mount.querySelector('[data-clear]').addEventListener('click', function () {
      if (!readMemos().length) return;
      if (confirm('把这一页所有留言从本机删掉？删了找不回来。')) {
        writeMemos([]);
        draw();
      }
    });

    draw();
  };

  /* ---------------- 启动 ---------------- */
  if (CFG.mode === 'giscus') bootGiscus(CFG.giscus);
  else bootLocal();
})();
