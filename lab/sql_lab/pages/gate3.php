<?php
include __DIR__ . '/../common.php';
include __DIR__ . '/../db.php';
page_top('第三关 · 布尔盲注');

$id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '1';

// 只查一个字段，页面也只回一句"存在/不存在"
$sql = "SELECT id FROM users WHERE id = $id";

$pdo = db();
$ok = false;
try {
    $row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    $ok = !empty($row);
} catch (Exception $e) {
    // 报错也吞掉，什么都不给 —— 这就是"盲"
    $ok = false;
}

echo '<p>这一关的页面只会告诉你一件事：查得到，还是查不到。</p>';
echo '<pre><code>SELECT id FROM users WHERE id = ' . htmlspecialchars($id) . '</code></pre>';

if ($ok) {
    echo '<div class="flag" style="background:#e8f0e0;color:#2c4234;border:1px solid #c9d6bd">用户存在</div>';
} else {
    echo '<div class="no">用户不存在（或者 SQL 出错了，反正你也看不出来）</div>';
}

echo '<p class="hint">目标：把 <code>secrets</code> 表里 id=2 那条的 <code>value</code> 猜出来。<br>
把它拆成字符，一个一个用 <code>unicode(substr(...))</code> 去问 ——
SQLite 没有 <code>ascii()</code>，用 <code>unicode()</code> 顶替，作用一样。<br>
脚本在第三讲「七 · 盲注」那一节，照抄就行，改个地址。</p>';

page_bottom();
