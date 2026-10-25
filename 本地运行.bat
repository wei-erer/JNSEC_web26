@echo off
chcp 65001 >nul
title 竹隐 · 本地运行
cd /d "%~dp0"

where python >nul 2>nul
if errorlevel 1 (
  echo.
  echo   没找到 python。
  echo.
  echo   去 https://www.python.org/downloads/ 装一个，
  echo   安装时记得勾上「Add Python to PATH」。
  echo.
  pause
  exit /b 1
)

echo.
echo   网站   http://127.0.0.1:8000
echo   靶场   http://127.0.0.1:8081
echo.
echo   等一下会弹出两个黑窗口，那是在跑服务，别关。
echo   要停的时候，把两个黑窗口关掉就行。
echo.

start "竹隐 · 网站" /d "%~dp0" cmd /k "python -m http.server 8000 --bind 127.0.0.1"
start "竹隐 · 靶场" /d "%~dp0lab" cmd /k "python zhulin_lab.py"

timeout /t 2 >nul
start http://127.0.0.1:8000/

exit /b 0
