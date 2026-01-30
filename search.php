<?php
require_once 'config.php';
require_once 'includes/database.php';

$query = $_GET['q'] ?? '';
$db = new JsonDB();

if (!empty($query)) {
    $results = $db->searchPosts($query, 30);
} else {
    $results = [];
}

$pageTitle = '搜索' . ($query ? "：{$query}" : '') . ' - ' . getSetting('site.title');
include 'templates/header.php';
?>

<div class="<?= $contentWidth ?> py-12">
    <header class="mb-12">
        <h1 class="text-4xl font-bold mb-8">搜索</h1>

        <form method="GET" action="">
            <div class="flex gap-4">
                <input
                    type="text"
                    name="q"
                    value="<?= e($query) ?>"
                    placeholder="搜索文章标题、内容、标签..."
                    class="flex-1 px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    autofocus
                >
                <button type="submit" class="px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:opacity-90 transition">
                    搜索
                </button>
            </div>
        </form>
    </header>

    <?php if (!empty($query)): ?>
    <div class="mb-8">
        <p class="text-muted-foreground">
            找到 <span class="font-bold text-foreground"><?= count($results) ?></span> 个结果
        </p>
    </div>
    <?php endif; ?>

    <?php if (empty($query)): ?>
    <div class="text-center py-12 text-muted-foreground">
        <i data-lucide="search" class="w-16 h-16 mx-auto mb-4 opacity-50"></i>
        <p>输入关键词搜索文章</p>
    </div>
    <?php elseif (empty($results)): ?>
    <div class="text-center py-12 text-muted-foreground">
        <i data-lucide="file-x" class="w-16 h-16 mx-auto mb-4 opacity-50"></i>
        <p>未找到相关文章</p>
    </div>
    <?php else: ?>
    <div class="space-y-6">
        <?php foreach ($results as $post): ?>
        <article class="group">
            <a href="<?= url('post/' . $post['id']) ?>" class="block">
                <h2 class="text-xl font-bold mb-2 group-hover:text-primary transition">
                    <?= e($post['title']) ?>
                </h2>

                <div class="flex items-center gap-3 text-sm text-muted-foreground mb-2">
                    <span><?= formatDate($post['date'], 'Y年m月d日') ?></span>
                    <span>·</span>
                    <span><?= e($post['category'] ?? '未分类') ?></span>
                </div>

                <p class="text-muted-foreground line-clamp-2">
                    <?= e($post['excerpt'] ?? strip_tags($post['content'])) ?>
                </p>

                <?php if (!empty($post['tags'])): ?>
                <div class="flex flex-wrap gap-2 mt-3">
                    <?php foreach ($post['tags'] as $tag): ?>
                    <span class="px-2 py-1 bg-accent rounded text-xs">#<?= e($tag) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </a>
        </article>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?>
