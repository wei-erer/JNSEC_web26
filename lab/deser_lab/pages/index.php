<?php
include __DIR__ . '/../common.php';
page_top('三道门');
?>

<p>三关，一关比一关绕。目标都是让页面吐出 <code>flag</code>。</p>

<a class="gate" href="/gate1">
  <b>其一 · 改一个属性</b>
  <span>有个类，<code>__destruct</code> 里干了件危险的事。把属性改成你要的值。</span>
</a>

<a class="gate" href="/gate2">
  <b>其二 · 把两个类串起来</b>
  <span>起点和终点不在同一个类里。中间得塞一层，让调用能走过去。</span>
</a>

<a class="gate" href="/gate3">
  <b>其三 · 字符串逃逸</b>
  <span>输入会被过滤，长度对不上。利用那个长度字段，硬造出一个新属性。</span>
</a>

<p class="hint" style="margin-top:2rem">
  建议先写个 PHP 脚本，把对象 <code>serialize()</code> 出来再改，
  别手写整串 —— 长度算错一次就白搭。<br>
  每关页面上都印着源码，照着类结构画一画再动手。
</p>

<?php page_bottom(); ?>
