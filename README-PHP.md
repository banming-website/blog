# 博客系统 - PHP + HTML

基于纯 HTML 静态页面 + PHP 后台管理系统的博客，使用 JSON 文件存储数据。

## 技术栈

- **后端**: PHP 8.2+
- **前端**: HTML + Tailwind CSS (CDN)
- **数据存储**: JSON 文件
- **编辑器**: TinyMCE 6
- **代码高亮**: Prism.js
- **动画**: GSAP + Lenis
- **图标**: Lucide Icons

## 功能特性

✅ **文章管理**: CRUD 操作，草稿/发布切换
✅ **富文本编辑**: TinyMCE 可视化编辑器
✅ **Markdown 支持**: 完整的 Markdown 解析
✅ **主题切换**: 浅色/深色/跟随系统
✅ **音乐播放器**: 网易云音乐集成
✅ **搜索功能**: 全文模糊搜索
✅ **RSS 订阅**: 自动生成 RSS feed
✅ **响应式设计**: 移动端友好
✅ **平滑滚动**: Lenis 平滑滚动
✅ **代码高亮**: Prism.js 语法高亮

## 安装步骤

### 1. 环境要求

- PHP 8.2 或更高版本
- Apache/Nginx 服务器
- 可选：Composer（用于安装 PHP 依赖）

### 2. 安装依赖

```bash
composer require erusev/parsedown
composer require erusev/parsedown-extra
composer require firebase/php-jwt
```

### 3. 配置系统

编辑 `config.php` 文件，设置以下配置：

```php
// GitHub OAuth（后台登录）
define('GITHUB_CLIENT_ID', 'your-client-id');
define('GITHUB_CLIENT_SECRET', 'your-client-secret');
define('GITHUB_CALLBACK_URL', 'https://yourdomain.com/admin/auth.php?action=callback');
define('ADMIN_GITHUB_USERNAMES', ['your-github-username']);

// JWT 密钥（请修改为随机字符串）
define('JWT_SECRET', 'your-secret-key-here');

// 可选：Gitee API（数据迁移）
define('GITEE_PAT', 'your-gitee-token');
```

### 4. 设置权限

```bash
# 确保 content 目录可写
chmod 755 content

# 确保 cache 目录可写
chmod 755 cache

# 确保 uploads 目录可写
chmod 755 public/uploads
```

### 5. 配置 Web 服务器

**Apache (.htaccess 已包含)**:
确保启用了 `mod_rewrite` 模块。

**Nginx**:
```nginx
location / {
    try_files $uri $uri/ @rewrite;
}

location @rewrite {
    rewrite ^/post/([^/]+)$ /post.php?slug=$1 last;
    rewrite ^/(about|works|games|music|resources|friends)$ /page.php?page=$1 last;
    rewrite ^/admin/(.*)$ /admin/$1.php last;
    rewrite ^/api/(.*)$ /api/$1.php last;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

## 使用指南

### 访问网站

- **前台**: https://yourdomain.com/
- **后台**: https://yourdomain.com/admin/

### 登录后台

1. 访问 `/admin/auth.php`
2. 点击"使用 GitHub 登录"
3. 授权后自动跳转到后台

### 创建文章

1. 点击"新建文章"
2. 选择编辑器类型（TinyMCE 或 Markdown）
3. 编写内容和设置元数据
4. 点击"保存"或"发布"

### 管理媒体

1. 进入"媒体管理"
2. 拖拽或点击上传图片
3. 图片会自动保存到 `public/uploads/` 目录

## 目录结构

```
blog/
├── admin/              # 后台管理
│   ├── index.php      # 后台首页
│   ├── auth.php       # 登录认证
│   ├── posts.php      # 文章管理
│   └── editor.php     # 编辑器
├── api/               # API 接口
│   ├── search.php     # 搜索 API
│   ├── music.php      # 音乐 API
│   └── weather.php    # 天气 API
├── content/           # JSON 数据存储
│   ├── posts.json     # 文章数据
│   ├── settings.json  # 系统设置
│   └── media.json     # 媒体索引
├── includes/          # PHP 公共文件
│   ├── database.php   # JSON 数据操作
│   ├── markdown.php   # Markdown 解析
│   └── functions.php  # 公共函数
├── public/            # 静态资源
│   ├── assets/
│   │   ├── css/       # 样式文件
│   │   └── js/        # JavaScript
│   ├── music/         # 音乐文件
│   └── uploads/       # 用户上传
├── templates/         # HTML 模板
│   ├── header.php     # 头部
│   └── footer.php     # 底部
├── index.php          # 首页
├── post.php           # 文章详情
├── archive.php        # 归档页
├── search.php         # 搜索页
├── rss.php            # RSS 订阅
└── config.php         # 配置文件
```

## 从 Next.js 迁移

如果你要从现有的 Next.js 博客迁移数据：

### 1. 导出 Redis 数据

运行 `scripts/migrate-from-redis.php` 脚本导出文章。

### 2. 迁移图片

运行 `scripts/migrate-images.php` 脚本下载图片到本地。

### 3. 手动导入

或者直接在后台创建新文章，复制粘贴内容。

## 功能配置

### 主题切换

访问网站后，点击右上角的调色板图标可以：
- 切换浅色/深色主题
- 选择背景图片
- 调整布局宽度（紧凑/默认/宽屏）

### 音乐播放器

1. 编辑 `config.php` 设置网易云歌单 ID：
   ```php
   define('NETEASE_PLAYLIST_ID', 'your-playlist-id');
   ```

2. 点击右下角音乐图标打开播放器

### 天气组件

已集成和风天气 API，会自动根据访客 IP 显示当地天气。

### 外观设置

编辑 `content/settings.json` 自定义：

```json
{
  "site": {
    "title": "你的博客标题",
    "description": "博客描述",
    "url": "https://yourdomain.com",
    "author": "你的名字"
  },
  "navigation": [
    {"name": "首页", "url": "/", "icon": "home"},
    {"name": "归档", "url": "/archive", "icon": "archive"}
  ]
}
```

## 性能优化

### 1. 启用 GZIP 压缩

已在 `.htaccess` 中配置。

### 2. 静态资源缓存

图片、CSS、JS 已设置长期缓存。

### 3. JSON 文件缓存

PHP 代码内置了文件缓存机制。

## 安全建议

1. **修改密钥**: 修改 `config.php` 中的 `JWT_SECRET`
2. **保护敏感文件**: `.htaccess` 已配置禁止访问
3. **定期备份**: 备份 `content/` 目录
4. **HTTPS**: 启用 SSL 证书
5. **更新依赖**: 定期运行 `composer update`

## 故障排除

### 500 错误

- 检查 PHP 错误日志
- 确保 `content/` 目录可写
- 检查 PHP 版本是否 >= 8.2

### 样式不加载

- 检查 CDN 是否可访问
- 确认 `public/assets/css/` 文件存在

### 音乐无法播放

- 确认 `NETEASE_PLAYLIST_ID` 正确
- 检查音乐 API 是否可用

### 登录失败

- 检查 GitHub OAuth 配置
- 确认回调 URL 正确
- 验证用户名在白名单中

## 扩展功能

### 添加评论系统

推荐使用：
- [Valine](https://valine.js.org/)
- [Twikoo](https://twikoo.js.org/)
- [Waline](https://waline.js.org/)

### 添加分析

推荐使用：
- Google Analytics
- 百度统计
- Cloudflare Analytics

### CDN 加速

推荐使用：
- Cloudflare
- 阿里云 CDN
- 又拍云 CDN

## 常见问题

**Q: 如何备份数据？**
A: 备份 `content/` 目录和 `public/uploads/` 目录即可。

**Q: 可以迁移到数据库吗？**
A: 可以，项目设计时考虑了这一点，只需修改 `includes/database.php` 即可。

**Q: 支持多用户吗？**
A: 当前版本仅支持单用户，可在 `ADMIN_GITHUB_USERNAMES` 中添加多个 GitHub 用户。

**Q: 如何自定义样式？**
A: 编辑 `public/assets/css/main.css` 文件。

**Q: 文章图片存储在哪里？**
A: 默认存储在 `public/uploads/` 目录，可通过媒体管理上传。

## 更新日志

### v1.0.0 (2026-01-30)

- ✨ 初始版本发布
- ✅ 完整的后台管理系统
- ✅ JSON 文件存储
- ✅ 富文本 + Markdown 双编辑器
- ✅ 主题切换
- ✅ 音乐播放器
- ✅ 搜索功能
- ✅ RSS 订阅

## 许可证

MIT License

## 支持

如有问题，请提交 Issue 或联系作者。

---

**享受你的新博客系统！** 🎉
