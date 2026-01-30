/**
 * 平滑滚动 (Lenis)
 */

// 初始化 Lenis
const lenis = new Lenis({
    duration: 1.2,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    direction: 'vertical',
    gestureDirection: 'vertical',
    smooth: true,
    mouseMultiplier: 1,
    smoothTouch: false,
    touchMultiplier: 2,
    infinite: false,
});

// RAF 循环
function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
}

requestAnimationFrame(raf);

// 页面内锚点平滑滚动
document.addEventListener('click', (e) => {
    const target = e.target.closest('a[href^="#"]');
    if (target) {
        e.preventDefault();
        const id = target.getAttribute('href').slice(1);
        const element = document.getElementById(id);
        if (element) {
            lenis.scrollTo(element);
        }
    }
});
