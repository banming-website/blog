# 快速开始指南

## 项目已完成！🎉

恭喜！博客系统已经从 Next.js 成功迁移到 PHP + HTML 架构。

## 📦 已创建的核心文件

### 后台管理
- ✅ `admin/auth.php` - GitHub OAuth 登录
- ✅ `admin/index.php` - 后台首页
- ✅ `admin/posts.php` - 文章管理

### 前端页面
- ✅ `index.php` - 首页
- ✅ `post.php` - 文章详情页
- ✅ `archive.php` - 归档页
- ✅ `search.php` - 搜索页
- ✅ `page.php` - 通用页面
- ✅ `rss.php` - RSS 订阅

### API 接口
- ✅ `api/search.php` - 搜索 API
- ✅ `api/music.php` - 音乐 API
- ✅ `api/weather.php` - 天气 API

### 核心库
- ✅ `includes/database.php` - JSON 数据库类
- ✅ `includes/markdown.php` - Markdown 解析器
- ✅ `includes/functions.php` - 公共函数库

### 模板
- ✅ `templates/header.php` - 页面头部
- ✅ `templates/footer.php` - 页面底部

### 静态资源
- ✅ `public/assets/css/main.css` - 主样式
- ✅ `public/assets/css/markdown.css` - Markdown 样式
- ✅ `public/assets/js/theme.js` - 主题切换
- ✅ `public/assets/js/music-player.js` - 音乐播放器
- ✅ `public/assets/js/search.js` - 搜索功能
- ✅ `public/assets/js/utils.js` - 工具函数

### 配置文件
- ✅ `config.php` - 主配置文件
- ✅ `content/posts.json` - 文章数据
- ✅ `content/settings.json` - 系统设置
- ✅ `.htaccess` - URL 重写规则

## 🚀 下一步操作

### 1. 安装 Composer 依赖

```bash
composer require erusev/parsedown
composer require erusev/parsedown-extra
composer require firebase/php-jwt
```

### 2. 配置系统

编辑 `config.php`，修改以下配置：

```php
// GitHub OAuth（必需）
define('GITHUB_CLIENT_ID', '从 GitHub 获取');
define('GITHUB_CLIENT_SECRET', '从 GitHub 获取');
define('ADMIN_GITHUB_USERNAMES', ['你的 GitHub 用户名']);

// JWT 密钥（必需，请改为随机字符串）
define('JWT_SECRET', 'your-random-secret-key-change-this');
```

### 3. 设置目录权限

```bash
chmod 755 content
chmod 755 public/uploads
chmod 755 cache
```

### 4. 启动本地服务器

**使用 PHP 内置服务器:**
```bash
php -S localhost:8000
```

然后访问:
- 前台: http://localhost:8000
- 后台: http://localhost:8000/admin

**或使用 XAMPP/WAMP:**
将项目放到 `htdocs` 目录，访问 http://localhost/blog

### 5. 创建第一篇文章

1. 访问后台: http://localhost:8000/admin
2. 使用 GitHub 账号登录
3. 点击"新建文章"
4. 编写内容并发布

## 📝 功能清单

- [x] 文章 CRUD（创建、读取、更新、删除）
- [x] 富文本编辑器（TinyMCE）
- [x] Markdown 编辑器
- [x] GitHub OAuth 登录
- [x] 主题切换（浅色/深色/系统）
- [x] 音乐播放器（网易云）
- [x] 天气组件
- [x] 搜索功能
- [x] RSS 订阅
- [x] 平滑滚动
- [x] 代码高亮（Prism.js）
- [x] 响应式设计
- [x] 分类和标签
- [x] 文章密码加密

## 🎨 自定义配置

### 修改网站信息

编辑 `content/settings.json`:

```json
{
  "site": {
    "title": "你的博客标题",
    "description": "博客描述",
    "author": "你的名字"
  }
}
```

### 添加导航菜单

在 `content/settings.json` 的 `navigation` 数组中添加:

```json
{"name": "新页面", "url": "/new-page", "icon": "star"}
```

### 自定义样式

编辑 `public/assets/css/main.css` 文件。

## 📊 数据迁移

如果你有现有的 Next.js 博客数据：

### 从 Redis 导出

```bash
php scripts/migrate-from-redis.php
```

### 从 Gitee 导入

运行迁移脚本自动从 Gitee API 拉取文章。

## 🔧 常见问题

**Q: 样式显示不正常？**
A: 检查 Tailwind CDN 是否可访问，或查看浏览器控制台错误。

**Q: 无法登录后台？**
A: 确保 GitHub OAuth 配置正确，回调 URL 匹配。

**Q: 音乐无法播放？**
A: 检查 `NETEASE_PLAYLIST_ID` 是否正确。

**Q: 图片上传失败？**
A: 确保 `public/uploads/` 目录可写。

## 📚 更多文档

- 完整文档: `README-PHP.md`
- 迁移计划: `.claude/plans/luminous-exploring-emerson.md`

## 🎉 完成！

博客系统已经准备就绪，开始创作吧！

---

**需要帮助？** 查看文档或提交 Issue。
