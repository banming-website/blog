---
title: "如何向博客添加文章"
date: 2026-01-30
category: "教程"
tags: ["教程", "Obsidian", "博客"]
---

# 如何向博客添加文章

本博客采用 **Obsidian + Gitee** 的工作流管理内容，以下是详细的添加文章步骤。

## 准备工作

1. 安装 [Obsidian](https://obsidian.md/)
2. 注册 Gitee 账号
3. 创建博客内容仓库

## 文章格式

每篇文章必须是 Markdown 格式，并包含以下 frontmatter：

```yaml
---
title: "文章标题"
date: 2026-01-30
category: "分类"
tags: ["标签1", "标签2"]
---
```

### Frontmatter 字段说明

- **title**: 文章标题（必填）
- **date**: 发布日期（必填，格式：YYYY-MM-DD）
- **category**: 文章分类（必填）
- **tags**: 标签数组（可选）

## 文件夹组织

建议的文件夹结构：

```
博客/
├── 技术/
│   ├── frontend/
│   │   └── react-hooks.md
│   └── backend/
│       └── nodejs-api.md
├── 生活/
│   └── daily-note.md
└── 教程/
    └── how-to-blog.md
```

分类会自动从文件夹路径生成，例如 `技术/frontend` 中的文章分类为 `frontend`。

## 编写文章

在 Obsidian 中创建新的 Markdown 文件，添加 frontmatter 后开始编写内容：

```markdown
---
title: "我的第一篇博客"
date: 2026-01-30
category: "技术"
tags: ["Markdown", "写作"]
---

# 我的第一篇博客

这里是文章内容...
```

## 发布流程

1. **保存文件**：在 Obsidian 中保存文章
2. **提交到 Git**：`git add .` && `git commit -m "新增文章"`
3. **推送到 Gitee**：`git push`
4. **自动更新**：Webhook 会自动触发，博客内容会在几秒内更新

## 图片处理

建议使用以下方式处理图片：

1. 将图片放在 Obsidian 的附件文件夹
2. 在 Markdown 中引用：`![图片描述](./images/image.jpg)`
3. 推送到 Gitee 后，图片会自动上传到 OSS

## 注意事项

- 文件名建议使用英文或拼音
- 避免使用特殊字符
- `.obsidian/` 和 `Temp Book/` 文件夹中的文件不会被处理
- 文章会自动转换为 slug 作为 URL

## 示例文章

本项目的 `example-posts/` 文件夹中包含了几篇示例文章，可以参考它们的格式。

---

开始写作吧！🎉
