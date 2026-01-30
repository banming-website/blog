<?php
/**
 * 搜索 API
 * 支持文章标题、内容、分类、标签的模糊搜索
 */

require_once '../config.php';
require_once '../includes/database.php';

header('Content-Type: application/json; charset=utf-8');

// 启用 CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// 处理预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$query = $_GET['q'] ?? '';
$results = [];

if (empty($query)) {
    echo json_encode($results);
    exit;
}

$db = new JsonDB();
$results = $db->searchPosts($query, 30);

// 移除 score 字段
$results = array_map(function($post) {
    unset($post['score']);
    return $post;
}, $results);

echo json_encode($results);
