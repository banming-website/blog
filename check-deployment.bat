@echo off
chcp 65001 >nul
echo ============================================
echo    博客系统部署检查
echo ============================================
echo.

echo 1. 检查 PHP 版本...
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo    ❌ PHP 未安装或不在 PATH 中
    goto :end
) else (
    for /f "tokens=2" %%i in ('php -v ^| findstr "PHP"') do set php_version=%%i
    echo    ✅ PHP 版本: %php_version%
)
echo.

echo 2. 检查必需目录...
if exist "content" (echo    ✅ content/) else (echo    ❌ content/ 缺失)
if exist "public\uploads" (echo    ✅ public\uploads/) else (echo    ❌ public\uploads/ 缺失)
if exist "includes" (echo    ✅ includes/) else (echo    ❌ includes/ 缺失)
if exist "templates" (echo    ✅ templates/) else (echo    ❌ templates/ 缺失)
if exist "admin" (echo    ✅ admin/) else (echo    ❌ admin/ 缺失)
if exist "api" (echo    ✅ api/) else (echo    ❌ api/ 缺失)
echo.

echo 3. 检查必需文件...
if exist "config.php" (echo    ✅ config.php) else (echo    ❌ config.php 缺失)
if exist "index.php" (echo    ✅ index.php) else (echo    ❌ index.php 缺失)
if exist "post.php" (echo    ✅ post.php) else (echo    ❌ post.php 缺失)
if exist ".htaccess" (echo    ✅ .htaccess) else (echo    ❌ .htaccess 缺失)
echo.

echo 4. 检查 Composer 依赖...
if exist "vendor" (
    echo    ✅ vendor/ 目录存在
    if exist "vendor\autoload.php" (
        echo    ✅ autoload.php 存在
    ) else (
        echo    ❌ autoload.php 缺失，请运行: composer install
    )
) else (
    echo    ❌ vendor/ 目录不存在
    echo    请运行: composer install
)
echo.

echo 5. 检查配置...
findstr /C:"your-secret-key-change-this" config.php >nul
if %errorlevel% equ 0 (
    echo    ⚠️  JWT_SECRET 使用默认值，请修改！
) else (
    echo    ✅ JWT_SECRET 已设置
)

findstr /C:"GITHUB_CLIENT_ID', ''" config.php >nul
if %errorlevel% equ 0 (
    echo    ⚠️  GitHub OAuth 未配置
) else (
    echo    ✅ GitHub OAuth 已配置
)
echo.

echo ============================================
echo    检查完成！
echo ============================================
echo.
echo 如果所有项都通过，你可以：
echo 1. 运行: php -S localhost:8000
echo 2. 访问 http://localhost:8000
echo 3. 访问 http://localhost:8000/admin/auth.php 登录后台
echo.

:end
pause
