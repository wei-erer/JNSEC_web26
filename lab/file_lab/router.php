<?php
/**
 * 竹隐文件靶场 · 路由
 *
 * 起法（在这个目录下）：
 *     php -S 127.0.0.1:8084 router.php
 *
 * 然后浏览器开 http://127.0.0.1:8084/
 *
 * 注意最后那个 return false：它把请求交还给 PHP 内置服务器，
 * 这样 uploads/ 里上传的 .php 才会被真正执行。
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

// 404 也给过，让内置服务器自己处理（静态文件、uploads 下上传的脚本）
return false;
