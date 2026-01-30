<!DOCTYPE html>
<html lang="zh-CN" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $pageTitle ?? getTitle() ?></title>

    <!-- SEO Meta Tags -->
    <?php if (isset($post)): ?>
    <meta name="description" content="<?= e($post['excerpt'] ?? getSetting('site.description')) ?>">
    <meta property="og:title" content="<?= e($post['title']) ?>">
    <meta property="og:description" content="<?= e($post['excerpt'] ?? '') ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?= url('post/' . $post['id']) ?>">
    <?php else: ?>
    <meta name="description" content="<?= getSetting('site.description') ?>">
    <?php endif; ?>

    <meta name="author" content="<?= getSetting('site.author') ?>">
    <link rel="canonical" href="<?= currentUrl() ?>">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: 'hsl(var(--background))',
                        foreground: 'hsl(var(--foreground))',
                        card: 'hsl(var(--card))',
                        'card-foreground': 'hsl(var(--card-foreground))',
                        primary: 'hsl(var(--primary))',
                        'primary-foreground': 'hsl(var(--primary-foreground))',
                        'muted-foreground': 'hsl(var(--muted-foreground))',
                        accent: 'hsl(var(--accent))',
                        'accent-foreground': 'hsl(var(--accent-foreground))',
                    },
                    fontFamily: {
                        sans: ['"Noto Sans SC"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        };
    </script>

    <!-- Prism.js (代码高亮) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- GSAP (动画) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <!-- Lenis (平滑滚动) -->
    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.29/bundled/lenis.min.js"></script>

    <!-- 自定义样式 -->
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/markdown.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/luoxiaohei.css') ?>">

    <style>
        :root {
            --background: 0 0% 100%;
            --foreground: 0 0% 3.9%;
            --card: 0 0% 100%;
            --card-foreground: 0 0% 3.9%;
            --primary: 0 0% 9%;
            --primary-foreground: 0 0% 98%;
            --muted-foreground: 0 0% 45.1%;
            --accent: 0 0% 96.1%;
            --accent-foreground: 0 0% 9%;
        }

        .dark {
            --background: 0 0% 3.9%;
            --foreground: 0 0% 98%;
            --card: 0 0% 3.9%;
            --card-foreground: 0 0% 98%;
            --primary: 0 0% 98%;
            --primary-foreground: 0 0% 9%;
            --muted-foreground: 0 0% 63.9%;
            --accent: 0 0% 14.9%;
            --accent-foreground: 0 0% 98%;
        }

        /* 布局模式 */
        .layout-compact { max-width: 960px; }
        .layout-default { max-width: 1280px; }
        .layout-wide { max-width: 1536px; }

        /* 背景图像 */
        .bg-character {
            background-image: url('https://your-cdn.com/character.png');
            background-repeat: no-repeat;
            background-position: bottom right;
            background-attachment: fixed;
        }

        /* 自定义滚动条 */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: hsl(var(--muted-foreground) / 0.3);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: hsl(var(--muted-foreground) / 0.5);
        }
    </style>
</head>
<body class="bg-background text-foreground min-h-screen <?= $themeClass ?? '' ?> <?= $layoutClass ?? 'layout-default mx-auto' ?>">

    <!-- 导航栏 -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-background/80 backdrop-blur-md border-b">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="<?= url() ?>" class="flex items-center space-x-2">
                    <span class="text-xl font-bold"><?= getSetting('site.title') ?></span>
                </a>

                <!-- 桌面导航 -->
                <div class="hidden md:flex items-center space-x-6">
                    <?php
                    $navItems = getSetting('navigation', []);
                    foreach ($navItems as $item):
                    ?>
                    <a href="<?= url($item['url']) ?>" class="text-sm hover:text-primary transition flex items-center gap-1">
                        <?php if (isset($item['icon'])): ?>
                        <i data-lucide="<?= $item['icon'] ?>" class="w-4 h-4"></i>
                        <?php endif; ?>
                        <?= $item['name'] ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- 右侧按钮 -->
                <div class="flex items-center space-x-4">
                    <!-- 搜索按钮 -->
                    <button onclick="openSearch()" class="p-2 hover:bg-accent rounded-lg transition">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>

                    <!-- 外观设置 -->
                    <button onclick="toggleAppearance()" class="p-2 hover:bg-accent rounded-lg transition">
                        <i data-lucide="palette" class="w-5 h-5"></i>
                    </button>

                    <!-- 主题切换 -->
                    <button onclick="toggleTheme()" class="p-2 hover:bg-accent rounded-lg transition">
                        <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
                        <i data-lucide="moon" class="w-5 h-5 block dark:hidden"></i>
                    </button>

                    <!-- 移动端菜单按钮 -->
                    <button onclick="toggleMobileMenu()" class="md:hidden p-2 hover:bg-accent rounded-lg transition">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- 移动端菜单 -->
        <div id="mobile-menu" class="md:hidden hidden border-t bg-background">
            <div class="container mx-auto px-4 py-4 space-y-2">
                <?php foreach ($navItems as $item): ?>
                <a href="<?= url($item['url']) ?>" class="block px-4 py-2 hover:bg-accent rounded-lg transition">
                    <?= $item['name'] ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>

    <!-- 主内容 -->
    <main class="pt-16 min-h-screen">
        <?php
        // 设置内容变量，供子页面使用
        if (!isset($contentWidth)) {
            $contentWidth = 'max-w-4xl mx-auto px-4';
        }
        ?>
