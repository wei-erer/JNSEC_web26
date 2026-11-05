<?php
include __DIR__ . '/../common.php';
include __DIR__ . '/../db.php';
page_top('第二关 · 字符型登录框');

$u = is_string($_POST['username'] ?? null) ? $_POST['username'] : '';
$p = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

echo '<p>一个再普通不过的登录框。它背后是这么一句：</p>';
echo '<pre><code>SELECT * FROM users WHERE username = \'$username\' AND password = \'$password\'</code></pre>';

echo '<form method="post" action="/gate2">
  <input type="text" name="username" placeholder="用户名" value="' . htmlspecialchars($u) . '">
  <input type="text" name="password" placeholder="密码">
  <button type="submit">登录</button>
</form>';

if ($u !== '') {
    $sql = "SELECT * FROM users WHERE username = '$u' AND password = '$p'";

    echo '<p class="hint">实际执行：</p>';
    echo '<pre><code class="sql">' . htmlspecialchars($sql) . '</code></pre>';

    $pdo = db();
    try {
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo '<div class="no">SQL 出错：<code>' . htmlspecialchars($e->getMessage()) . '</code></div>';
        page_bottom();
        exit;
    }

    if (!empty($rows)) {
        echo '<div class="flag">登录成功（' . count($rows) . ' 条记录匹配）<br>'
           . 'flag{sql_login_bypass}</div>';
        echo '<p class="hint">想想：为什么一行密码都不对，也能登录成功？'
           . '把上面那句实际执行的 SQL 读一遍就明白了。</p>';
    } else {
        echo '<div class="no">登录失败。</div>';
    }
}

echo '<p class="hint">目标：不知道任何密码，登进 admin 的账号。</p>';

page_bottom();
