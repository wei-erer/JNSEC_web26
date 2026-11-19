<?php
include __DIR__ . '/../common.php';
page_top('第三关 · 字符串逃逸');

function filter($str) {
    return str_replace('bb', 'ccc', $str);   // 两个字符换成三个，每次多一个
}

class A {
    public $name = 'admin';
    public $pass = '123';

    public function __destruct() {
        if (isset($this->hack)) {            // 目标：让它多出一个 hack 属性
            echo '<div class="flag">';
            echo htmlspecialchars(@file_get_contents(__DIR__ . '/../flag3.txt'));
            echo '</div>';
        }
    }
}

echo '<p>这一关的代码：</p>';
show_code('function filter($str) {
    return str_replace("bb", "ccc", $str);   // bb -> ccc，变长了
}

class A {
    public $name = "admin";
    public $pass = "123";
    public function __destruct() {
        if (isset($this->hack)) { echo file_get_contents("flag3.txt"); }
    }
}

$f = filter($_GET["cmd"]);
unserialize($f);');

$data = is_string($_GET['cmd'] ?? null) ? $_GET['cmd'] : '';

echo '<form method="get" action="/gate3">
  <input type="text" name="cmd" placeholder="经过 filter 处理后的字符串，最后会被反序列化" value="'
  . htmlspecialchars($data, ENT_QUOTES) . '">
  <button type="submit">提交</button>
</form>';

if ($data !== '') {
    $filtered = filter($data);
    echo '<p class="hint">过滤之后变成：</p>';
    echo '<pre><code>' . htmlspecialchars($filtered) . '</code></pre>';

    $obj = safe_unserialize($filtered);
    if ($obj instanceof A) {
        echo '<p class="hint">注意：对象已经被创建了，但 hack 属性</p>';
        unset($obj);
    } else {
        echo '<div class="no">反序列化失败了。看上面那行「过滤之后」的字符串，哪个长度对不上。</div>';
    }
}

echo '<p class="hint">
思路：<br>
1. 每替换一次，字符串<strong>多一个字符</strong>。<br>
2. 但序列化里的 <code>s:长度</code> 还是<em>原始</em>长度，解析时照这个数读。<br>
3. 想办法让「多出来的字符」正好顶到属性值结束的位置，
   让你写在值里的 <code>";s:4:"hack";s:7:"hacker!";}</code> 滑进结构里。<br>
<br>
算一下：name 填 n 组 <code>bb</code>（2n 个字符），后面接 27 个字符的 payload，
序列化出来是 <code>s:(2n+27)</code>。过滤后那部分是 <code>3n</code> 个字符。<br>
要让 <code>2n+27 = 3n</code> —— n 是多少？
</p>';

page_bottom();
