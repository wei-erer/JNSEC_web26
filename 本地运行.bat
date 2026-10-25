@echo off
pushd "%~dp0"

where python >nul 2>nul
if errorlevel 1 (
  echo.
  echo   Python not found.
  echo.
  echo   Install it from https://www.python.org/downloads/
  echo   Remember to tick "Add Python to PATH" during setup.
  echo.
  pause
  popd
  exit /b 1
)

python run.py
if errorlevel 1 pause
popd