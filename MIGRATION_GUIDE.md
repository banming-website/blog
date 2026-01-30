# 博客名称修改说明

## 修改内容

博客名称已从 **"Shuakami"** 更改为 **"Banming"**

## 修改的文件

### 核心配置文件
- `src/app/layout.tsx` - 元数据、标题、图标路径
- `src/app/rss/route.ts` - RSS 订阅配置

### 组件文件
- `src/components/Header.tsx` - 顶部导航栏名称
- `src/components/Footer.tsx` - 页脚版权信息和社交链接
- `src/components/SideNav.tsx` - 侧边栏个人信息
- `src/components/MobileNav.tsx` - 移动端导航栏

### 工具文件
- `src/utils/posts.ts` - 文章作者信息
- `src/hooks/use-music-player.tsx` - 音乐播放器版权信息

### 文档文件
- `README.md` - 项目说明

## 需要手动更新的资源

### 1. Favicon（网站图标）
需要将以下文件重命名或替换：
- `/public/shuakami.jpg` → `/public/banming.jpg`

### 2. 头像资源
需要将以下文件重命名或替换：
- `/public/friends/assets/avatars/shuakami.jpg` → `/public/friends/assets/avatars/banming.jpg`

### 3. 社交链接
需要更新以下账号信息：
- GitHub: `https://github.com/shuakami` → `https://github.com/banming`
- Email: `shuakami@sdjz.wiki` → `banming@sdjz.wiki`

## 如何更新资源

### 方法一：重命名现有文件
```bash
cd public
mv shuakami.jpg banming.jpg
cd friends/assets/avatars
mv shuakami.jpg banming.jpg
```

### 方法二：替换为新图片
1. 准备新的 `banming.jpg` 图片
2. 替换 `/public/banming.jpg`
3. 替换 `/public/friends/assets/avatars/banming.jpg`

推荐尺寸：
- Favicon: 512x512px 或更高
- 头像: 400x400px 或更高（正方形）

## 示例文章

已在 `example-posts/` 文件夹中创建示例文章：

1. **welcome.md** - 博客欢迎页
2. **nextjs-16-features.md** - Next.js 技术文章
3. **typescript-best-practices.md** - TypeScript 教程
4. **tailwind-css-tips.md** - Tailwind CSS 技巧
5. **how-to-add-posts.md** - 如何添加文章的教程

要将这些文章发布到博客：
1. 将 `.md` 文件推送到你的 Gitee 仓库
2. Webhook 会自动处理并更新博客内容

## 验证修改

启动开发服务器验证修改：
```bash
npm run dev
# 或
pnpm dev
```

检查以下内容是否正确显示：
- 浏览器标签页标题
- 页面顶部和底部的名称
- RSS 订阅信息
- 社交媒体链接
- 侧边栏个人信息

## 完成

所有代码修改已完成，只需更新图片资源即可！
