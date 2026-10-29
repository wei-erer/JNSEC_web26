<?php
/**
 * 竹隐 PHP 靶场 · 路由
 *
 * 起法（在这个目录下）：
 *     php -S 127.0.0.1:8082 router.php
 *
 * 然后浏览器开 http://127.0.0.1:8082/
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');

if ($path === '') {
    include __DIR__ . '/pages/index.php';
    return true;
}

$file = __DIR__ . '/pages' . $path . '.php';

if (is_file($file)) {
    include $file;
    return true;
}

http_response_code(404);
echo '<!doctype html><meta charset="utf-8"><title>404</title>';
echo '<p style="font-family:sans-serif;padding:2rem">没有这个关卡。<a href="/">回门口</a></p>';
return true;
