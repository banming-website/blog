---
title: "Tailwind CSS 使用技巧"
date: 2026-01-27
category: "技术"
tags: ["CSS", "Tailwind", "样式"]
---

# Tailwind CSS 使用技巧

Tailwind CSS 是一个功能类优先的 CSS 框架，让我们来看看一些实用技巧。

## 1. 自定义配置

扩展 Tailwind 的默认主题：

```javascript
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          500: '#0ea5e9',
          900: '#0c4a6e',
        }
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      }
    }
  }
}
```

## 2. 响应式设计

使用移动优先的响应式设计：

```html
<!-- 移动端默认，md:以上中等屏幕 -->
<div className="w-full md:w-1/2 lg:w-1/3">
  响应式容器
</div>
```

## 3. 深色模式

实现深色模式支持：

```html
<div className="bg-white dark:bg-black text-black dark:text-white">
  自适应主题
</div>
```

## 4. 组合工具类

创建可复用的组件样式：

```html
<button className="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
  按钮
</button>
```

## 5. 使用 @apply 提取重复样式

```css
/* 在 CSS 文件中 */
.btn-primary {
  @apply px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors;
}
```

## 总结

Tailwind CSS 提供了一种快速构建 UI 的方式，掌握这些技巧可以让你的开发效率更高。
