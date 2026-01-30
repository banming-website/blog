# 博客系统部署说明（PHP 7.4 虚拟主机）

## 📦 部署包内容

本压缩包包含运行博客所需的所有文件。

## 🚀 快速部署（3步）

### 步骤 1：解压文件

将压缩包解压到虚拟主机的 `public_html` 或 `www` 目录。

### 步骤 2：安装 Composer 依赖

**重要！** 必须先安装 PHP 依赖库。

在本地电脑执行：
```bash
composer require erusev/parsedown
composer require erusev/parsedown-extra
composer require firebase/php-jwt:^5.0
```

然后将生成的 `vendor/` 目录上传到虚拟主机。

或者如果虚拟主机支持 SSH：
```bash
ssh user@yourdomain.com
cd public_html
composer require erusev/parsedown
composer require erusev/parsedown-extra
composer require firebase/php-jwt:^5.0
```

### 步骤 3：修改配置

编辑 `config.php` 文件：

```php
// 修改为你的域名
define('SITE_URL', 'https://yourdomain.com');

// 修改为随机密钥（重要！）
define('JWT_SECRET', '随机生成的32位字符串');

// GitHub OAuth（需要去 GitHub 创建 OAuth App）
define('GITHUB_CLIENT_ID', '从GitHub获取');
define('GITHUB_CLIENT_SECRET', '从GitHub获取');
define('ADMIN_GITHUB_USERNAMES', ['你的GitHub用户名']);
```

生成随机密钥方法：
访问 https://www.random.org/strings/?num=1&len=32&digits=on&upperalpha=on&loweralpha=on&unique=on&format=html&rnd=new

## ⚙️ 配置 GitHub OAuth

1. 访问 https://github.com/settings/developers
2. 点击 "New OAuth App"
3. 填写信息：
   - Application name: Blog Admin
   - Homepage URL: https://yourdomain.com
   - Authorization callback URL: https://yourdomain.com/admin/auth.php?action=callback
4. 创建后获取 Client ID 和 Client Secret
5. 更新 config.php 文件

## 📁 目录结构说明

```
public_html/
├── admin/              # 后台管理（登录、文章管理）
├── api/               # API 接口（搜索、音乐、天气）
├── content/           # JSON 数据存储（文章、设置）
├── includes/          # PHP 核心库
├── public/            # 静态资源（CSS、JS、图片）
├── templates/         # HTML 模板
├── vendor/            # Composer 依赖（需要安装）
├── index.php          # 首页
├── post.php           # 文章详情
├── archive.php        # 归档页
├── search.php         # 搜索页
├── config.php         # 配置文件（需要修改）
└── .htaccess          # URL 重写规则
```

## 🧪 测试部署

上传完成后，访问以下 URL 测试：

1. ✅ 首页: https://yourdomain.com/
2. ✅ 归档: https://yourdomain.com/archive
3. ✅ 后台登录: https://yourdomain.com/admin/auth.php
4. ✅ RSS: https://yourdomain.com/rss

## ⚠️ 常见问题

### 404 Not Found
- 确认 .htaccess 文件已上传
- 联系主机商确认 mod_rewrite 已启用

### 500 Internal Server Error
- 检查 PHP 版本是否为 7.4+
- 检查目录权限：content/ 和 public/uploads/ 设为 755
- 查看主机控制面板的错误日志

### 无法登录后台
- 确认 GitHub OAuth 配置正确
- 确认 Callback URL 完全匹配
- 确认你的 GitHub 用户名在白名单中

### Class 'Parsedown' not found
- 说明 vendor/ 目录未上传或不完整
- 重新安装 Composer 依赖

## 📋 目录权限设置

通过 FTP 客户端或主机控制面板设置：

```
content/          # 755
public/uploads/   # 755
cache/            # 755（如果需要）
```

## 🎯 部署完成检查清单

- [ ] 文件已上传到正确目录
- [ ] vendor/ 目录已上传
- [ ] config.php 已修改
- [ ] GitHub OAuth 已配置
- [ ] 目录权限已设置
- [ ] 首页能访问
- [ ] 后台能登录
- [ ] 能创建文章

## 📞 需要帮助？

详细文档请查看：
- 快速开始: QUICKSTART.md
- 上传指南: UPLOAD-GUIDE.md
- 完整文档: README-PHP.md

## 🔒 安全提醒

1. **务必修改 JWT_SECRET** 为随机字符串
2. **启用 HTTPS**（安装 SSL 证书）
3. **定期备份** content/ 目录
4. **保护 config.php** 不被直接访问（.htaccess 已配置）

---

**祝你部署成功！** 🎉

如有问题，请查看各文档或联系主机商技术支持。
