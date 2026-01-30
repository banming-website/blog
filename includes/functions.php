<?php
/**
 * 公共函数库
 */

/**
 * 获取客户端 IP 地址
 */
function getClientIP() {
    $ip = '';

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    }

    return $ip;
}

/**
 * 获取用户位置（根据 IP）
 */
function getUserLocation() {
    $ip = getClientIP();

    // 本地测试，返回北京
    if ($ip === '127.0.0.1' || $ip === '::1') {
        return ['city' => '北京', 'province' => '北京'];
    }

    // 调用 IP 定位 API
    $apiUrl = "http://whois.pconline.com.cn/ipJson.jsp?ip=" . $ip;
    $response = @file_get_contents($apiUrl);

    if ($response) {
        // GBK 转 UTF-8
        $response = mb_convert_encoding($response, 'UTF-8', 'GBK');

        if (preg_match('/"city":"([^"]+)"/', $response, $matches)) {
            $city = str_replace('市', '', $matches[1]);
            return ['city' => $city, 'province' => $city];
        }
    }

    return ['city' => '北京', 'province' => '北京'];
}

/**
 * 按年份分组文章
 */
function groupByYear($posts) {
    $grouped = [];

    foreach ($posts as $post) {
        $year = date('Y', strtotime($post['date']));
        if (!isset($grouped[$year])) {
            $grouped[$year] = [];
        }
        $grouped[$year][] = $post;
    }

    // 倒序排列年份
    krsort($grouped);

    return $grouped;
}

/**
 * 计算统计数据
 */
function calculateStats($posts) {
    $totalWords = 0;
    $thisYear = date('Y');
    $thisYearCount = 0;
    $thisYearWords = 0;

    foreach ($posts as $post) {
        $words = $post['wordCount'] ?? 0;
        $totalWords += $words;

        $postYear = date('Y', strtotime($post['date']));
        if ($postYear == $thisYear) {
            $thisYearCount++;
            $thisYearWords += $words;
        }
    }

    return [
        'totalPosts' => count($posts),
        'totalWords' => $totalWords,
        'thisYearCount' => $thisYearCount,
        'thisYearWords' => $thisYearWords,
    ];
}

/**
 * 生成随机字符串
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * 密码哈希（用于加密文章）
 */
function hashPassword($password, $slug) {
    $secret = JWT_SECRET;
    $data = $slug . ':' . $password . ':' . $secret;
    return hash('sha256', $data);
}

/**
 * 验证密码
 */
function verifyPassword($password, $slug, $hash) {
    return hashPassword($password, $slug) === $hash;
}

/**
 * 缩短文本
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * 格式化文件大小
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * 获取 Gravatar 头像
 */
function getGravatar($email, $size = 80) {
    $hash = md5(strtolower(trim($email)));
    return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=mp";
}

/**
 * 生成文件名
 */
function generateFilename($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);
    $slug = generateSlug($basename);

    return $slug . '-' . time() . '.' . $extension;
}

/**
 * 创建目录（如果不存在）
 */
function ensureDirectory($path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

/**
 * 记录日志
 */
function logMessage($message, $level = 'info') {
    $logFile = ROOT_PATH . '/logs/app.log';
    $logDir = dirname($logFile);

    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $entry = "[{$timestamp}] [{$level}] {$message}\n";

    error_log($entry, 3, $logFile);
}

/**
 * JSON 响应
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 错误响应
 */
function errorResponse($message, $statusCode = 400) {
    jsonResponse([
        'error' => true,
        'message' => $message
    ], $statusCode);
}

/**
 * 成功响应
 */
function successResponse($data = []) {
    jsonResponse([
        'success' => true,
        'data' => $data
    ]);
}

/**
 * 重定向
 */
function redirect($url, $statusCode = 302) {
    header("Location: {$url}", true, $statusCode);
    exit;
}

/**
 * 检查是否为 AJAX 请求
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * 获取请求输入
 */
function input($key, $default = null) {
    if (isset($_POST[$key])) {
        return $_POST[$key];
    }
    if (isset($_GET[$key])) {
        return $_GET[$key];
    }
    return $default;
}

/**
 * 获取所有请求输入
 */
function allInput() {
    return array_merge($_GET, $_POST);
}

/**
 * 旧值（用于表单回填）
 */
function old($key, $default = '') {
    return $_SESSION['_old_input'][$key] ?? $default;
}

/**
 * 设置旧值
 */
function setOldInput($data) {
    $_SESSION['_old_input'] = $data;
}

/**
 * 清除旧值
 */
function clearOldInput() {
    unset($_SESSION['_old_input']);
}

/**
 * Flash 消息
 */
function flash($key, $value = null) {
    if ($value === null) {
        $message = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $message;
    }

    $_SESSION['_flash'][$key] = $value;
}

/**
 * 设置页面标题
 */
function setTitle($title) {
    $_SESSION['_page_title'] = $title;
}

/**
 * 获取页面标题
 */
function getTitle() {
    return $_SESSION['_page_title'] ?? getSetting('site.title', 'Blog');
}

/**
 * 资源 URL
 */
function asset($path) {
    return '/assets/' . ltrim($path, '/');
}

/**
 * 上传文件 URL
 */
function upload($path) {
    return '/uploads/' . ltrim($path, '/');
}

/**
 * URL 生成
 */
function url($path = '') {
    $baseUrl = rtrim(getSetting('site.url', SITE_URL), '/');
    return $baseUrl . '/' . ltrim($path, '/');
}

/**
 * 当前 URL
 */
function currentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * 检查是否为 HTTPS
 */
function isHttps() {
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
}

/**
 * 获取 User Agent
 */
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? '';
}

/**
 * 检测设备类型
 */
function isMobile() {
    $userAgent = getUserAgent();
    $mobileAgents = ['Android', 'iPhone', 'iPad', 'Windows Phone', 'Mobile'];

    foreach ($mobileAgents as $agent) {
        if (stripos($userAgent, $agent) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * 获取浏览器信息
 */
function getBrowser() {
    $userAgent = getUserAgent();

    if (preg_match('/Chrome/i', $userAgent)) {
        return 'Chrome';
    } elseif (preg_match('/Firefox/i', $userAgent)) {
        return 'Firefox';
    } elseif (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
        return 'Safari';
    } elseif (preg_match('/Edge/i', $userAgent)) {
        return 'Edge';
    } elseif (preg_match('/MSIE/i', $userAgent) || preg_match('/Trident/i', $userAgent)) {
        return 'Internet Explorer';
    }

    return 'Unknown';
}

/**
 * 验证邮箱
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * 验证 URL
 */
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * 清理 HTML
 */
function sanitizeHtml($html) {
    $allowedTags = '<p><br><strong><em><u><a><ul><ol><li><h1><h2><h3><h4><h5><h6><pre><code><blockquote>';
    return strip_tags($html, $allowedTags);
}

/**
 * 防止 XSS 攻击
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }

    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * 解析 User Agent（更详细）
 */
function parseUserAgent($userAgent) {
    $info = [
        'browser' => 'Unknown',
        'version' => 'Unknown',
        'os' => 'Unknown',
        'device' => 'Desktop'
    ];

    // 操作系统
    if (preg_match('/Windows/i', $userAgent)) {
        $info['os'] = 'Windows';
    } elseif (preg_match('/Macintosh|Mac OS/i', $userAgent)) {
        $info['os'] = 'MacOS';
    } elseif (preg_match('/Linux/i', $userAgent)) {
        $info['os'] = 'Linux';
    } elseif (preg_match('/Android/i', $userAgent)) {
        $info['os'] = 'Android';
        $info['device'] = 'Mobile';
    } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
        $info['os'] = 'iOS';
        $info['device'] = 'Mobile';
    }

    return $info;
}

/**
 * 生成 UUID v4
 */
function generateUuid() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // version 4
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // variant

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * 时间流逝（多久之前）
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);

    if ($time < 60) {
        return '刚刚';
    } elseif ($time < 3600) {
        return floor($time / 60) . ' 分钟前';
    } elseif ($time < 86400) {
        return floor($time / 3600) . ' 小时前';
    } elseif ($time < 2592000) {
        return floor($time / 86400) . ' 天前';
    } elseif ($time < 31536000) {
        return floor($time / 2592000) . ' 个月前';
    } else {
        return floor($time / 31536000) . ' 年前';
    }
}
