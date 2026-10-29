<?php
include __DIR__ . '/../common.php';
include __DIR__ . '/../flag.php';
page_top('第四关 · 变量覆盖');

// 危险的一行：把 $_GET 里的键值对，变成当前作用域的变量
extract($_GET);

// 只检查了值，没检查键
foreach ($_GET as $key => $val) {
    if (is_string($val) && preg_match('/[a-zA-Z0-9]/', $val)) {
        echo '<div class="no">值里出现了字母或数字：<code>' . htmlspecialchars($val) . '</code>，出局。</div>';
        page_bottom();
        exit;
    }
}

echo '<p>这份代码做了两件事：</p>';
echo '<pre><code>extract($_GET);          // 键变成变量名，值变成变量的值

foreach ($_GET as $val) {
    if (preg_match("/[a-zA-Z0-9]/", $val)) die("error");
}                        // 只检查了值

if ($cmd) { echo $flag; }</code></pre>';

echo '<p>要求 <code>$cmd</code> 非空，但值里不能有字母和数字。</p>';

if (!empty($cmd)) {
    echo '<div class="flag">' . htmlspecialchars($flags[4]) . '</div>';
} else {
    echo '<div class="no">还没过。现在 <code>$cmd</code> '
       . (isset($cmd) ? '是空的' : '还不存在') . '。</div>';
}

echo '<p class="hint">提示：既然只查值不查键，那你传一个"值是符号"的参数试试 —— '
   . '它的名字会自动变成一个变量。</p>';

page_bottom();
