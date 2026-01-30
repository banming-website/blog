<?php
/**
 * 后台管理系统 - 文章管理
 */

require_once '../config.php';
require_once '../includes/database.php';

// 检查登录状态
if (!isset($_SESSION[ADMIN_SESSION_NAME])) {
    header('Location: auth.php');
    exit;
}

$db = new JsonDB();

// 处理操作
$action = $_GET['action'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['id'] ?? '';

    switch ($action) {
        case 'delete':
            if ($db->deletePost($postId)) {
                $message = '文章已删除';
            } else {
                $message = '删除失败';
            }
            break;

        case 'toggle-publish':
            $post = $db->getPost($postId);
            if ($post) {
                $post['publish'] = !($post['publish'] ?? true);
                $db->savePost($post);
                $message = $post['publish'] ? '文章已发布' : '文章已设为草稿';
            }
            break;
    }
}

// 获取筛选条件
$page = (int)($_GET['page'] ?? 1);
$categoryFilter = $_GET['category'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$filters = ['sort' => 'date', 'order' => 'DESC'];

if ($statusFilter === 'published') {
    $filters['publish'] = true;
} elseif ($statusFilter === 'draft') {
    $filters['publish'] = false;
}

if ($categoryFilter) {
    $filters['category'] = $categoryFilter;
}

$totalPosts = count($db->getPosts($filters));
$filters['page'] = $page;
$filters['perPage'] = ADMIN_POSTS_PER_PAGE;

$posts = $db->getPosts($filters);
$categories = $db->getCategories();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章管理 - 后台管理</title>
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
                <a href="index.php" class="flex items-center gap-2 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    <i data-lucide="home" class="w-5 h-5"></i>
                    首页
                </a>
                <a href="posts.php" class="flex items-center gap-2 px-4 py-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg mt-1">
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                    文章管理
                </a>
                <a href="editor.php" class="flex items-center gap-2 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mt-1">
                    <i data-lucide="edit" class="w-5 h-5"></i>
                    新建文章
                </a>
                <a href="auth.php?action=logout" class="flex items-center gap-2 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mt-1">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                    登出
                </a>
            </nav>
        </aside>

        <!-- 主内容区 -->
        <main class="flex-1 p-8">
            <header class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold">文章管理</h2>
                <a href="editor.php" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center gap-2">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    新建文章
                </a>
            </header>

            <?php if ($message): ?>
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400 rounded-lg">
                <?= htmlspecialchars($message) ?>
            </div>
            <?php endif; ?>

            <!-- 筛选栏 -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6">
                <div class="flex gap-4">
                    <select onchange="location.href='?status='+this.value" class="px-4 py-2 border rounded-lg">
                        <option value="">全部状态</option>
                        <option value="published" <?= $statusFilter === 'published' ? 'selected' : '' ?>>已发布</option>
                        <option value="draft" <?= $statusFilter === 'draft' ? 'selected' : '' ?>>草稿</option>
                    </select>

                    <select onchange="location.href='?category='+this.value" class="px-4 py-2 border rounded-lg">
                        <option value="">全部分类</option>
                        <?php foreach ($categories as $cat => $count): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $categoryFilter === $cat ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?> (<?= $count ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- 文章列表 -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">标题</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">分类</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">日期</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">状态</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">操作</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($posts as $post): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <a href="editor.php?id=<?= $post['id'] ?>" class="font-medium hover:text-blue-500">
                                        <?= htmlspecialchars($post['title']) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <?= htmlspecialchars($post['category'] ?? '未分类') ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <?= formatDate($post['date']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($post['publish'] ?? true): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">已发布</span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">草稿</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="id" value="<?= $post['id'] ?>">
                                        <button type="submit" name="action" value="toggle-publish"
                                                class="text-blue-500 hover:text-blue-700 mr-3" title="切换发布状态">
                                            <i data-lucide="eye" class="w-5 h-5"></i>
                                        </button>
                                        <a href="editor.php?id=<?= $post['id'] ?>" class="text-gray-500 hover:text-gray-700 mr-3" title="编辑">
                                            <i data-lucide="edit-2" class="w-5 h-5"></i>
                                        </a>
                                        <button type="submit" name="action" value="delete"
                                                onclick="return confirm('确定删除这篇文章吗？')"
                                                class="text-red-500 hover:text-red-700" title="删除">
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 分页 -->
                <div class="px-6 py-4 border-t flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        共 <?= $totalPosts ?> 篇文章
                    </div>
                    <?php if ($totalPosts > ADMIN_POSTS_PER_PAGE): ?>
                    <div class="flex gap-2">
                        <?php
                        $totalPages = ceil($totalPosts / ADMIN_POSTS_PER_PAGE);
                        for ($i = 1; $i <= $totalPages; $i++):
                        ?>
                        <a href="?page=<?= $i ?>&status=<?= $statusFilter ?>&category=<?= $categoryFilter ?>"
                           class="<?= $i === $page ? 'bg-blue-500 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' ?> px-3 py-1 rounded">
                            <?= $i ?>
                        </a>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
