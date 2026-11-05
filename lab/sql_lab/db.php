<?php
/**
 * 竹隐 SQL 靶场 · 数据库初始化
 *
 * 用 SQLite，不用装 MySQL，跑起来就有一个现成的库。
 * 数据库文件在 data.sqlite，删掉它下次会自动重建。
 *
 * 这个靶场是故意写漏的，只用来练习。
 */

function db()
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $file = __DIR__ . '/data.sqlite';
    $fresh = !file_exists($file);

    $pdo = new PDO('sqlite:' . $file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($fresh) {
        // users 和 secrets 都是三列，方便 union
        $pdo->exec('CREATE TABLE users (
            id INTEGER PRIMARY KEY,
            username TEXT,
            password TEXT
        )');

        $pdo->exec('CREATE TABLE secrets (
            id INTEGER PRIMARY KEY,
            name TEXT,
            value TEXT
        )');

        $users = [
            [1, 'admin', 'adm1n_pazzword'],
            [2, 'alice', 'alice123'],
            [3, 'bob',   'bob456'],
        ];
        $st = $pdo->prepare('INSERT INTO users (id, username, password) VALUES (?, ?, ?)');
        foreach ($users as $u) {
            $st->execute($u);
        }

        $secrets = [
            [1, 'flag', 'flag{sql_union_gets_the_table}'],
            [2, 'note', 'flag{blind_guess_one_char_at_a_time}'],
        ];
        $st = $pdo->prepare('INSERT INTO secrets (id, name, value) VALUES (?, ?, ?)');
        foreach ($secrets as $s) {
            $st->execute($s);
        }
    }

    return $pdo;
}
