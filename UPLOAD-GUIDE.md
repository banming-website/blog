# 📤 虚拟主机上传文件清单

## 需要上传的文件和目录

```
✅ 必须上传：
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
│       │   ├── main.css
│       │   ├── markdown.css
│       │   └── luoxiaohei.css
│       └── js/
│           ├── theme.js
│           ├── music-player.js
│           ├── search.js
│           ├── smooth-scroll.js
│           └── utils.js
├── templates/
│   ├── header.php
│   └── footer.php
├── vendor/              # Composer 依赖（必须上传）
├── index.php
├── post.php
├── archive.php
├── search.php
├── page.php
├── rss.php
├── config.php           # 需要先修改配置
└── .htaccess

❌ 不需要上传：
node_modules/
src/
.git/
package.json
composer.json
composer.lock
example-posts/
scripts/
.vscode/
.idea/
```

## 🎯 快速部署三步走

### 第一步：本地安装依赖

```bash
# 在项目根目录执行
composer require erusev/parsedown
composer require erusev/parsedown-extra
composer require firebase/php-jwt:^5.0
```

### 第二步：修改配置

编辑 `config.php`，修改以下内容：

```php
// 修改为你的域名
define('SITE_URL', 'https://yourdomain.com');

// 修改为随机字符串（重要！）
define('JWT_SECRET', '随机生成的密钥');

// GitHub OAuth（在 GitHub 创建 OAuth App 获取）
define('GITHUB_CLIENT_ID', '从GitHub获取');
define('GITHUB_CLIENT_SECRET', '从GitHub获取');
define('ADMIN_GITHUB_USERNAMES', ['你的GitHub用户名']);
```

### 第三步：上传到虚拟主机

1. 使用 FTP/SFTP 工具（推荐 FileZilla）
2. 连接到虚拟主机
3. 上传以上"必须上传"的文件到 `public_html` 或 `www` 目录
4. 访问 https://yourdomain.com 测试

## 📝 上传后的检查

1. **首页能打开吗？**
   - 访问 https://yourdomain.com

2. **后台能登录吗？**
   - 访问 https://yourdomain.com/admin/auth.php
   - 点击 GitHub 登录

3. **权限正确吗？**
   - content/ 目录可写
   - public/uploads/ 目录可写

## ⚠️ 常见问题

### Q: 上传后 404 错误
A: 检查是否在正确的目录，确认 .htaccess 已上传

### Q: 无法登录后台
A: 检查 GitHub OAuth 配置是否正确

### Q: 显示 500 错误
A: 检查 PHP 版本是否为 7.4+，查看错误日志

### Q: 图片无法上传
A: 检查 public/uploads/ 目录权限是否为 755

## 🚀 完成后

1. 访问后台创建第一篇文章
2. 测试各项功能是否正常
3. 享受你的新博客！
