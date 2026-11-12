<?php
include __DIR__ . '/../common.php';
page_top('第四关 · 查了 Content-Type');

$dir = __DIR__ . '/../uploads/';
if (!is_dir($dir)) {
    @mkdir($dir, 0777, true);
}

echo '<p>这一关比上一关多了一行检查：</p>';
show_code('if ($_FILES["file"]["type"] !== "image/png") {
    die("只收 png");
}
// 然后照旧存原文件名');

echo '<form method="post" enctype="multipart/form-data" action="/gate4">
  <input type="file" name="file">
  <button type="submit">上传</button>
</form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $type = $_FILES['file']['type'] ?? '';
    $name = basename($_FILES['file']['name']);

    if ($name === '') {
        echo '<div class="no">没选文件。</div>';
    } elseif ($type !== 'image/png') {
        echo '<div class="no">只收 png。你传的类型是 <code>' . htmlspecialchars($type) . '</code></div>';
        echo '<p class="hint">但这个类型是你在请求里自己写的，不是服务器验出来的。</p>';
    } elseif (@move_uploaded_file($_FILES['file']['tmp_name'], $dir . $name)) {
        echo '<div class="flag">通过检查，已保存：' . htmlspecialchars($name) . '
访问地址：<a href="/uploads/' . htmlspecialchars($name) . '" target="_blank">/uploads/'
            . htmlspecialchars($name) . '</a></div>';
    } else {
        echo '<div class="no">保存失败。</div>';
    }
}

echo '<p class="hint">目标：还是传一个 <code>.php</code> 上去。只是这次要让 <code>type</code> 看起来像 png。<br>
Burp 抓包改 <code>Content-Type: image/png</code>，或者命令行：<br>
<code>curl -F "file=@shell.php;type=image/png" http://127.0.0.1:8084/gate4</code></p>';

page_bottom();
