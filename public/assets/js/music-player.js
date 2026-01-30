/**
 * 音乐播放器
 */

class MusicPlayer {
    constructor() {
        this.audio = new Audio();
        this.playlist = [];
        this.currentIndex = 0;
        this.isPlaying = false;
        this.progress = 0;

        this.init();
    }

    async init() {
        try {
            const response = await fetch('/api/music?action=playlist');
            this.playlist = await response.json();

            // 加载保存的播放状态
            this.loadState();

            // 绑定事件
            this.audio.addEventListener('timeupdate', () => this.onTimeUpdate());
            this.audio.addEventListener('ended', () => this.next());
            this.audio.addEventListener('play', () => {
                this.isPlaying = true;
                this.updatePlayButton();
            });
            this.audio.addEventListener('pause', () => {
                this.isPlaying = false;
                this.updatePlayButton();
            });

        } catch (error) {
            console.error('Failed to load playlist:', error);
        }
    }

    async play(index) {
        if (index !== undefined) {
            this.currentIndex = index;
        }

        const track = this.playlist[this.currentIndex];
        if (!track) return;

        try {
            const response = await fetch(`/api/music?action=url&id=${track.id}`);
            const data = await response.json();

            if (data.url) {
                this.audio.src = data.url;
                this.audio.play();
                this.updateNowPlaying(track);
            }
        } catch (error) {
            console.error('Failed to play track:', error);
        }
    }

    pause() {
        this.audio.pause();
    }

    toggle() {
        if (this.isPlaying) {
            this.pause();
        } else {
            this.play();
        }
    }

    next() {
        this.currentIndex = (this.currentIndex + 1) % this.playlist.length;
        this.play();
    }

    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.playlist.length) % this.playlist.length;
        this.play();
    }

    seek(percent) {
        if (this.audio.duration) {
            this.audio.currentTime = (percent / 100) * this.audio.duration;
        }
    }

    updateNowPlaying(track) {
        const titleEl = document.getElementById('music-title');
        const artistEl = document.getElementById('music-artist');
        const coverEl = document.getElementById('music-cover');

        if (titleEl) titleEl.textContent = track.name;
        if (artistEl) artistEl.textContent = track.artist;
        if (coverEl) coverEl.src = track.cover || '/assets/music/covers/music.jpg';

        // 保存当前播放
        localStorage.setItem('music-current', this.currentIndex);
    }

    onTimeUpdate() {
        const duration = this.audio.duration;
        const currentTime = this.audio.currentTime;

        if (duration) {
            const percent = (currentTime / duration) * 100;
            this.progress = percent;

            const progressEl = document.getElementById('music-progress');
            const currentEl = document.getElementById('music-current');
            const durationEl = document.getElementById('music-duration');

            if (progressEl) progressEl.style.width = `${percent}%`;
            if (currentEl) currentEl.textContent = this.formatTime(currentTime);
            if (durationEl) durationEl.textContent = this.formatTime(duration);
        }
    }

    updatePlayButton() {
        const icon = document.getElementById('music-play-icon');
        if (icon) {
            icon.setAttribute('data-lucide', this.isPlaying ? 'pause' : 'play');
            lucide?.createIcons();
        }
    }

    formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }

    loadState() {
        const current = localStorage.getItem('music-current');
        if (current !== null) {
            this.currentIndex = parseInt(current);
        }

        const volume = localStorage.getItem('music-volume');
        if (volume !== null) {
            this.audio.volume = parseFloat(volume);
        }
    }

    setVolume(volume) {
        this.audio.volume = volume;
        localStorage.setItem('music-volume', volume);
    }
}

// 全局实例
let musicPlayer;

// 初始化
document.addEventListener('DOMContentLoaded', () => {
    musicPlayer = new MusicPlayer();
});

// 全局函数
window.toggleMusicPlayer = function() {
    const modal = document.getElementById('music-modal');
    if (modal) {
        modal.classList.toggle('hidden');
    }
}
