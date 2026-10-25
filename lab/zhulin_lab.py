#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
竹隐靶场 · Web 方向第一讲配套
────────────────────────────────────────────────
只依赖 Python 标准库，不用 Docker，不用装任何东西。

跑起来（先 cd 到这个文件所在的文件夹）：
    python zhulin_lab.py

文件名必须叫 zhulin_lab.py，后缀是 .py。Windows 记事本默认会存成 .py.txt。

然后浏览器开 http://127.0.0.1:8081

四道门，四样毛病：
    /gate/1      信了 X-Forwarded-For
    /gate/2      信了 Host
    /gate/3      信了 Cookie
    /download    没做路径规范化

这个靶场是故意写漏的，只为练习。别拿它的写法去写线上服务。
"""

import os
import sys
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from urllib.parse import urlparse, parse_qs
from html import escape

HOST = "127.0.0.1"
PORT = 8081

BASE = os.path.dirname(os.path.abspath(__file__))
PUBLIC = os.path.join(BASE, "public")

FLAGS = {
    1: "flag{zhulin_xff_is_just_a_header}",
    2: "flag{zhulin_host_you_can_say_anything}",
    3: "flag{zhulin_cookie_is_your_id}",
    4: "flag{zhulin_dot_dot_slash}",
}

# ──────────────────────────────────────────────
# 页面外壳
# ──────────────────────────────────────────────
SHELL = """<!doctype html>
<html lang="zh-CN"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>竹隐靶场 · {title}</title>
<style>
  :root {{
    --paper:#f5f0e4; --ink:#1e2a23; --soft:#6d7a6f;
    --bamboo:#4f6f52; --deep:#2c4234; --cinnabar:#a83a2c;
  }}
  * {{ box-sizing:border-box; }}
  body {{
    margin:0; min-height:100vh; padding:clamp(1.4rem,6vw,4rem) 1.2rem 4rem;
    background:var(--paper);
    background-image:
      radial-gradient(900px 600px at 10% -10%, rgba(255,255,255,.8), transparent 60%),
      radial-gradient(900px 700px at 100% 100%, rgba(79,111,82,.10), transparent 60%);
    color:var(--ink); font-family:"PingFang SC","Microsoft YaHei",system-ui,sans-serif;
    line-height:1.9; font-size:16px;
  }}
  .wrap {{ max-width:640px; margin:0 auto; }}
  .seal {{
    display:inline-grid; place-items:center; width:2rem; height:2rem;
    background:var(--cinnabar); color:#f8f2e6; border-radius:2px;
    font-family:KaiTi,STKaiti,serif; font-size:1.1rem; transform:rotate(-2deg);
  }}
  header {{ display:flex; align-items:center; gap:.7rem; margin-bottom:.4rem; }}
  header b {{ font-family:KaiTi,STKaiti,serif; font-size:1.15rem; letter-spacing:.16em; }}
  .sub {{ color:var(--soft); font-size:.8rem; letter-spacing:.2em; margin:0 0 2rem 2.7rem; }}
  h1 {{
    font-family:Songti SC,SimSun,serif; font-size:1.5rem; font-weight:600;
    letter-spacing:.04em; margin:0 0 1rem;
  }}
  p {{ margin:0 0 1rem; }}
  code {{
    font-family:Consolas,monospace; font-size:.88em;
    background:rgba(79,111,82,.10); padding:.1em .38em; border-radius:2px;
    color:var(--deep);
  }}
  a {{ color:var(--bamboo); text-underline-offset:3px; }}
  .gate {{
    display:block; padding:.9rem 1.1rem; margin-bottom:.7rem;
    border:1px solid rgba(79,111,82,.25); border-radius:3px;
    background:rgba(255,255,255,.5); text-decoration:none; color:inherit;
  }}
  .gate:hover {{ border-color:rgba(79,111,82,.5); background:#fff; }}
  .gate b {{ font-family:Songti SC,SimSun,serif; }}
  .gate span {{ display:block; color:var(--soft); font-size:.85rem; }}
  .flag {{
    margin:1.4rem 0; padding:1rem 1.2rem;
    background:#222d26; color:#b7e0a5; border-radius:3px;
    font-family:Consolas,monospace; font-size:.95rem; word-break:break-all;
  }}
  .no {{
    margin:1.4rem 0; padding:.9rem 1.1rem;
    border-left:2px solid var(--cinnabar);
    background:rgba(168,58,44,.06);
  }}
  .back {{ display:inline-block; margin-top:1.6rem; color:var(--soft); font-size:.85rem; }}
  .tip {{ color:var(--soft); font-size:.82rem; margin-top:2rem; }}
</style></head>
<body><div class="wrap">
  <header><span class="seal">竹</span><b>竹隐靶场</b></header>
  <p class="sub">第一次例会 · 自己打自己</p>
  {body}
  <a class="back" href="/">← 回到门口</a>
  <p class="tip">这个靶场是故意写漏的，只用来练习。别拿它的写法去写线上服务。</p>
</div></body></html>"""


def page(title, body):
    return SHELL.format(title=escape(title), body=body).encode("utf-8")


HOME = """
<h1>四道门</h1>
<p>每道门后面有一段 flag。四道门，四样毛病，都在第一次例会讲过。</p>
<a class="gate" href="/gate/1"><b>其一 · 门牌</b><span>它说"只给本机看"。可它怎么知道你是本机？</span></a>
<a class="gate" href="/gate/2"><b>其二 · 内宅</b><span>它认门牌号。而门牌号，是你自己写的。</span></a>
<a class="gate" href="/gate/3"><b>其三 · 腰牌</b><span>它看你的腰牌。腰牌上的字，也是你自己写的。</span></a>
<a class="gate" href="/download?file=notice.txt"><b>其四 · 后门</b><span>它帮你从柜子里拿文件。柜子外面还有一层吗？</span></a>
"""


class Handler(BaseHTTPRequestHandler):
    # 关掉默认的日志格式，换成一行中文的
    def log_message(self, fmt, *args):
        sys.stdout.write("  %s  %s\n" % (self.address_string(), fmt % args))

    # ---------- 回复工具 ----------
    def reply(self, code, body_html, extra_headers=None):
        if isinstance(body_html, str):
            body = page("结果", body_html)
        else:
            body = body_html
        self.send_response(code)
        self.send_header("Content-Type", "text/html; charset=utf-8")
        self.send_header("Content-Length", str(len(body)))
        self.send_header("Server", "ZhulinLab/0.1 (Python)")
        for k, v in (extra_headers or []):
            self.send_header(k, v)
        self.end_headers()
        self.wfile.write(body)

    def flag_block(self, n):
        return '<h1>门开了</h1><p>这一关的 flag 收好：</p><div class="flag">%s</div>' % escape(FLAGS[n])

    def deny(self, why):
        return '<h1>不让进</h1><div class="no">%s</div>' % why

    def not_found(self):
        return '<h1>404</h1><p>没有这个东西。</p>'

    # ---------- 解析 cookie ----------
    def cookies(self):
        jar = {}
        raw = self.headers.get("Cookie", "")
        for piece in raw.split(";"):
            piece = piece.strip()
            if not piece or "=" not in piece:
                continue
            k, v = piece.split("=", 1)
            jar[k.strip()] = v.strip()
        return jar

    # ---------- 路由 ----------
    def do_GET(self):
        parsed = urlparse(self.path)
        path = parsed.path
        query = parse_qs(parsed.query)

        if path in ("/", "/index.html"):
            return self.reply(200, HOME)

        if path == "/favicon.ico":
            self.send_response(204)
            self.end_headers()
            return

        # ── 第一关：只认 X-Forwarded-For 的第一个值 ──
        if path == "/gate/1":
            xff = self.headers.get("X-Forwarded-For", "")
            first = xff.split(",")[0].strip()
            if first == "127.0.0.1":
                return self.reply(200, self.flag_block(1))
            return self.reply(403, self.deny(
                "这是内网接口，只给本机看。<br>"
                "你来自：<code>%s</code>" % escape(first or "（没说）")
            ))

        # ── 第二关：只认 Host ──
        if path == "/gate/2":
            host = self.headers.get("Host", "")
            if host.split(":")[0].lower() == "admin.internal":
                return self.reply(200, self.flag_block(2))
            return self.reply(403, self.deny(
                "内宅只认 <code>admin.internal</code> 这块门牌。<br>"
                "你报的门牌是：<code>%s</code>" % escape(host or "（没写）")
            ))

        # ── 第三关：只认 cookie 里的 role ──
        if path == "/gate/3":
            role = self.cookies().get("role", "user")
            if role == "admin":
                return self.reply(200, self.flag_block(3))
            return self.reply(200, self.deny(
                "你的身份是 <code>%s</code>，这里只有 admin 能进。<br>"
                "顺便说一句：这个身份是存在 cookie 里的。" % escape(role)
            ), extra_headers=[("Set-Cookie", "role=%s; Path=/" % escape(role))])

        # ── 第四关：路径直接拼接，没有规范化 ──
        if path == "/download":
            name = (query.get("file") or ["notice.txt"])[0]
            target = os.path.join(PUBLIC, name)          # ← 漏洞就在这一行
            try:
                with open(target, "r", encoding="utf-8") as f:
                    content = f.read()
            except OSError:
                return self.reply(404, self.not_found())
            body = (
                "<h1>文件内容</h1>"
                "<p>你取的是：<code>%s</code></p>"
                "<pre style=\"white-space:pre-wrap;background:rgba(79,111,82,.07);"
                "padding:1rem;border-radius:3px\">%s</pre>"
            ) % (escape(name), escape(content))
            return self.reply(200, body)

        return self.reply(404, self.not_found())


def ensure_files():
    """第一次跑的时候把用到的文件铺好，省得你手建。"""
    os.makedirs(PUBLIC, exist_ok=True)

    notice = os.path.join(PUBLIC, "notice.txt")
    if not os.path.exists(notice):
        with open(notice, "w", encoding="utf-8") as f:
            f.write("这是一份公开的通知，谁都能下载。\n\n"
                    "它放在 public/ 目录里，本来只该让这个目录下的文件被读到。\n"
                    "但你手上那个 file 参数，好像没人管它想去哪儿。\n")

    # 藏在 public 的上一级，正常访问碰不到
    secret = os.path.join(BASE, "flag.txt")
    if not os.path.exists(secret):
        with open(secret, "w", encoding="utf-8") as f:
            f.write(FLAGS[4] + "\n")
    # 注意：第四关读到的就是这个文件


def main():
    # Windows 控制台偶尔会因为编码直接崩，先兜一下
    try:
        sys.stdout.reconfigure(encoding="utf-8")
    except Exception:
        pass

    ensure_files()

    server = ThreadingHTTPServer((HOST, PORT), Handler)
    print("")
    print("  竹隐靶场已开 →  http://%s:%d" % (HOST, PORT))
    print("  四道门：/gate/1  /gate/2  /gate/3  /download?file=notice.txt")
    print("  停掉就按 Ctrl+C")
    print("")
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        print("\n  收工。")
        server.server_close()


if __name__ == "__main__":
    main()
