<?php
include __DIR__ . '/../common.php';
page_top('四道门');
?>

<p>两关文件包含，两关文件上传。前者负责读，后者负责写。</p>

<a class="gate" href="/gate1?f=secret.txt">
  <b>其一 · 无过滤包含</b>
  <span>直接把参数塞进 include。想想 secret.txt 在哪。</span>
</a>

<a class="gate" href="/gate2?f=welcome.txt">
  <b>其二 · 有前缀的包含</b>
  <span>路径被拼在一段前缀后面，而且 ../ 被过滤掉了。过滤器只删一次。</span>
</a>

<a class="gate" href="/gate3">
  <b>其三 · 无过滤上传</b>
  <span>什么都不查。传个一句话上去，再访问它。</span>
</a>

<a class="gate" href="/gate4">
  <b>其四 · 查了 Content-Type</b>
  <span>要求 image/png。想想这个值是谁写在请求里的。</span>
</a>

<p class="hint" style="margin-top:2rem">
  上传的文件都落在 <code>uploads/</code> 目录。<br>
  包含关卡的目标是读出 <code>secret.txt</code>。
</p>

<?php page_bottom(); ?>
