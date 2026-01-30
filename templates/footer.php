    </main>

    <!-- 页脚 -->
    <footer class="border-t mt-20">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- 关于 -->
                <div>
                    <h3 class="font-bold mb-4">关于</h3>
                    <p class="text-sm text-muted-foreground mb-4">
                        <?= getSetting('site.description') ?>
                    </p>
                    <div class="flex space-x-4">
                        <?php if (getSetting('footer.github')): ?>
                        <a href="<?= getSetting('footer.github') ?>" target="_blank" rel="noopener" class="text-muted-foreground hover:text-primary transition">
                            <i data-lucide="github" class="w-5 h-5"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (getSetting('footer.email')): ?>
                        <a href="mailto:<?= getSetting('footer.email') ?>" class="text-muted-foreground hover:text-primary transition">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 链接 -->
                <div>
                    <h3 class="font-bold mb-4">链接</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?= url('archive') ?>" class="text-muted-foreground hover:text-primary transition">归档</a></li>
                        <li><a href="<?= url('about') ?>" class="text-muted-foreground hover:text-primary transition">关于</a></li>
                        <li><a href="<?= url('rss') ?>" class="text-muted-foreground hover:text-primary transition">RSS</a></li>
                    </ul>
                </div>

                <!-- 统计 -->
                <div>
                    <h3 class="font-bold mb-4">统计</h3>
                    <ul class="space-y-2 text-sm text-muted-foreground">
                        <li>文章数: <?= getSetting('stats.totalPosts', 0) ?></li>
                        <li>总字数: <?= number_format(getSetting('stats.totalWords', 0)) ?></li>
                        <li>今年: <?= getSetting('stats.thisYearCount', 0) ?> 篇</li>
                    </ul>
                </div>
            </div>

            <!-- 版权信息 -->
            <div class="mt-8 pt-8 border-t text-center text-sm text-muted-foreground">
                <?php if (getSetting('footer.icp')): ?>
                <p><?= getSetting('footer.icp') ?></p>
                <?php endif; ?>
                <p class="mt-2">&copy; <?= date('Y') ?> <?= getSetting('site.author') ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- 音乐播放器（全局） -->
    <div id="music-player" class="fixed bottom-4 right-4 z-40">
        <!-- 音乐控制 -->
        <button onclick="toggleMusicPlayer()" class="p-3 bg-primary text-primary-foreground rounded-full shadow-lg hover:scale-105 transition">
            <i data-lucide="music" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- 外观设置面板 -->
    <div id="appearance-panel" class="fixed top-20 right-4 z-50 hidden">
        <div class="bg-card border rounded-lg shadow-xl p-6 w-72">
            <h3 class="font-bold mb-4">外观设置</h3>

            <!-- 主题 -->
            <div class="mb-4">
                <label class="text-sm font-medium mb-2 block">主题</label>
                <div class="grid grid-cols-3 gap-2">
                    <button onclick="setTheme('light')" class="p-2 border rounded hover:bg-accent transition text-sm">浅色</button>
                    <button onclick="setTheme('dark')" class="p-2 border rounded hover:bg-accent transition text-sm">深色</button>
                    <button onclick="setTheme('system')" class="p-2 border rounded hover:bg-accent transition text-sm">跟随系统</button>
                </div>
            </div>

            <!-- 背景图片 -->
            <div class="mb-4">
                <label class="text-sm font-medium mb-2 block">背景</label>
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="setBackground('none')" class="p-2 border rounded hover:bg-accent transition text-sm">无</button>
                    <button onclick="setBackground('character')" class="p-2 border rounded hover:bg-accent transition text-sm">角色</button>
                </div>
            </div>

            <!-- 布局 -->
            <div class="mb-4">
                <label class="text-sm font-medium mb-2 block">布局</label>
                <div class="grid grid-cols-3 gap-2">
                    <button onclick="setLayout('compact')" class="p-2 border rounded hover:bg-accent transition text-sm">紧凑</button>
                    <button onclick="setLayout('default')" class="p-2 border rounded hover:bg-accent transition text-sm">默认</button>
                    <button onclick="setLayout('wide')" class="p-2 border rounded hover:bg-accent transition text-sm">宽屏</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 搜索模态框 -->
    <div id="search-modal" class="fixed inset-0 z-50 hidden bg-background/80 backdrop-blur-sm">
        <div class="container mx-auto px-4 py-20">
            <div class="max-w-2xl mx-auto">
                <div class="bg-card border rounded-lg shadow-xl">
                    <div class="p-4">
                        <div class="flex items-center space-x-4">
                            <i data-lucide="search" class="w-5 h-5 text-muted-foreground"></i>
                            <input
                                type="text"
                                id="search-input"
                                placeholder="搜索文章..."
                                class="flex-1 bg-transparent border-none outline-none text-lg"
                                oninput="handleSearch(this.value)"
                            >
                            <button onclick="closeSearch()" class="p-2 hover:bg-accent rounded-lg transition">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                    <div id="search-results" class="border-t max-h-96 overflow-y-auto">
                        <!-- 搜索结果将在这里显示 -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 音乐播放器模态框 -->
    <div id="music-modal" class="fixed inset-0 z-50 hidden bg-background/80 backdrop-blur-sm">
        <div class="container mx-auto px-4 py-20">
            <div class="max-w-md mx-auto bg-card border rounded-lg shadow-xl">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold">正在播放</h3>
                        <button onclick="toggleMusicPlayer()" class="p-2 hover:bg-accent rounded-lg transition">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- 封面 -->
                    <div class="mb-6">
                        <img id="music-cover" src="<?= asset('music/covers/music.jpg') ?>" alt="专辑封面" class="w-full rounded-lg shadow-lg">
                    </div>

                    <!-- 信息 -->
                    <div class="text-center mb-6">
                        <h4 id="music-title" class="text-lg font-bold mb-1">未播放</h4>
                        <p id="music-artist" class="text-sm text-muted-foreground">-</p>
                    </div>

                    <!-- 进度条 -->
                    <div class="mb-4">
                        <div class="bg-accent rounded-full h-1">
                            <div id="music-progress" class="bg-primary h-1 rounded-full" style="width: 0%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-muted-foreground mt-1">
                            <span id="music-current">0:00</span>
                            <span id="music-duration">0:00</span>
                        </div>
                    </div>

                    <!-- 控制 -->
                    <div class="flex justify-center items-center space-x-4">
                        <button onclick="musicPlayer.prev()" class="p-2 hover:bg-accent rounded-lg transition">
                            <i data-lucide="skip-back" class="w-6 h-6"></i>
                        </button>
                        <button onclick="musicPlayer.toggle()" class="p-4 bg-primary text-primary-foreground rounded-full hover:scale-105 transition">
                            <i data-lucide="play" id="music-play-icon" class="w-8 h-8"></i>
                        </button>
                        <button onclick="musicPlayer.next()" class="p-2 hover:bg-accent rounded-lg transition">
                            <i data-lucide="skip-forward" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // 初始化 Lucide 图标
        lucide.createIcons();

        // 初始化平滑滚动
        const lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        });

        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        // 主题管理
        const appearanceConfig = JSON.parse(localStorage.getItem('appearance-config') || '{}');

        function applyTheme(theme) {
            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        function applyBackground(bg) {
            document.body.classList.remove('bg-character');
            if (bg === 'character') {
                document.body.classList.add('bg-character');
            }
        }

        function applyLayout(layout) {
            document.body.classList.remove('layout-compact', 'layout-default', 'layout-wide');
            document.body.classList.add('layout-' + layout);
        }

        // 应用保存的设置
        applyTheme(appearanceConfig.theme || 'system');
        applyBackground(appearanceConfig.background || 'none');
        applyLayout(appearanceConfig.layout || 'default');

        // 主题切换
        function toggleTheme() {
            const current = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = current === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        }

        function setTheme(theme) {
            appearanceConfig.theme = theme;
            localStorage.setItem('appearance-config', JSON.stringify(appearanceConfig));
            applyTheme(theme);
        }

        function setBackground(bg) {
            appearanceConfig.background = bg;
            localStorage.setItem('appearance-config', JSON.stringify(appearanceConfig));
            applyBackground(bg);
        }

        function setLayout(layout) {
            appearanceConfig.layout = layout;
            localStorage.setItem('appearance-config', JSON.stringify(appearanceConfig));
            applyLayout(layout);
        }

        // 外观面板
        function toggleAppearance() {
            const panel = document.getElementById('appearance-panel');
            panel.classList.toggle('hidden');
        }

        // 移动端菜单
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // 搜索功能
        let searchTimeout;

        function openSearch() {
            document.getElementById('search-modal').classList.remove('hidden');
            document.getElementById('search-input').focus();
        }

        function closeSearch() {
            document.getElementById('search-modal').classList.add('hidden');
            document.getElementById('search-input').value = '';
            document.getElementById('search-results').innerHTML = '';
        }

        async function handleSearch(query) {
            clearTimeout(searchTimeout);

            if (query.length < 2) {
                document.getElementById('search-results').innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(async () => {
                const response = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
                const results = await response.json();

                const resultsContainer = document.getElementById('search-results');

                if (results.length === 0) {
                    resultsContainer.innerHTML = '<div class="p-4 text-center text-muted-foreground">未找到相关文章</div>';
                    return;
                }

                resultsContainer.innerHTML = results.map(post => `
                    <a href="/post/${post.id}" class="block p-4 hover:bg-accent transition border-b last:border-b-0">
                        <h4 class="font-medium mb-1">${post.title}</h4>
                        <p class="text-sm text-muted-foreground mb-2">${post.excerpt || ''}</p>
                        <div class="text-xs text-muted-foreground">
                            <span>${post.category || '未分类'}</span>
                            <span class="mx-2">·</span>
                            <span>${new Date(post.date).toLocaleDateString('zh-CN')}</span>
                        </div>
                    </a>
                `).join('');
            }, 300);
        }

        // ESC 关闭搜索
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeSearch();
            }
            if (e.key === 'k' && (e.metaKey || e.ctrlKey)) {
                e.preventDefault();
                openSearch();
            }
        });

        // 音乐播放器
        const musicPlayer = {
            audio: new Audio(),
            playlist: [],
            currentIndex: 0,
            isPlaying: false,

            async init() {
                const response = await fetch('/api/music?action=playlist');
                this.playlist = await response.json();
            },

            async play(index) {
                if (index !== undefined) this.currentIndex = index;

                const track = this.playlist[this.currentIndex];
                const urlResponse = await fetch(`/api/music?action=url&id=${track.id}`);
                const urlData = await urlResponse.json();

                this.audio.src = urlData.url;
                this.audio.play();
                this.isPlaying = true;

                this.updateUI(track);
            },

            pause() {
                this.audio.pause();
                this.isPlaying = false;
                this.updatePlayIcon();
            },

            toggle() {
                if (this.isPlaying) {
                    this.pause();
                } else {
                    this.play();
                }
            },

            next() {
                this.currentIndex = (this.currentIndex + 1) % this.playlist.length;
                this.play();
            },

            prev() {
                this.currentIndex = (this.currentIndex - 1 + this.playlist.length) % this.playlist.length;
                this.play();
            },

            updateUI(track) {
                document.getElementById('music-title').textContent = track.name;
                document.getElementById('music-artist').textContent = track.artist;
                document.getElementById('music-cover').src = track.cover || '<?= asset("music/covers/music.jpg") ?>';
                this.updatePlayIcon();
            },

            updatePlayIcon() {
                const icon = document.getElementById('music-play-icon');
                icon.setAttribute('data-lucide', this.isPlaying ? 'pause' : 'play');
                lucide.createIcons();
            }
        };

        function toggleMusicPlayer() {
            document.getElementById('music-modal').classList.toggle('hidden');
        }

        // 初始化音乐播放器
        musicPlayer.init();

        // 点击外部关闭面板
        document.addEventListener('click', (e) => {
            const appearancePanel = document.getElementById('appearance-panel');
            if (!e.target.closest('#appearance-panel') && !e.target.closest('[onclick="toggleAppearance()"]')) {
                appearancePanel.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
