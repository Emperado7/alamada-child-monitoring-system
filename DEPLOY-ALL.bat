@echo off
title Alamada CMS - Full Auto Deploy
color 0A
powershell -ExecutionPolicy Bypass -File "%~dp0DEPLOY-ALL.ps1"
pause
