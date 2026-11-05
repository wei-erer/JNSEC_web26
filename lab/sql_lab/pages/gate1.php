<?php
include __DIR__ . '/../common.php';
include __DIR__ . '/../db.php';
page_top('第一关 · 数字型注入');

$id = is_string($_GET['id'] ?? null) ? $_GET['id'] : '1';

// 危险的一行：$id 直接拼进 SQL，外面没有引号
$sql = "SELECT id, username, password FROM users WHERE id = $id";

echo '<p>后端执行的语句：</p>';
echo '<pre><code>SELECT id, username, password FROM users WHERE id = ' . htmlspecialchars($id) . '</code></pre>';

$pdo = db();
try {
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo '<div class="no">SQL 出错：<code>' . htmlspecialchars($e->getMessage()) . '</code></div>';
    echo '<p class="hint">提示：先 <code>?id=1 order by 3</code> 探列数，
          <code>?id=1 order by 4</code> 会报错。这张表是 3 列。</p>';
    page_bottom();
    exit;
}

if (empty($rows)) {
    echo '<div class="no">查不到东西。</div>';
} else {
    echo '<table><thead><tr><th>id</th><th>username</th><th>password</th></tr></thead><tbody>';
    foreach ($rows as $r) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars((string)$r['id']) . '</td>';
        echo '<td>' . htmlspecialchars((string)$r['username']) . '</td>';
        echo '<td>' . htmlspecialchars((string)$r['password']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}

echo '<p class="hint">目标：把 <code>secrets</code> 表里的 flag 读出来。'
   . '不知道有哪些表？SQLite 的元数据表叫 <code>sqlite_master</code>。</p>';

page_bottom();
