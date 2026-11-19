<?php
include __DIR__ . '/../common.php';
page_top('第一关 · 改一个属性');

class Welcome {
    public $username = 'guest';
    public $action = 'none';

    public function __destruct() {
        if ($this->action === 'open') {
            echo '<div class="flag">';
            echo htmlspecialchars(@file_get_contents(__DIR__ . '/../flag1.txt'));
            echo '</div>';
        }
    }
}

echo '<p>第一关的源码：</p>';
show_code('class Welcome {
    public $username = "guest";
    public $action   = "none";

    public function __destruct() {
        if ($this->action === "open") {
            echo file_get_contents("flag1.txt");
        }
    }
}

unserialize($_GET["data"]);');

$data = is_string($_GET['data'] ?? null) ? $_GET['data'] : '';

echo '<form method="get" action="/gate1">
  <input type="text" name="data" placeholder="把序列化字符串粘在这儿" value="'
  . htmlspecialchars($data, ENT_QUOTES) . '">
  <button type="submit">提交</button>
</form>';

if ($data !== '') {
    $obj = safe_unserialize($data);
    if ($obj instanceof Welcome) {
        // 主动销毁，让 __destruct 当场执行。不然会等到脚本结束才输出，
        // flag 就跑到页面最末尾去了
        unset($obj);
    } else {
        echo '<div class="no">反序列化没成 —— 要么格式不对，要么长度写错了。</div>';
    }
}

echo '<p class="hint">提示：你要的东西在 <code>action</code> 属性里。<br>
序列化格式是 <code>O:类名长度:"类名":属性个数:{属性...}</code>。<br>
先想清楚 <code>Welcome</code> 有几个属性、每个属性名多长。</p>';

page_bottom();
