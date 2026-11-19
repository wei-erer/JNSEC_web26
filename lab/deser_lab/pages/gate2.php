<?php
include __DIR__ . '/../common.php';
page_top('第二关 · 把两个类串起来');

class Starter {
    public $next;
    public function __destruct() {
        if (is_object($this->next)) {
            $this->next->run();          // 起点：把 next 的方法调起来
        }
    }
}

class Middle {
    public $next;
    public function run() {
        if (is_object($this->next)) {
            $this->next->exec();         // 中转
        }
    }
}

class End {
    public $cmd;
    public function exec() {
        if ($this->cmd === 'flag') {     // 终点
            echo '<div class="flag">';
            echo htmlspecialchars(@file_get_contents(__DIR__ . '/../flag2.txt'));
            echo '</div>';
        }
    }
}

echo '<p>三个类，起承转合：</p>';
show_code('class Starter {
    public $next;
    public function __destruct() { $this->next->run(); }
}
class Middle {
    public $next;
    public function run() { $this->next->exec(); }
}
class End {
    public $cmd;
    public function exec() { if ($this->cmd === "flag") { echo file_get_contents("flag2.txt"); } }
}

unserialize($_GET["data"]);');

$data = is_string($_GET['data'] ?? null) ? $_GET['data'] : '';

echo '<form method="get" action="/gate2">
  <input type="text" name="data" placeholder="序列化字符串" value="'
  . htmlspecialchars($data, ENT_QUOTES) . '">
  <button type="submit">提交</button>
</form>';

if ($data !== '') {
    $obj = safe_unserialize($data);
    if ($obj instanceof Starter) {
        unset($obj);       // 触发 __destruct，让它顺着链子走下去
    } else {
        echo '<div class="no">反序列化没成。检查格式和长度。</div>';
    }
}

echo '<p class="hint">要点：<code>next</code> 这个属性，<strong>可以是一个对象</strong>。<br>
序列化里放对象，就是 <code>s:4:"next";O:6:"Middle":1:{...}</code> 这样接着写。<br>
终点要的是 <code>End</code> 的 <code>cmd</code> 等于 <code>flag</code>。</p>';

page_bottom();
