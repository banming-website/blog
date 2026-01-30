/**
 * 主题切换功能
 */

(function() {
    const STORAGE_KEY = 'appearance-config';

    // 获取保存的配置
    function getConfig() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        } catch {
            return {};
        }
    }

    // 保存配置
    function saveConfig(config) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(config));
    }

    // 应用主题
    function applyTheme(theme) {
        const html = document.documentElement;

        if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
    }

    // 应用背景
    function applyBackground(background) {
        document.body.classList.remove('bg-character');
        if (background === 'character') {
            document.body.classList.add('bg-character');
        }
    }

    // 应用布局
    function applyLayout(layout) {
        document.body.classList.remove('layout-compact', 'layout-default', 'layout-wide');
        document.body.classList.add('layout-' + layout);
    }

    // 初始化
    function init() {
        const config = getConfig();
        applyTheme(config.theme || 'system');
        applyBackground(config.background || 'none');
        applyLayout(config.layout || 'default');

        // 监听系统主题变化
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (getConfig().theme === 'system') {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    // 切换主题
    window.toggleTheme = function() {
        const config = getConfig();
        const isDark = document.documentElement.classList.contains('dark');
        config.theme = isDark ? 'light' : 'dark';
        saveConfig(config);
        applyTheme(config.theme);
    }

    // 设置主题
    window.setTheme = function(theme) {
        const config = getConfig();
        config.theme = theme;
        saveConfig(config);
        applyTheme(theme);
    }

    // 设置背景
    window.setBackground = function(background) {
        const config = getConfig();
        config.background = background;
        saveConfig(config);
        applyBackground(background);
    }

    // 设置布局
    window.setLayout = function(layout) {
        const config = getConfig();
        config.layout = layout;
        saveConfig(config);
        applyLayout(layout);
    }

    // 启动
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
