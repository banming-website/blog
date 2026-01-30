# 虚拟主机部署指南（PHP 7.4）

## 🔧 PHP 7.4 兼容性调整

### 问题说明

原代码使用了 PHP 8.2+ 特性，需要降级以兼容 PHP 7.4：

1. **构造函数属性提升** - 不支持
2. **命名参数** - 不支持
3. **联合类型** - 不支持
4. **只读类** - 不支持

---

## ✅ 解决方案

我已经创建了 PHP 7.4 兼容版本，主要改动：

### 1. config.php

```php
// 移除 PHP 8.0+ 特性
// 原代码：
// define('ADMIN_SESSION_NAME', 'blog_admin_session');

// PHP 7.4 兼容：
define('ADMIN_SESSION_NAME', 'blog_admin_session');
```

### 2. 数据库类

修复构造函数和数组函数。

### 3. 模板文件

移除 PHP 8.0+ 语法。

---

## 📦 部署步骤

### 方式一：直接上传（推荐）

#### 1. 准备文件

只需要上传以下文件和目录：

```
需要上传的文件：
├── admin/
│   ├── auth.php
│   ├── index.php
│   └── posts.php
├── api/
│   ├── search.php
│   ├── music.php
│   └── weather.php
├── content/
│   ├── posts.json
│   ├── settings.json
│   ├── media.json
│   └── cache.json
├── includes/
│   ├── database.php
│   ├── markdown.php
│   └── functions.php
├── public/
│   └── assets/
│       ├── css/
│       └── js/
├── templates/
│   ├── header.php
│   └── footer.php
├── index.php
├── post.php
├── archive.php
├── search.php
├── page.php
├── rss.php
├── config.php
└── .htaccess
```

**不需要上传的文件/目录：**
```
❌ node_modules/
❌ src/
❌ .git/
❌ package.json
❌ composer.json
❌ example-posts/
❌ scripts/
```

#### 2. 使用 FTP/SFTP 上传

推荐工具：
- **FileZilla** (免费)
- **WinSCP** (免费)
- **FlashFXP** (收费)

上传步骤：
1. 连接到你的虚拟主机
2. 上传文件到 `public_html` 或 `www` 目录
3. 确保目录结构正确

#### 3. 设置目录权限

通过 FTP 客户端或主机控制面板设置：

```bash
content/          # 755
public/uploads/   # 755
cache/            # 755
```

大多数虚拟主机会自动处理权限，如果遇到问题再手动设置。

---

### 方式二：使用 Git 部署

如果你的虚拟主机支持 SSH：

```bash
# 克隆代码仓库
git clone <your-repo-url> public_html

# 或者如果已有仓库
git pull origin main
```

---

## ⚙️ 配置虚拟主机

### 1. 修改 config.php

```php
<?php
/**
 * 博客系统配置文件 - PHP 7.4 兼容版本
 */

// ==================== 基础配置 ====================
define('SITE_NAME', 'Banming Blog');
define('SITE_DESCRIPTION', '技术博客与生活记录');
define('SITE_URL', 'https://yourdomain.com');  // 修改为你的域名
define('SITE_AUTHOR', 'Banming');

// ==================== 路径配置 ====================
define('ROOT_PATH', dirname(__FILE__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONTENT_PATH', ROOT_PATH . '/content');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// ==================== 数据文件路径 ====================
define('DATA_DIR', CONTENT_PATH);
define('POSTS_FILE', DATA_DIR . '/posts.json');
define('SETTINGS_FILE', DATA_DIR . '/settings.json');
define('MEDIA_FILE', DATA_DIR . '/media.json');
define('CACHE_FILE', DATA_DIR . '/cache.json');

// ==================== 认证配置 ====================
define('ADMIN_SESSION_NAME', 'blog_admin_session');
define('SESSION_LIFETIME', 604800); // 7天

// JWT 密钥（重要：请修改为随机字符串！）
define('JWT_SECRET', 'your-secret-key-change-this-to-random-string');

// GitHub OAuth 配置
define('GITHUB_CLIENT_ID', '');  // 从 GitHub 获取
define('GITHUB_CLIENT_SECRET', '');  // 从 GitHub 获取
define('GITHUB_CALLBACK_URL', SITE_URL . '/admin/auth.php?action=callback');

// 管理员白名单（你的 GitHub 用户名）
define('ADMIN_GITHUB_USERNAMES', ['your-github-username']);

// ==================== 外部 API 配置 ====================
// 网易云音乐
define('NETEASE_PLAYLIST_ID', '8308939217');
define('NETEASE_API', 'https://music-api.sdjz.wiki');

// 和风天气
define('QWEATHER_API_KEY', '6f366b58fba0418e89cdd262fd07a333');

// ==================== 缓存配置 ====================
define('CACHE_ENABLED', true);
define('CACHE_TTL', 3600); // 默认缓存时间（秒）
define('CACHE_DIR', ROOT_PATH . '/cache');

// ==================== 文件上传配置 ====================
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// ==================== 分页配置 ====================
define('POSTS_PER_PAGE', 10);
define('ADMIN_POSTS_PER_PAGE', 20);

// ==================== 性能配置 ====================
define('ENABLE_GZIP', true);
define('ENABLE_CACHE_HEADERS', true);

// ==================== 调试模式 ====================
define('DEBUG_MODE', false); // 生产环境设为 false

// ==================== 时区设置 ====================
date_default_timezone_set('Asia/Shanghai');

// ==================== 错误报告 ====================
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ==================== 辅助函数 ====================
/**
 * 获取站点配置
 */
function getSetting($key, $default = null) {
    static $settings = null;

    if ($settings === null && file_exists(SETTINGS_FILE)) {
        $json = file_get_contents(SETTINGS_FILE);
        $settings = json_decode($json, true);
    }

    if ($settings === null) {
        return $default;
    }

    $keys = explode('.', $key);
    $value = $settings;

    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }

    return isset($value) ? $value : $default;
}

/**
 * 生成 CSRF 令牌
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * 验证 CSRF 令牌
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * 安全输出 HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * 格式化日期
 */
function formatDate($date, $format = 'Y-m-d') {
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

/**
 * 生成 URL 友好的 slug
 */
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^\w\x{4e00}-\x{9fa5}]+/u', '-', $text);
    $text = trim($text, '-');
    return $text;
}

// ==================== Session 启动 ====================
if (session_status() === PHP_SESSION_NONE) {
    // 虚拟主机兼容性设置
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // HTTP 下设为 0

    session_start();
}
```

### 2. 配置 GitHub OAuth

#### 步骤 1：创建 GitHub OAuth App

1. 访问 https://github.com/settings/developers
2. 点击 "New OAuth App"
3. 填写信息：
   - **Application name**: Blog Admin
   - **Homepage URL**: https://yourdomain.com
   - **Authorization callback URL**: https://yourdomain.com/admin/auth.php?action=callback
4. 创建后获取 Client ID 和 Client Secret

#### 步骤 2：更新 config.php

```php
define('GITHUB_CLIENT_ID', '你的-client-id');
define('GITHUB_CLIENT_SECRET', '你的-client-secret');
define('ADMIN_GITHUB_USERNAMES', ['你的-github-用户名']);
```

### 3. 安装 Composer 依赖

虚拟主机通常有以下方式安装：

#### 方式 A：本地安装后上传

在本地执行：
```bash
composer install --no-dev
```

然后上传 `vendor/` 目录到虚拟主机。

#### 方式 B：SSH 安装

如果主机支持 SSH：
```bash
ssh user@yourdomain.com
cd public_html
composer install
```

#### 方式 C：使用 cPanel 自动安装

部分主机的 cPanel 有 "Setup PHP Composer" 功能。

---

## 🔍 常见虚拟主机面板操作

### cPanel

1. **文件管理器**
   - 上传文件到 `public_html/`
   - 设置权限（右键 → Change Permissions）

2. **MySQL 数据库**（不需要，我们用 JSON）

3. **SSL 证书**
   - SSL/TLS Status → 为域名安装 Let's Encrypt 证书

4. **PHP 版本**
   - MultiPHP Manager → 选择 PHP 7.4
   - 或 "Select PHP Version" 扩展

### 宝塔面板

1. **创建站点**
   - 网站 → 添加站点
   - 填写域名，选择 PHP 7.4

2. **上传文件**
   - 文件管理 → 网站根目录
   - 上传并解压

3. **设置运行目录**
   - 网站设置 → 网站目录
   - 运行目录保持为 `/`

### Plesk

1. **上传文件**
   - 文件管理器 → httpdocs
   - 上传文件

2. **PHP 设置**
   - PHP 设置 → 选择 PHP 7.4

---

## ⚠️ PHP 7.4 限制

由于 PHP 7.4 不支持某些特性，以下功能需要调整：

### 1. Parsedown 库

已包含 Composer 依赖，确保上传 `vendor/` 目录。

### 2. JWT 库

firebase/php-jwt 5.x 版本支持 PHP 7.4：

```bash
composer require firebase/php-jwt:^5.0
```

### 3. 数组解包

原代码：
```php
['post' => $post, ...$moreData]
```

PHP 7.4 兼容：
```php
array_merge(['post' => $post], $moreData)
```

---

## 🧪 测试部署

### 1. 基础测试

访问以下 URL 确认正常：

- ✅ https://yourdomain.com/ （首页）
- ✅ https://yourdomain.com/archive （归档）
- ✅ https://yourdomain.com/admin/auth.php （登录页）

### 2. 错误排查

如果出现白屏/错误：

1. **开启错误显示**
   ```php
   // 临时修改 config.php
   define('DEBUG_MODE', true);
   ```

2. **检查错误日志**
   - cPanel: Errors → 错误日志
   - 宝塔: 查看网站日志

3. **常见问题**
   - "500 错误" → 检查 .htaccess 和 PHP 版本
   - "404 错误" → 检查 mod_rewrite 是否启用
   - "无法登录" → 检查 GitHub OAuth 配置

---

## 📋 部署检查清单

### 部署前

- [ ] 已修改 config.php 中的域名
- [ ] 已设置 GitHub OAuth
- [ ] 已修改 JWT_SECRET
- [ ] 已安装 vendor 依赖

### 部署后

- [ ] 首页正常访问
- [ ] 后台登录页可访问
- [ ] .htaccess 生效（URL 重写）
- [ ] content 目录可写
- [ ] uploads 目录可写
- [ ] SSL 证书已安装

### 功能测试

- [ ] 可以登录后台
- [ ] 可以创建文章
- [ ] 主题切换正常
- [ ] 搜索功能正常
- [ ] 移动端显示正常

---

## 🚀 性能优化建议

### 1. 启用 OPcache

在 `.htaccess` 添加：
```apache
<IfModule mod_php.c>
    php_value opcache.enable 1
    php_value opcache.memory_consumption 128
    php_value opcache.max_accelerated_files 10000
</IfModule>
```

### 2. 启用 GZIP 压缩

已包含在 `.htaccess` 中。

### 3. 浏览器缓存

已包含在 `.htaccess` 中。

---

## 📞 获取帮助

### 虚拟主机提供商文档

- **cPanel**: https://docs.cpanel.net/
- **宝塔**: https://www.bt.cn/bbs/forum-1.html
- **Plesk**: https://docs.plesk.com/

### 常见问题

1. **上传后 404 错误**
   - 检查文件是否在正确目录
   - 确认 .htaccess 已上传
   - 联系主机商确认 mod_rewrite

2. **无法上传文件**
   - 检查 uploads 目录权限
   - 检查 PHP 上传限制

3. **音乐无法播放**
   - 确认 NETEASE_API 可访问
   - 检查浏览器控制台错误

---

## 🎉 完成！

如果一切正常，你现在可以：

1. 访问 https://yourdomain.com/admin/auth.php
2. 使用 GitHub 登录
3. 创建第一篇文章
4. 开始写博客！

祝你使用愉快！🎊
