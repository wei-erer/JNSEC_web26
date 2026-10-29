<?php
include __DIR__ . '/../common.php';
include __DIR__ . '/../flag.php';
page_top('第三关 · 科学计数法');

$a = is_string($_GET['a'] ?? null) ? $_GET['a'] : '';

echo '<p>传一个参数 <code>a</code>，同时满足：</p>';
echo '<pre><code>strlen($a) &lt; 4
($a + 1) &gt; 1000</code></pre>';
echo '<p>长度不到 4 个字符，加 1 之后却要大于 1000。</p>';

$len = strlen($a);
$plus = $a === '' ? 0 : $a + 1;

if ($a !== '') {
    echo '<p class="hint">你传的是 <code>' . htmlspecialchars($a) . '</code>，'
       . '长度 ' . $len . '，加 1 之后算出来是 <code>' . htmlspecialchars((string)$plus) . '</code>。</p>';
}

if ($a !== '' && strlen($a) < 4 && ($a + 1) > 1000) {
    echo '<div class="flag">' . htmlspecialchars($flags[3]) . '</div>';
} else {
    echo '<div class="no">还没过。</div>';
}

echo '<p class="hint">提示：纯数字做不到（999 加 1 才 1000，不满足"大于"）。'
   . '但 PHP 认科学计数法 —— 想想 <code>1e3</code> 是多少。</p>';

page_bottom();
