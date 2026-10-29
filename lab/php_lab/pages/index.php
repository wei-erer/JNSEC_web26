<?php
include __DIR__ . '/../common.php';
page_top('四道门');
?>

<p>四关，对应第二讲 PHP 那部分的四个知识点。每关都能在正文里找到对应的段落。</p>

<a class="gate" href="/gate1">
  <b>其一 · 弱比较</b>
  <span>两个不相等的值，md5 之后却弱相等。想想 0e。</span>
</a>

<a class="gate" href="/gate2">
  <b>其二 · 强比较</b>
  <span>换成 === 了，0e 不管用。但 md5 面对数组会怎样？</span>
</a>

<a class="gate" href="/gate3">
  <b>其三 · 科学计数法</b>
  <span>长度不到 4，加 1 之后要大于 1000。</span>
</a>

<a class="gate" href="/gate4">
  <b>其四 · 变量覆盖</b>
  <span>extract 把数组变成了变量。它只检查了值，没检查键。</span>
</a>

<?php page_bottom(); ?>
