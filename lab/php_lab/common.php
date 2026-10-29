<?php
/**
 * 竹隐 PHP 靶场 · 公共部分
 * 四关，对应第二讲的四个知识点：0e、数组、科学计数法、变量覆盖。
 *
 * 这个靶场是故意写漏的，只用来练习。
 */

function page_top($title)
{
    ?>
<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>竹隐 PHP 靶场 · <?= htmlspecialchars($title) ?></title>
<style>
  :root {
    --paper:#f5f0e4; --ink:#1e2a23; --soft:#6d7a6f;
    --bamboo:#4f6f52; --deep:#2c4234; --cinnabar:#a83a2c;
  }
  * { box-sizing:border-box; }
  body {
    margin:0; min-height:100vh; padding:clamp(1.4rem,6vw,3.5rem) 1.2rem 4rem;
    background:var(--paper);
    background-image:
      radial-gradient(900px 600px at 10% -10%, rgba(255,255,255,.8), transparent 60%),
      radial-gradient(900px 700px at 100% 100%, rgba(79,111,82,.10), transparent 60%);
    color:var(--ink); line-height:1.9; font-size:16px;
    font-family:"PingFang SC","Microsoft YaHei",system-ui,sans-serif;
  }
  .wrap { max-width:680px; margin:0 auto; }
  .head { display:flex; align-items:center; gap:.7rem; margin-bottom:2rem; }
  .seal {
    display:inline-grid; place-items:center; width:2rem; height:2rem;
    background:var(--cinnabar); color:#f8f2e6; border-radius:2px;
    font-family:KaiTi,STKaiti,serif; font-size:1.1rem; transform:rotate(-2deg);
  }
  .head b { font-family:KaiTi,STKaiti,serif; font-size:1.1rem; letter-spacing:.14em; }
  h1 {
    font-family:"Songti SC",SimSun,serif; font-size:1.45rem; font-weight:600;
    letter-spacing:.03em; margin:0 0 1.2rem;
  }
  p { margin:0 0 1rem; }
  code {
    font-family:Consolas,monospace; font-size:.88em;
    background:rgba(79,111,82,.10); padding:.1em .38em; border-radius:2px;
    color:var(--deep);
  }
  pre {
    background:#222d26; color:#dfe6d8; padding:.9rem 1rem; border-radius:3px;
    overflow-x:auto; font-size:.83rem; line-height:1.75;
  }
  pre code { background:none; color:inherit; padding:0; }
  a { color:var(--bamboo); text-underline-offset:3px; }
  .gate {
    display:block; padding:.9rem 1.1rem; margin-bottom:.7rem;
    border:1px solid rgba(79,111,82,.25); border-radius:3px;
    background:rgba(255,255,255,.5); text-decoration:none; color:inherit;
  }
  .gate:hover { border-color:rgba(79,111,82,.5); background:#fff; }
  .gate b { font-family:"Songti SC",SimSun,serif; }
  .gate span { display:block; color:var(--soft); font-size:.85rem; }
  .flag {
    margin:1.2rem 0; padding:1rem 1.2rem; border-radius:3px;
    background:#222d26; color:#b7e0a5;
    font-family:Consolas,monospace; font-size:.95rem; word-break:break-all;
  }
  .no {
    margin:1.2rem 0; padding:.8rem 1rem;
    border-left:2px solid var(--cinnabar); background:rgba(168,58,44,.06);
  }
  .hint { color:var(--soft); font-size:.84rem; }
  .back { display:inline-block; margin-top:1.8rem; color:var(--soft); font-size:.85rem; }
  .tip { color:var(--soft); font-size:.8rem; margin-top:1.4rem; }
</style>
</head>
<body><div class="wrap">
  <div class="head"><span class="seal">竹</span><b>竹隐 PHP 靶场</b></div>
  <h1><?= htmlspecialchars($title) ?></h1>
    <?php
}

function page_bottom()
{
    ?>
  <a class="back" href="/">← 回到门口</a>
  <p class="tip">这个靶场是故意写漏的，只用来练习。别拿它的写法去写线上服务。</p>
</div></body></html>
    <?php
}
