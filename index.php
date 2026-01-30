<?php
require_once 'config.php';
require_once 'includes/database.php';

$db = new JsonDB();
$posts = $db->getPosts([
    'publish' => true,
    'resource' => false,
    'limit' => 10,
    'sort' => 'date',
    'order' => 'DESC'
]);

$pageTitle = '首页 - ' . getSetting('site.title');
include 'templates/header.php';
?>

<div class="<?= $contentWidth ?> py-12">
    <!-- 欢迎区域 -->
    <section class="mb-16 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
            欢迎来到 <?= getSetting('site.title') ?>
        </h1>
        <p class="text-xl text-muted-foreground mb-8">
            <?= getSetting('site.description') ?>
        </p>
        <div class="flex justify-center gap-4">
            <a href="<?= url('archive') ?>" class="px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition">
                浏览文章
            </a>
            <a href="<?= url('about') ?>" class="px-6 py-3 border rounded-lg hover:bg-accent transition">
                关于我
            </a>
        </div>
    </section>

    <!-- 最新文章 -->
    <section>
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold">最新文章</h2>
            <a href="<?= url('archive') ?>" class="text-sm text-muted-foreground hover:text-primary transition flex items-center gap-1">
                查看全部
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <?php if (empty($posts)): ?>
        <div class="text-center py-12">
            <p class="text-muted-foreground">暂无文章</p>
        </div>
        <?php else: ?>
        <div class="space-y-8">
            <?php foreach ($posts as $post): ?>
            <article class="group">
                <a href="<?= url('post/' . $post['id']) ?>" class="block">
                    <!-- 标题 -->
                    <h2 class="text-2xl font-bold mb-3 group-hover:text-primary transition">
                        <?= e($post['title']) ?>
                    </h2>

                    <!-- 元信息 -->
                    <div class="flex items-center gap-3 text-sm text-muted-foreground mb-3">
                        <span class="flex items-center gap-1">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                            <?= formatDate($post['date'], 'Y年m月d日') ?>
                        </span>
                        <span>·</span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="folder" class="w-4 h-4"></i>
                            <?= e($post['category'] ?? '未分类') ?>
                        </span>
                        <span>·</span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            <?= $post['readingTime'] ?? 1 ?> 分钟阅读
                        </span>
                    </div>

                    <!-- 摘要 -->
                    <p class="text-muted-foreground mb-4 line-clamp-2">
                        <?= e($post['excerpt'] ?? strip_tags($post['content'])) ?>
                    </p>

                    <!-- 标签 -->
                    <?php if (!empty($post['tags'])): ?>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($post['tags'] as $tag): ?>
                        <span class="px-3 py-1 bg-accent text-accent-foreground rounded-full text-xs">
                            #<?= e($tag) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </a>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>

    <!-- 统计信息 -->
    <?php
    $stats = $db->getStats();
    ?>
    <section class="mt-16 p-6 bg-accent/20 rounded-lg">
        <h3 class="font-bold mb-4">博客统计</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div>
                <div class="text-2xl font-bold"><?= $stats['totalPosts'] ?></div>
                <div class="text-sm text-muted-foreground">篇文章</div>
            </div>
            <div>
                <div class="text-2xl font-bold"><?= number_format($stats['totalWords']) ?></div>
                <div class="text-sm text-muted-foreground">总字数</div>
            </div>
            <div>
                <div class="text-2xl font-bold"><?= $stats['categories'] ?></div>
                <div class="text-sm text-muted-foreground">个分类</div>
            </div>
            <div>
                <div class="text-2xl font-bold"><?= $stats['thisYearCount'] ?></div>
                <div class="text-sm text-muted-foreground">今年发文</div>
            </div>
        </div>
    </section>
</div>

<?php include 'templates/footer.php'; ?>
