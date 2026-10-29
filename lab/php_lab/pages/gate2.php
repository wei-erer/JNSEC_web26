<?php
include __DIR__ . '/../common.php';
include __DIR__ . '/../flag.php';
page_top('第二关 · 强比较');

$a = $_GET['a'] ?? '';
$b = $_GET['b'] ?? '';

/**
 * 这一关的判定用的是 ===。
 *
 * 注意：PHP 8 里给 md5() 传数组会直接抛 TypeError，
 * 而 PHP 7 里它只是报个 warning、返回 null。
 * 为了让两种版本下的行为一致（也为了守住这个知识点），
 * 这里把"不是字符串"的情况按 null 处理 ——
 * 这正是 PHP 7 里的真实表现。
 */
$a_hash = is_array($a) ? null : md5((string)$a);
$b_hash = is_array($b) ? null : md5((string)$b);

echo '<p>这一关把 <code>==</code> 换成了 <code>===</code>：</p>';
echo '<pre><code>$a !== $b &amp;&amp; md5($a) === md5($b)</code></pre>';
echo '<p>0e 那招在这里不管用了 —— 严格比较要求类型也得一样。</p>';

if ($a !== $b && $a_hash === $b_hash) {
    echo '<div class="flag">' . htmlspecialchars($flags[2]) . '</div>';
} else {
    echo '<div class="no">还没过。</div>';
}

echo '<p class="hint">提示：md5() 只吃字符串。那如果传进去的是个<b>数组</b>呢？'
   . '两个都拿到一样的东西，严格比较自然就过了。想想 URL 上怎么写数组。</p>';

page_bottom();
