/**
 * 通用工具函数
 */

// 防抖函数
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// 节流函数
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// 复制到剪贴板
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        return true;
    } catch {
        // 降级方案
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            document.body.removeChild(textarea);
            return true;
        } catch {
            document.body.removeChild(textarea);
            return false;
        }
    }
}

// 格式化日期
function formatDate(date, format = 'YYYY-MM-DD') {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');

    return format
        .replace('YYYY', year)
        .replace('MM', month)
        .replace('DD', day)
        .replace('HH', hours)
        .replace('mm', minutes);
}

// 相对时间（多久之前）
function timeAgo(date) {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);

    const intervals = {
        年: 31536000,
        月: 2592000,
        周: 604800,
        天: 86400,
        小时: 3600,
        分钟: 60
    };

    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return `${interval} ${unit}前`;
        }
    }

    return '刚刚';
}

// 代码块复制按钮
function initCodeBlocks() {
    document.querySelectorAll('pre[class*="language-"]').forEach(pre => {
        const wrapper = document.createElement('div');
        wrapper.className = 'relative';
        pre.parentNode.insertBefore(wrapper, pre);
        wrapper.appendChild(pre);

        const button = document.createElement('button');
        button.className = 'absolute top-2 right-2 p-2 bg-accent hover:bg-accent/80 rounded text-xs opacity-0 group-hover:opacity-100 transition';
        button.textContent = '复制';
        button.style.opacity = '0';

        pre.classList.add('group');
        pre.addEventListener('mouseenter', () => button.style.opacity = '1');
        pre.addEventListener('mouseleave', () => button.style.opacity = '0');

        button.addEventListener('click', async () => {
            const code = pre.querySelector('code').textContent;
            const success = await copyToClipboard(code);
            button.textContent = success ? '已复制!' : '复制失败';
            setTimeout(() => button.textContent = '复制', 2000);
        });

        wrapper.appendChild(button);
    });
}

// 图片懒加载
function initLazyImages() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// 外链处理
function initExternalLinks() {
    document.querySelectorAll('a[href^="http"]').forEach(link => {
        if (!link.hostname.includes(window.location.hostname)) {
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
        }
    });
}

// 初始化所有功能
document.addEventListener('DOMContentLoaded', () => {
    initCodeBlocks();
    initLazyImages();
    initExternalLinks();
});

// 页面加载完成后
window.addEventListener('load', () => {
    // 移除加载动画（如果有）
    document.body.classList.remove('loading');
});
