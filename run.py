#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
竹隐 · 本地一键起服务

用「本地运行.bat」调这个文件，别直接跑它也行：
    python run.py

它会做三件事：
  1. 起一个静态服务器，让你能在本地看网站（http://127.0.0.1:8000）
  2. 起第一讲配套的靶场（http://127.0.0.1:8081）
  3. 自动打开浏览器

按 Ctrl+C 退出，两个服务会一起停掉。
"""

import os
import shutil
import socket
import subprocess
import sys
import time
import webbrowser

ROOT = os.path.dirname(os.path.abspath(__file__))
LAB_DIR = os.path.join(ROOT, "lab")
LAB_FILE = os.path.join(LAB_DIR, "zhulin_lab.py")
PHP_DIR = os.path.join(LAB_DIR, "php_lab")

WEB_PORT = 8000
LAB_PORT = 8081
PHP_PORT = 8082


def find_php():
    """先找 PATH，再找 phpstudy 的常见位置。都没有就返回 None。"""
    exe = shutil.which("php")
    if exe:
        return exe

    roots = [
        r"D:\phpstudy_pro\Extensions\php",
        r"C:\phpstudy_pro\Extensions\php",
        r"D:\phpstudy\php",
        r"C:\phpstudy\php",
    ]
    for root in roots:
        if not os.path.isdir(root):
            continue
        for name in sorted(os.listdir(root), reverse=True):
            candidate = os.path.join(root, name, "php.exe")
            if os.path.isfile(candidate):
                return candidate
    return None



def port_open(port):
    """这个端口上是不是已经有东西在听了"""
    s = socket.socket()
    s.settimeout(0.4)
    try:
        return s.connect_ex(("127.0.0.1", port)) == 0
    finally:
        s.close()


def start(args, cwd):
    """在后台起一个子进程，输出丢掉（用户看浏览器就行）"""
    flags = 0
    if os.name == "nt":
        # 不弹新的控制台窗口
        flags = getattr(subprocess, "CREATE_NO_WINDOW", 0)
    return subprocess.Popen(
        args,
        cwd=cwd,
        stdout=subprocess.DEVNULL,
        stderr=subprocess.DEVNULL,
        creationflags=flags,
    )


def main():
    try:
        sys.stdout.reconfigure(encoding="utf-8")
    except Exception:
        pass

    print()
    print("  竹隐 · 本地运行")
    print("  " + "-" * 34)

    if not os.path.exists(LAB_FILE):
        print("  找不到 lab/zhulin_lab.py，这个目录不完整。")
        return 1

    procs = []

    # 网站
    if port_open(WEB_PORT):
        print("  [网站]  8000 已经有服务在跑，跳过")
    else:
        print("  [网站]  正在启动 ...")
        procs.append(start(
            [sys.executable, "-m", "http.server", str(WEB_PORT), "--bind", "127.0.0.1"],
            ROOT,
        ))

    # 靶场
    if port_open(LAB_PORT):
        print("  [靶场]  8081 已经有服务在跑，跳过")
    else:
        print("  [靶场]  正在启动 ...")
        procs.append(start([sys.executable, LAB_FILE], LAB_DIR))

    # PHP 靶场（第二讲配套，没有 PHP 就跳过）
    php = find_php()
    php_note = ""
    if php is None:
        php_note = "没找到 PHP，跳过"
        print("  [PHP ]  没找到 PHP，第二讲的靶场跳过（装个 phpstudy 或者 wsl 里的 php-cli 都可以）")
    elif port_open(PHP_PORT):
        print("  [PHP ]  8082 已经有服务在跑，跳过")
    else:
        print("  [PHP ]  正在启动 ...")
        procs.append(start([php, "-S", "127.0.0.1:%d" % PHP_PORT, "router.php"], PHP_DIR))

    time.sleep(1.6)

    ok_web = port_open(WEB_PORT)
    ok_lab = port_open(LAB_PORT)
    ok_php = port_open(PHP_PORT)

    print("  " + "-" * 34)
    print("  网站   http://127.0.0.1:%d   %s" % (WEB_PORT, "OK" if ok_web else "启动失败"))
    print("  靶场   http://127.0.0.1:%d   %s" % (LAB_PORT, "OK" if ok_lab else "启动失败"))
    if ok_php:
        print("  PHP    http://127.0.0.1:%d   OK" % PHP_PORT)
    elif php_note:
        print("  PHP    ———                  %s" % php_note)
    else:
        print("  PHP    http://127.0.0.1:%d   启动失败" % PHP_PORT)
    print()
    print("  浏览器会自动打开。要停的时候按 Ctrl+C，")
    print("  或者直接把这个窗口关掉。")
    print()

    if ok_web:
        webbrowser.open("http://127.0.0.1:%d/" % WEB_PORT)

    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("\n  收工。")
    finally:
        for p in procs:
            try:
                p.terminate()
            except Exception:
                pass
    return 0


if __name__ == "__main__":
    sys.exit(main())
