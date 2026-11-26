@echo off
rem Runs appy.ps1 without needing to change the PowerShell execution policy.
rem Usage: appy.cmd [start|stop|update|status|logs [service]]
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0appy.ps1" %*
