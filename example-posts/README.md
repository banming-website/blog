# 示例文章说明

这个文件夹包含了博客的示例文章，用于演示博客的内容格式和样式。

## 文章列表

1. **welcome.md** - 欢迎页面，介绍博客的基本信息
2. **nextjs-16-features.md** - Next.js 16 技术文章
3. **typescript-best-practices.md** - TypeScript 最佳实践
4. **tailwind-css-tips.md** - Tailwind CSS 使用技巧
5. **how-to-add-posts.md** - 如何添加新文章的教程

## 如何使用

### 方法一：直接推送到 Gitee 仓库

1. 将这些 `.md` 文件复制到你的 Obsidian 博客仓库
2. 提交并推送到 Gitee
3. Webhook 会自动处理这些文章

### 方法二：通过 Obsidian 编辑

1. 将这些文件导入到你的 Obsidian 工作区
2. 根据需要修改内容
3. 通过 Git 插件推送到 Gitee

## Frontmatter 格式

每篇文章都必须包含 frontmatter：

```yaml
---
title: "文章标题"
date: 2026-01-30
category: "分类"
tags: ["标签1", "标签2"]
---
```

## URL 生成规则

- 文章的 URL 会被自动转换为 slug
- 例如：`nextjs-16-features.md` → `/post/nextjs-16-features`

## 注意事项

- 日期格式必须是 `YYYY-MM-DD`
- category 和 tags 会影响文章的分类和筛选
- 支持标准 Markdown 语法
- 可以使用代码块、表格、列表等
