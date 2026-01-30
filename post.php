<?php
require_once 'config.php';
require_once 'includes/database.php';
require_once 'includes/markdown.php';

$slug = $_GET['slug'] ?? '';
$db = new JsonDB();
$post = $db->getPost($slug);

if (!$post || !($post['publish'] ?? true)) {
    header('HTTP/1.0 404 Not Found');
    include 'templates/header.php';
    echo '<div class="container mx-auto px-4 py-20 text-center">
        <h1 class="text-4xl font-bold mb-4">404</h1>
        <p class="text-muted-foreground mb-8">文章不存在</p>
        <a href="' . url() . '" class="text-primary">返回首页</a>
    </div>';
    include 'templates/footer.php';
    exit;
}

// 加密文章验证
if (!empty($post['encrypted'])) {
    $password = $_GET['password'] ?? '';
    $hash = $post['encryption']['hash'] ?? '';

    if (empty($password) || !verifyPassword($password, $post['id'], $hash)) {
        $pageTitle = '需要密码';
        include 'templates/header.php';
        echo '<div class="' . $contentWidth . ' py-12">
            <div class="max-w-md mx-auto mt-20 p-6 bg-card border rounded-lg">
                <h1 class="text-2xl font-bold mb-4">加密文章</h1>
                <p class="text-muted-foreground mb-6">此文章需要密码才能查看</p>
                <form method="GET" action="">
                    <input type="hidden" name="slug" value="' . e($slug) . '">
                    <div class="mb-4">
                        <input type="password" name="password" placeholder="请输入密码"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:opacity-90">
                        提交
                    </button>
                </form>
            </div>
        </div>';
        include 'templates/footer.php';
        exit;
    }
}

// 生成目录
$markdownParser = new MarkdownParser();
$toc = $markdownParser->extractTOC($post['content']);

// 获取相邻文章
$prevPost = $db->getAdjacentPost($slug, 'prev');
$nextPost = $db->getAdjacentPost($slug, 'next');

$pageTitle = $post['title'] . ' - ' . getSetting('site.title');
include 'templates/header.php';
?>

<div class="<?= $contentWidth ?> py-12">
    <article>
        <!-- 文章头部 -->
        <header class="mb-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-6"><?= e($post['title']) ?></h1>

            <div class="flex flex-wrap items-center gap-4 text-muted-foreground mb-6">
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
                <span>·</span>
                <span class="flex items-center gap-1">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <?= $post['wordCount'] ?? 0 ?> 字
                </span>
            </div>

            <?php if (!empty($post['excerpt'])): ?>
            <p class="text-xl text-muted-foreground">
                <?= e($post['excerpt']) ?>
            </p>
            <?php endif; ?>
        </header>

        <!-- 目录 -->
        <?php if (!empty($toc)): ?>
        <nav class="mb-8 p-6 bg-accent/20 rounded-lg print:hidden">
            <h3 class="font-bold mb-4 flex items-center gap-2">
                <i data-lucide="list" class="w-5 h-5"></i>
                目录
            </h3>
            <ul class="space-y-2">
                <?php foreach ($toc as $item): ?>
                <li class="level-<?= $item['level'] ?>" style="padding-left: <?= ($item['level'] - 1) * 16 ?>px">
                    <a href="#<?= $item['id'] ?>" class="hover:text-primary transition block">
                        <?= e($item['text']) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php endif; ?>

        <!-- 文章内容 -->
        <div class="markdown-content prose prose-lg dark:prose-invert max-w-none">
            <?= $post['content'] ?>
        </div>

        <!-- 标签 -->
        <?php if (!empty($post['tags'])): ?>
        <div class="mt-12 pt-8 border-t">
            <div class="flex flex-wrap gap-2">
                <?php foreach ($post['tags'] as $tag): ?>
                <a href="<?= url('archive?tag=' . urlencode($tag)) ?>"
                   class="px-4 py-2 bg-accent hover:bg-accent/80 rounded-lg text-sm transition">
                    #<?= e($tag) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 文章导航 -->
        <?php if ($prevPost || $nextPost): ?>
        <nav class="mt-12 pt-8 border-t flex justify-between gap-4">
            <?php if ($prevPost): ?>
            <a href="<?= url('post/' . $prevPost['id']) ?>"
               class="flex-1 p-4 bg-accent/20 hover:bg-accent/30 rounded-lg transition">
                <div class="text-sm text-muted-foreground mb-1">上一篇</div>
                <div class="font-medium"><?= e($prevPost['title']) ?></div>
            </a>
            <?php else: ?>
            <div class="flex-1"></div>
            <?php endif; ?>

            <?php if ($nextPost): ?>
            <a href="<?= url('post/' . $nextPost['id']) ?>"
               class="flex-1 p-4 bg-accent/20 hover:bg-accent/30 rounded-lg transition text-right">
                <div class="text-sm text-muted-foreground mb-1">下一篇</div>
                <div class="font-medium"><?= e($nextPost['title']) ?></div>
            </a>
            <?php else: ?>
            <div class="flex-1"></div>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

        <!-- 分享按钮 -->
        <div class="mt-12 pt-8 border-t">
            <div class="flex items-center justify-between">
                <span class="text-sm text-muted-foreground">分享文章</span>
                <div class="flex gap-2">
                    <button onclick="shareToTwitter()" class="p-2 hover:bg-accent rounded-lg transition">
                        <i data-lucide="twitter" class="w-5 h-5"></i>
                    </button>
                    <button onclick="shareToWeibo()" class="p-2 hover:bg-accent rounded-lg transition">
                        <i data-lucide="share-2" class="w-5 h-5"></i>
                    </button>
                    <button onclick="copyLink()" class="p-2 hover:bg-accent rounded-lg transition">
                        <i data-lucide="link" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>
    </article>
</div>

<script>
// 分享功能
function shareToTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('<?= e($post['title']) ?>');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
}

function shareToWeibo() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('<?= e($post['title']) ?> - <?= getSetting('site.title') ?>');
    window.open(`https://service.weibo.com/share/share.php?url=${url}&title=${text}`, '_blank');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href);
    alert('链接已复制到剪贴板');
}

// 代码块复制按钮
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('pre').forEach(pre => {
        const button = document.createElement('button');
        button.className = 'absolute top-2 right-2 p-2 bg-accent hover:bg-accent/80 rounded text-xs opacity-0 hover:opacity-100 transition';
        button.textContent = '复制';
        pre.style.position = 'relative';
        pre.appendChild(button);

        button.addEventListener('click', () => {
            const code = pre.querySelector('code').textContent;
            navigator.clipboard.writeText(code);
            button.textContent = '已复制!';
            setTimeout(() => button.textContent = '复制', 2000);
        });

        pre.addEventListener('mouseenter', () => button.style.opacity = '1');
        pre.addEventListener('mouseleave', () => button.style.opacity = '0');
    });
});
</script>

<?php include 'templates/footer.php'; ?>
