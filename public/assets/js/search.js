/**
 * 搜索功能
 */

// 全局搜索实例
const Search = {
    timeout: null,
    results: [],

    open() {
        const modal = document.getElementById('search-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.getElementById('search-input')?.focus();
        }
    },

    close() {
        const modal = document.getElementById('search-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
        this.clearResults();
    },

    async search(query) {
        clearTimeout(this.timeout);

        if (query.length < 2) {
            this.clearResults();
            return;
        }

        this.timeout = setTimeout(async () => {
            try {
                const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
                this.results = await response.json();
                this.renderResults();
            } catch (error) {
                console.error('Search failed:', error);
            }
        }, 300);
    },

    renderResults() {
        const container = document.getElementById('search-results');
        if (!container) return;

        if (this.results.length === 0) {
            container.innerHTML = '<div class="p-4 text-center text-muted-foreground">未找到相关文章</div>';
            return;
        }

        container.innerHTML = this.results.map(post => `
            <a href="/post/${post.id}" class="block p-4 hover:bg-accent transition border-b last:border-b-0">
                <h4 class="font-medium mb-1">${this.escapeHtml(post.title)}</h4>
                <p class="text-sm text-muted-foreground mb-2">${this.escapeHtml(post.excerpt || '')}</p>
                <div class="text-xs text-muted-foreground">
                    <span>${post.category || '未分类'}</span>
                    <span class="mx-2">·</span>
                    <span>${new Date(post.date).toLocaleDateString('zh-CN')}</span>
                </div>
            </a>
        `).join('');
    },

    clearResults() {
        const container = document.getElementById('search-results');
        const input = document.getElementById('search-input');

        if (container) container.innerHTML = '';
        if (input) input.value = '';
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// 全局函数
window.openSearch = function() {
    Search.open();
}

window.closeSearch = function() {
    Search.close();
}

window.handleSearch = function(query) {
    Search.search(query);
}

// 键盘快捷键
document.addEventListener('keydown', (e) => {
    // ESC 关闭搜索
    if (e.key === 'Escape') {
        Search.close();
    }

    // Cmd/Ctrl + K 打开搜索
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        Search.open();
    }
});

// 搜索框事件
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            Search.search(e.target.value);
        });
    }
});
