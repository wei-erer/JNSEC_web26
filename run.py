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
import socket
import subprocess
import sys
import time
import webbrowser

ROOT = os.path.dirname(os.path.abspath(__file__))
LAB_DIR = os.path.join(ROOT, "lab")
LAB_FILE = os.path.join(LAB_DIR, "zhulin_lab.py")

WEB_PORT = 8000
LAB_PORT = 8081


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

    time.sleep(1.6)

    ok_web = port_open(WEB_PORT)
    ok_lab = port_open(LAB_PORT)

    print("  " + "-" * 34)
    print("  网站   http://127.0.0.1:%d   %s" % (WEB_PORT, "OK" if ok_web else "启动失败"))
    print("  靶场   http://127.0.0.1:%d   %s" % (LAB_PORT, "OK" if ok_lab else "启动失败"))
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
