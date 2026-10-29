<?php
/**
 * 四关的 flag。别直接访问这个文件 —— 它没有输出，
 * 是被各关 include 进来的。
 */

$flags = [
    1 => 'flag{php_0e_magic_hash}',
    2 => 'flag{php_array_breaks_md5}',
    3 => 'flag{php_scientific_notation}',
    4 => 'flag{php_extract_overwrites}',
];
