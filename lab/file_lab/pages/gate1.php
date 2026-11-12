<?php
include __DIR__ . '/../common.php';
page_top('第一关 · 无过滤包含');

$f = is_string($_GET['f'] ?? null) ? $_GET['f'] : '';

echo '<p>后端就这两行：</p>';
show_code('$file = $_GET["f"];
include $file;   // 没有任何检查');

if ($f !== '') {
    echo '<p class="hint">实际包含了：<code>' . htmlspecialchars($f) . '</code></p>';
    echo '<div class="flag">';
    // 下面这行就是漏洞本身
    include $f;
    echo '</div>';
} else {
    echo '<div class="no">传个 <code>?f=</code> 参数试试。</div>';
}

echo '<p class="hint">目标：读出 <code>secret.txt</code>。'
   . '它就在本靶场的根目录下，和 router.php 同级。<br>'
   . '猜出来之后，也可以试试包含一个 <code>.php</code> 文件 —— '
   . '你会发现它被执行了，页面上看不到源码。</p>';

page_bottom();
