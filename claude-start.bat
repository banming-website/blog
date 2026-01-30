@echo off

:: Claude Code 启动脚本 - AI 部署版
:: 注意：请先获取有效的 API 令牌和代理地址

echo 正在启动 Claude Code...
echo 设置代理服务环境变量...

echo 设置 ANTHROPIC_AUTH_TOKEN...
set ANTHROPIC_AUTH_TOKEN=sk-YD8QjyoZfZlUlAABW0zHq23BBh8agZAMwURz7wWoMfxCyiKP
echo 设置 ANTHROPIC_BASE_URL...
set ANTHROPIC_BASE_URL=http://134.209.165.253:60000

echo.
echo 使用代理服务: %ANTHROPIC_BASE_URL%
echo.
echo 正在启动 Claude Code...
echo.

claude