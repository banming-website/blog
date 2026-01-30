<?php
require_once 'config.php';
require_once 'includes/database.php';

$db = new JsonDB();
$posts = $db->getPosts([
    'publish' => true,
    'resource' => false,
    'sort' => 'date',
    'order' => 'DESC'
]);

// 筛选条件
$categoryFilter = $_GET['category'] ?? '';
$tagFilter = $_GET['tag'] ?? '';

if ($categoryFilter) {
    $posts = array_filter($posts, fn($p) => ($p['category'] ?? '') === $categoryFilter);
}

if ($tagFilter) {
    $posts = array_filter($posts, fn($p) => in_array($tagFilter, $p['tags'] ?? []));
}

$posts = array_values($posts);

// 按年份分组
$grouped = groupByYear($posts);

// 统计信息
$stats = calculateStats($posts);
$categories = $db->getCategories();
$tags = $db->getTags();

$pageTitle = '归档 - ' . getSetting('site.title');
include 'templates/header.php';
?>

<div class="<?= $contentWidth ?> py-12">
    <!-- 页面标题 -->
    <header class="mb-12">
        <h1 class="text-4xl font-bold mb-4">文章归档</h1>
        <p class="text-xl text-muted-foreground">
            共 <?= count($posts) ?> 篇文章，<?= number_format($stats['totalWords']) ?> 字
        </p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- 侧边栏 -->
        <aside class="lg:col-span-1 space-y-8">
            <!-- 分类筛选 -->
            <div>
                <h3 class="font-bold mb-4 flex items-center gap-2">
                    <i data-lucide="folder" class="w-5 h-5"></i>
                    分类
                </h3>
                <div class="space-y-2">
                    <a href="<?= url('archive') ?>"
                       class="<?= empty($categoryFilter) ? 'text-primary font-medium' : 'text-muted-foreground' ?> hover:text-primary transition block">
                        全部 (<?= count($posts) ?>)
                    </a>
                    <?php foreach ($categories as $cat => $count): ?>
                    <a href="<?= url('archive?category=' . urlencode($cat)) ?>"
                       class="<?= $categoryFilter === $cat ? 'text-primary font-medium' : 'text-muted-foreground' ?> hover:text-primary transition flex justify-between">
                        <span><?= e($cat) ?></span>
                        <span class="text-sm">(<?= $count ?>)</span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 标签云 -->
            <?php if (!empty($tags)): ?>
            <div>
                <h3 class="font-bold mb-4 flex items-center gap-2">
                    <i data-lucide="tag" class="w-5 h-5"></i>
                    标签
                </h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach (array_slice($tags, 0, 20) as $tag => $count): ?>
                    <a href="<?= url('archive?tag=' . urlencode($tag)) ?>"
                       class="px-3 py-1 bg-accent hover:bg-accent/80 rounded-full text-sm transition">
                        <?= e($tag) ?> (<?= $count ?>)
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 统计 -->
            <div class="p-4 bg-accent/20 rounded-lg">
                <h3 class="font-bold mb-4">统计</h3>
                <ul class="space-y-2 text-sm">
                    <li>总文章: <?= $stats['totalPosts'] ?></li>
                    <li>总字数: <?= number_format($stats['totalWords']) ?></li>
                    <li>今年发文: <?= $stats['thisYearCount'] ?></li>
                    <li>今年字数: <?= number_format($stats['thisYearWords']) ?></li>
                </ul>
            </div>
        </aside>

        <!-- 文章列表 -->
        <div class="lg:col-span-3">
            <?php if (empty($grouped)): ?>
            <div class="text-center py-12 text-muted-foreground">
                未找到相关文章
            </div>
            <?php else: ?>
            <?php foreach ($grouped as $year => $yearPosts): ?>
            <section class="mb-12">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                    <span><?= $year ?></span>
                    <span class="text-sm text-muted-foreground font-normal">
                        <?= count($yearPosts) ?> 篇文章
                    </span>
                </h2>

                <div class="space-y-4">
                    <?php foreach ($yearPosts as $post): ?>
                    <article class="group">
                        <a href="<?= url('post/' . $post['id']) ?>" class="block">
                            <h3 class="text-lg font-medium mb-2 group-hover:text-primary transition">
                                <?= e($post['title']) ?>
                            </h3>
                            <div class="flex items-center gap-3 text-sm text-muted-foreground">
                                <span><?= formatDate($post['date'], 'm月d日') ?></span>
                                <span>·</span>
                                <span><?= e($post['category'] ?? '未分类') ?></span>
                                <span>·</span>
                                <span><?= $post['readingTime'] ?? 1 ?> 分钟</span>

                                <?php if (!empty($post['tags'])): ?>
                                <span>·</span>
                                <div class="flex gap-1">
                                    <?php foreach (array_slice($post['tags'], 0, 3) as $tag): ?>
                                    <span class="text-xs px-2 py-0.5 bg-accent rounded">
                                        #<?= e($tag) ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($post['excerpt'])): ?>
                            <p class="mt-2 text-sm text-muted-foreground line-clamp-1">
                                <?= e($post['excerpt']) ?>
                            </p>
                            <?php endif; ?>
                        </a>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
