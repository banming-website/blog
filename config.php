<?php
/**
 * 博客系统配置文件
 */

// ==================== 基础配置 ====================
define('SITE_NAME', 'Banming Blog');
define('SITE_DESCRIPTION', '技术博客与生活记录');
define('SITE_URL', 'https://sdjz.wiki');
define('SITE_AUTHOR', 'Banming');

// ==================== 路径配置 ====================
define('ROOT_PATH', dirname(__FILE__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONTENT_PATH', ROOT_PATH . '/content');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// ==================== 数据库配置 ====================
define('DATA_DIR', CONTENT_PATH);
define('POSTS_FILE', DATA_DIR . '/posts.json');
define('SETTINGS_FILE', DATA_DIR . '/settings.json');
define('MEDIA_FILE', DATA_DIR . '/media.json');
define('CACHE_FILE', DATA_DIR . '/cache.json');

// ==================== 认证配置 ====================
define('ADMIN_SESSION_NAME', 'blog_admin_session');
define('SESSION_LIFETIME', 604800); // 7天（秒）
define('JWT_SECRET', 'your-secret-key-change-this'); // 请修改为随机密钥

// GitHub OAuth 配置
define('GITHUB_CLIENT_ID', '');
define('GITHUB_CLIENT_SECRET', '');
define('GITHUB_CALLBACK_URL', SITE_URL . '/admin/auth.php?action=callback');

// 管理员白名单
define('ADMIN_GITHUB_USERNAMES', ['your-github-username']); // 替换为你的 GitHub 用户名

// ==================== Gitee API 配置 ====================
define('GITEE_PAT', ''); // Gitee Personal Access Token
define('GITEE_OWNER', '');
define('GITEE_REPO', '');
define('GITEE_BRANCH', 'master');

// ==================== 外部 API 配置 ====================
// 网易云音乐
define('NETEASE_PLAYLIST_ID', '8308939217');
define('NETEASE_API', 'https://music-api.sdjz.wiki');

// 和风天气
define('QWEATHER_API_KEY', '6f366b58fba0418e89cdd262fd07a333');

// ==================== 缓存配置 ====================
define('CACHE_ENABLED', true);
define('CACHE_TTL', 3600); // 默认缓存时间（秒）
define('CACHE_DIR', ROOT_PATH . '/cache');

// ==================== 文件上传配置 ====================
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 200);

// ==================== 分页配置 ====================
define('POSTS_PER_PAGE', 10);
define('ADMIN_POSTS_PER_PAGE', 20);

// ==================== 性能配置 ====================
define('ENABLE_GZIP', true);
define('ENABLE_CACHE_HEADERS', true);

// ==================== 调试模式 ====================
define('DEBUG_MODE', false); // 生产环境设为 false

// ==================== 时区设置 ====================
date_default_timezone_set('Asia/Shanghai');

// ==================== 错误报告 ====================
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ==================== 自动加载 ====================
spl_autoload_register(function ($class) {
    $paths = [
        INCLUDES_PATH . '/classes/' . $class . '.php',
        INCLUDES_PATH . '/' . strtolower($class) . '.php',
    ];

    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ==================== 辅助函数 ====================
/**
 * 获取站点配置
 */
function getSetting($key, $default = null) {
    static $settings = null;

    if ($settings === null && file_exists(SETTINGS_FILE)) {
        $settings = json_decode(file_get_contents(SETTINGS_FILE), true);
    }

    $keys = explode('.', $key);
    $value = $settings;

    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }

    return $value ?? $default;
}

/**
 * 生成 CSRF 令牌
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * 验证 CSRF 令牌
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * 安全输出 HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * 格式化日期
 */
function formatDate($date, $format = 'Y-m-d') {
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

/**
 * 生成 URL 友好的 slug
 */
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^\w\x{4e00}-\x{9fa5}]+/u', '-', $text);
    $text = trim($text, '-');
    return $text;
}

// ==================== Session 启动 ====================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
