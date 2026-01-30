<?php
/**
 * 后台管理系统 - 首页
 */

require_once '../config.php';
require_once '../includes/database.php';

// 检查登录状态
if (!isset($_SESSION[ADMIN_SESSION_NAME])) {
    header('Location: auth.php');
    exit;
}

$db = new JsonDB();
$stats = $db->getStats();
$recentPosts = $db->getPosts(['limit' => 5, 'sort' => 'date', 'order' => 'DESC']);

$currentUser = $_SESSION[ADMIN_SESSION_NAME]['user'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理 - <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex min-h-screen">
        <!-- 侧边栏 -->
        <aside class="w-64 bg-white dark:bg-gray-800 border-r">
            <div class="p-6">
                <h1 class="text-xl font-bold">后台管理</h1>
            </div>

            <nav class="px-4">
                <a href="index.php" class="flex items-center gap-2 px-4 py-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    首页
                </a>
                <a href="posts.php" class="flex items-center gap-2 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mt-1">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    文章管理
                </a>
                <a href="editor.php" class="flex items-center gap-2 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mt-1">
                    <i data-lucide="edit" class="w-5 h-5"></i>
                    新建文章
                </a>
                <a href="media.php" class="flex items-center gap-2 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mt-1">
                    <i data-lucide="image" class="w-5 h-5"></i>
                    媒体管理
                </a>
                <a href="settings.php" class="flex items-center gap-2 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mt-1">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    系统设置
                </a>
            </nav>
        </aside>

        <!-- 主内容区 -->
        <main class="flex-1 p-8">
            <!-- 顶部栏 -->
            <header class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold">仪表盘</h2>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        <?= $currentUser ?>
                    </span>
                    <a href="../" target="_blank" class="px-4 py-2 border rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        查看网站
                    </a>
                    <a href="auth.php?action=logout" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                        登出
                    </a>
                </div>
            </header>

            <!-- 统计卡片 -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">总文章数</p>
                            <p class="text-2xl font-bold mt-1"><?= $stats['totalPosts'] ?></p>
                        </div>
                        <i data-lucide="file-text" class="w-10 h-10 text-blue-500"></i>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">总字数</p>
                            <p class="text-2xl font-bold mt-1"><?= number_format($stats['totalWords']) ?></p>
                        </div>
                        <i data-lucide="type" class="w-10 h-10 text-green-500"></i>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">分类数</p>
                            <p class="text-2xl font-bold mt-1"><?= $stats['categories'] ?></p>
                        </div>
                        <i data-lucide="folder" class="w-10 h-10 text-yellow-500"></i>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">今年发文</p>
                            <p class="text-2xl font-bold mt-1"><?= $stats['thisYearCount'] ?></p>
                        </div>
                        <i data-lucide="calendar" class="w-10 h-10 text-purple-500"></i>
                    </div>
                </div>
            </div>

            <!-- 最新文章 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-bold">最新文章</h3>
                </div>
                <div class="p-6">
                    <?php if (empty($recentPosts)): ?>
                    <p class="text-gray-500 text-center py-8">暂无文章</p>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recentPosts as $post): ?>
                        <a href="editor.php?id=<?= $post['id'] ?>"
                           class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition">
                            <div>
                                <h4 class="font-medium"><?= htmlspecialchars($post['title']) ?></h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    <?= formatDate($post['date']) ?>
                                    <?php if (!($post['publish'] ?? true)): ?>
                                    <span class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-xs">草稿</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <i data-lucide="edit-2" class="w-5 h-5 text-gray-400"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="mt-6">
                        <a href="posts.php" class="inline-flex items-center gap-2 text-blue-500 hover:text-blue-600">
                            查看全部文章
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
