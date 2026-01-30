<?php
/**
 * 后台管理系统 - 登录认证
 * 支持 GitHub OAuth 登录
 */

require_once '../config.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        // GitHub OAuth 登录
        if (empty(GITHUB_CLIENT_ID) || empty(GITHUB_CLIENT_SECRET)) {
            die('请配置 GitHub OAuth 参数');
        }

        $state = bin2hex(random_bytes(16));
        $_SESSION['github_oauth_state'] = $state;

        $authUrl = 'https://github.com/login/oauth/authorize?' . http_build_query([
            'client_id' => GITHUB_CLIENT_ID,
            'redirect_uri' => GITHUB_CALLBACK_URL,
            'scope' => 'read:user,user:email',
            'state' => $state
        ]);

        header("Location: {$authUrl}");
        exit;

    case 'callback':
        // GitHub OAuth 回调
        $code = $_GET['code'] ?? '';
        $state = $_GET['state'] ?? '';

        // 验证 state
        if (!isset($_SESSION['github_oauth_state']) || $state !== $_SESSION['github_oauth_state']) {
            die('Invalid state parameter');
        }

        unset($_SESSION['github_oauth_state']);

        // 交换 access token
        $tokenResponse = @file_get_contents('https://github.com/login/oauth/access_token?' . http_build_query([
            'client_id' => GITHUB_CLIENT_ID,
            'client_secret' => GITHUB_CLIENT_SECRET,
            'code' => $code
        ]));

        if ($tokenResponse === false) {
            die('Failed to exchange token');
        }

        parse_str($tokenResponse, $tokenData);
        $accessToken = $tokenData['access_token'] ?? '';

        if (empty($accessToken)) {
            die('Failed to get access token');
        }

        // 获取用户信息
        $context = stream_context_create([
            'http' => [
                'header' => "Authorization: token {$accessToken}\r\n" .
                           "User-Agent: Blog\r\n"
            ]
        ]);

        $userResponse = @file_get_contents('https://api.github.com/user', false, $context);

        if ($userResponse === false) {
            die('Failed to fetch user info');
        }

        $user = json_decode($userResponse, true);
        $username = $user['login'] ?? '';

        // 验证是否为管理员
        if (!in_array($username, ADMIN_GITHUB_USERNAMES)) {
            die('Access denied');
        }

        // 生成 JWT token
        require_once '../vendor/autoload.php';
        use Firebase\JWT\JWT;
        use Firebase\JWT\Key;

        $payload = [
            'user' => $username,
            'avatar' => $user['avatar_url'] ?? '',
            'exp' => time() + SESSION_LIFETIME
        ];

        $token = JWT::encode($payload, JWT_SECRET, 'HS256');

        // 设置 session
        $_SESSION[ADMIN_SESSION_NAME] = [
            'token' => $token,
            'user' => $username,
            'avatar' => $user['avatar_url'] ?? ''
        ];

        // 重定向到后台首页
        header('Location: index.php');
        exit;

    case 'logout':
        // 登出
        unset($_SESSION[ADMIN_SESSION_NAME]);
        header('Location: auth.php');
        exit;

    default:
        // 显示登录页面
        ?>
        <!DOCTYPE html>
        <html lang="zh-CN">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>登录 - 后台管理</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <script src="https://unpkg.com/lucide@latest"></script>
        </head>
        <body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-96">
                <div class="text-center mb-8">
                    <i data-lucide="github" class="w-16 h-16 mx-auto mb-4"></i>
                    <h1 class="text-2xl font-bold">后台管理</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">使用 GitHub 账号登录</p>
                </div>

                <a href="?action=login"
                   class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-900 dark:bg-gray-700 text-white rounded-lg hover:opacity-90 transition">
                    <i data-lucide="github" class="w-5 h-5"></i>
                    使用 GitHub 登录
                </a>

                <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                    仅限管理员访问
                </div>
            </div>

            <script>lucide.createIcons();</script>
        </body>
        </html>
        <?php
        break;
}
