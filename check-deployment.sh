#!/bin/bash
# 虚拟主机部署检查脚本

echo "============================================"
echo "   博客系统部署检查"
echo "============================================"
echo ""

# 检查 PHP 版本
echo "1. 检查 PHP 版本..."
php_version=$(php -v | head -n 1 | awk '{print $2}' | cut -d'.' -f1,2)
echo "   当前 PHP 版本: $php_version"

if [ "$(echo "$php_version >= 7.4" | bc)" -eq 1 ]; then
    echo "   ✅ PHP 版本符合要求"
else
    echo "   ❌ PHP 版本过低，需要 7.4 或更高"
fi
echo ""

# 检查必需的目录
echo "2. 检查必需目录..."
dirs=("content" "public/uploads" "includes" "templates" "admin" "api")

for dir in "${dirs[@]}"; do
    if [ -d "$dir" ]; then
        echo "   ✅ $dir/"
    else
        echo "   ❌ $dir/ 缺失"
    fi
done
echo ""

# 检查必需的文件
echo "3. 检查必需文件..."
files=("config.php" "index.php" "post.php" "archive.php" ".htaccess")

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "   ✅ $file"
    else
        echo "   ❌ $file 缺失"
    fi
done
echo ""

# 检查 Composer 依赖
echo "4. 检查 Composer 依赖..."
if [ -d "vendor" ]; then
    echo "   ✅ vendor/ 目录存在"
    if [ -f "vendor/autoload.php" ]; then
        echo "   ✅ autoload.php 存在"
    else
        echo "   ❌ autoload.php 缺失，请运行 composer install"
    fi
else
    echo "   ❌ vendor/ 目录不存在"
    echo "   请运行: composer install"
fi
echo ""

# 检查文件权限
echo "5. 检查文件权限..."
if [ -w "content" ]; then
    echo "   ✅ content/ 可写"
else
    echo "   ❌ content/ 不可写"
    echo "   运行: chmod 755 content"
fi

if [ -d "public/uploads" ]; then
    if [ -w "public/uploads" ]; then
        echo "   ✅ public/uploads/ 可写"
    else
        echo "   ❌ public/uploads/ 不可写"
        echo "   运行: chmod 755 public/uploads"
    fi
fi
echo ""

# 检查配置
echo "6. 检查配置文件..."
if grep -q "your-secret-key-change-this" config.php; then
    echo "   ⚠️  JWT_SECRET 使用默认值，请修改！"
else
    echo "   ✅ JWT_SECRET 已设置"
fi

if grep -q "GITHUB_CLIENT_ID', ''" config.php; then
    echo "   ⚠️  GitHub OAuth 未配置"
else
    echo "   ✅ GitHub OAuth 已配置"
fi
echo ""

# 检查 .htaccess
echo "7. 检查 .htaccess..."
if [ -f ".htaccess" ]; then
    echo "   ✅ .htaccess 存在"
    if grep -q "RewriteEngine On" .htaccess; then
        echo "   ✅ mod_rewrite 已启用"
    else
        echo "   ⚠️  mod_rewrite 配置未找到"
    fi
else
    echo "   ❌ .htaccess 缺失"
fi
echo ""

echo "============================================"
echo "   检查完成！"
echo "============================================"
echo ""
echo "如果所有项都通过，你可以："
echo "1. 访问网站首页测试"
echo "2. 访问 /admin/auth.php 登录后台"
echo "3. 开始创建文章"
echo ""
