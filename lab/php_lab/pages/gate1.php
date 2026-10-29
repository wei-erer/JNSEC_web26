<?php
include __DIR__ . '/../common.php';
include __DIR__ . '/../flag.php';
page_top('第一关 · 弱比较');

// 只收字符串，数组是第二关的事
$a = is_string($_GET['a'] ?? null) ? $_GET['a'] : '';
$b = is_string($_GET['b'] ?? null) ? $_GET['b'] : '';

echo '<p>传两个参数 <code>a</code> 和 <code>b</code>。判定条件是：</p>';
echo '<pre><code>$a !== $b &amp;&amp; md5($a) == md5($b)</code></pre>';
echo '<p>两个值<b>必须不一样</b>，但 md5 之后要<b>弱相等</b>。</p>';

$passed = ($a !== '' && $b !== '' && $a !== $b && md5($a) == md5($b));

if ($passed) {
    echo '<div class="flag">' . htmlspecialchars($flags[1]) . '</div>';
} elseif ($a !== '' || $b !== '') {
    echo '<div class="no">还没过。<br>';
    echo 'a 的 md5：<code>' . ($a !== '' ? md5($a) : '（没传）') . '</code><br>';
    echo 'b 的 md5：<code>' . ($b !== '' ? md5($b) : '（没传）') . '</code></div>';
}

echo '<p class="hint">提示：去正文里找那批 md5 长得像 0e 开头的字符串。'
   . '两个都是 0e 的话，弱比较时都当 0 处理。</p>';

page_bottom();
