<?php
include __DIR__ . '/../common.php';
page_top('第二关 · 有前缀的包含');

$f = is_string($_GET['f'] ?? null) ? $_GET['f'] : '';

// 过滤器只把这一个子串删掉，删完就不再检查
$f = str_replace('../', '', $f);

echo '<p>后端先过滤，再拼接：</p>';
show_code('$f = str_replace("../", "", $_GET["f"]);   // 删掉 ../
include __DIR__ . "/../templates/" . $f;            // 再拼一个前缀');

echo '<p class="hint">过滤之后：<code>' . htmlspecialchars($f) . '</code></p>';

echo '<p>实际包含路径大致是：<code>pages/../templates/' . htmlspecialchars($f) . '</code></p>';

if ($f !== '') {
    echo '<div class="flag">';
    include __DIR__ . '/../templates/' . $f;
    echo '</div>';
} else {
    echo '<div class="no">传个 <code>?f=</code> 参数试试，默认那个是 <code>welcome.txt</code>。</div>';
}

echo '<p class="hint">目标还是 <code>secret.txt</code>。<br>
过滤器只删一次 —— 想想什么样的输入，删完之后正好变成 <code>../</code>。<br>
提示：四个点加两个斜杠，长得像什么？</p>';

page_bottom();
