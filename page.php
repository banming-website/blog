<?php
require_once 'config.php';

$page = $_GET['page'] ?? 'home';
$pages = [
    'about' => ['title' => '关于', 'content' => 'about-content'],
    'works' => ['title' => '作品', 'content' => 'works-content'],
    'games' => ['title' => '游戏', 'content' => 'games-content'],
    'music' => ['title' => '音乐', 'content' => 'music-content'],
    'resources' => ['title' => '资源', 'content' => 'resources-content'],
    'friends' => ['title' => '好兄弟们', 'content' => 'friends-content'],
];

if (!isset($pages[$page])) {
    header('HTTP/1.0 404 Not Found');
    include 'templates/header.php';
    echo '<div class="container mx-auto px-4 py-20 text-center">
        <h1 class="text-4xl font-bold mb-4">404</h1>
        <p class="text-muted-foreground">页面不存在</p>
    </div>';
    include 'templates/footer.php';
    exit;
}

$pageInfo = $pages[$page];
$pageTitle = $pageInfo['title'] . ' - ' . getSetting('site.title');
include 'templates/header.php';
?>

<div class="<?= $contentWidth ?? 'max-w-4xl mx-auto px-4' ?> py-12">
    <h1 class="text-4xl font-bold mb-8"><?= $pageInfo['title'] ?></h1>

    <div class="prose prose-lg dark:prose-invert max-w-none">
        <?php
        // 这里可以根据不同页面加载不同内容
        // 实际内容可以从 JSON 文件或数据库加载
        switch ($page) {
            case 'about':
                echo '<p>关于页面内容...</p>';
                break;
            case 'works':
                echo '<p>作品页面内容...</p>';
                break;
            case 'friends':
                echo '<p>友情链接...</p>';
                break;
            default:
                echo '<p>页面内容正在建设中...</p>';
        }
        ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
