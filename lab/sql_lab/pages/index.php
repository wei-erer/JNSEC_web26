<?php
include __DIR__ . '/../common.php';
page_top('三道门');
?>

<p>三关，对应第三讲的三种主要手法。用的是 SQLite —— 零配置，PHP 自带。</p>

<a class="gate" href="/gate1?id=1">
  <b>其一 · 数字型注入</b>
  <span>id 直接拼进 SQL，没有引号。页面会把查询结果列出来。</span>
</a>

<a class="gate" href="/gate2">
  <b>其二 · 字符型登录框</b>
  <span>经典的万能密码。密码那一栏你能随便写。</span>
</a>

<a class="gate" href="/gate3?id=1">
  <b>其三 · 布尔盲注</b>
  <span>页面只说"存在"或"不存在"，别的什么都不给。得靠脚本猜。</span>
</a>

<p class="hint" style="margin-top:2rem">
  靶场用 SQLite，所以查表名要问 <code>sqlite_master</code> 而不是
  <code>information_schema</code>。原理一样，只是元数据表的名字不同。
</p>

<?php page_bottom(); ?>
