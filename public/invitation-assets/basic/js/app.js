/**
 * Template: Basic — app.js v1.0.0
 */

document.addEventListener('DOMContentLoaded', function () {

    // Reveal on scroll
    const revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); obs.unobserve(e.target); } });
        }, { threshold: 0.12 });
        revealEls.forEach(el => obs.observe(el));
    } else {
        revealEls.forEach(el => el.classList.add('revealed'));
    }

    // Countdown
    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
        const target = new Date(countdownEl.dataset.date).getTime();
        function tick() {
            const diff = target - Date.now();
            if (diff <= 0) { countdownEl.innerHTML = '<span class="countdown-done">Hari Bahagia Telah Tiba 🎉</span>'; return; }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            countdownEl.innerHTML = `
                <div class="countdown-item"><span>${d}</span><small>Hari</small></div>
                <div class="countdown-item"><span>${h}</span><small>Jam</small></div>
                <div class="countdown-item"><span>${m}</span><small>Menit</small></div>
                <div class="countdown-item"><span>${s}</span><small>Detik</small></div>`;
        }
        tick(); setInterval(tick, 1000);
    }

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const t = document.querySelector(a.getAttribute('href'));
            if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });

    // Active nav
    const sections = document.querySelectorAll('section[id]');
    const navItems = document.querySelectorAll('.nav-item[data-section]');
    if (sections.length && 'IntersectionObserver' in window) {
        new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    navItems.forEach(n => n.classList.remove('active'));
                    document.querySelector(`.nav-item[data-section="${e.target.id}"]`)?.classList.add('active');
                }
            });
        }, { threshold: 0.4 }).observe && sections.forEach(s =>
            new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        navItems.forEach(n => n.classList.remove('active'));
                        document.querySelector(`.nav-item[data-section="${e.target.id}"]`)?.classList.add('active');
                    }
                });
            }, { threshold: 0.4 }).observe(s)
        );
    }

    // Music FAB
    const audio = document.getElementById('bgMusic');
    const fab   = document.getElementById('musicFab');
    if (audio && fab) {
        audio.volume = 0.6;
        function setPlaying(s) { fab.classList.toggle('playing', s); fab.classList.toggle('paused', !s); }
        audio.play().then(() => setPlaying(true)).catch(() => {
            setPlaying(false);
            const start = () => { audio.play().then(() => setPlaying(true)).catch(() => {}); };
            ['click','touchstart','scroll'].forEach(ev => document.addEventListener(ev, start, { once: true }));
        });
        fab.addEventListener('click', () => {
            audio.paused ? audio.play().then(() => setPlaying(true)).catch(() => {}) : (audio.pause(), setPlaying(false));
        });
    }

}); // end DOMContentLoaded

// Copy rekening
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = '✅';
        setTimeout(() => btn.textContent = orig, 2000);
    });
}

// Lightbox
function openLightbox(src, caption) {
    const lb = document.getElementById('lightbox');
    document.getElementById('lightboxImg').src = src;
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

// Slideshow
(function () {
    const track = document.getElementById('slideshowTrack');
    if (!track) return;
    const slides = track.querySelectorAll('.slide');
    const dots   = document.querySelectorAll('#slideDots .dot');
    let current  = 0, timer;

    function goToSlide(n) {
        current = (n + slides.length) % slides.length;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((d, i) => d.classList.toggle('active', i === current));
    }

    window.goToSlide = goToSlide;
    window.slideNext = () => goToSlide(current + 1);
    window.slidePrev = () => goToSlide(current - 1);

    const container = track.closest('.slideshow');
    const startAuto = () => { timer = setInterval(() => goToSlide(current + 1), 4000); };
    const stopAuto  = () => clearInterval(timer);

    container?.addEventListener('mouseenter', stopAuto);
    container?.addEventListener('mouseleave', startAuto);
    container?.addEventListener('touchstart', stopAuto, { passive: true });
    startAuto();
})();
