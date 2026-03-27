/**
 * Template: Premium White 1 — app.js v1.1.0
 */

document.addEventListener('DOMContentLoaded', function () {

    // ══════════════════════════════════════════════════════════════════
    // REVEAL ON SCROLL
    // ══════════════════════════════════════════════════════════════════
    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        revealEls.forEach(el => observer.observe(el));
    } else {
        revealEls.forEach(el => el.classList.add('revealed'));
    }

    // ══════════════════════════════════════════════════════════════════
    // COUNTDOWN TIMER
    // ══════════════════════════════════════════════════════════════════
    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
        const target = new Date(countdownEl.dataset.date).getTime();

        function updateCountdown() {
            const diff = target - Date.now();
            if (diff <= 0) {
                countdownEl.innerHTML = '<span class="countdown-done">Hari Bahagia Telah Tiba 🎉</span>';
                return;
            }
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
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    // ══════════════════════════════════════════════════════════════════
    // SMOOTH SCROLL
    // ══════════════════════════════════════════════════════════════════
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ══════════════════════════════════════════════════════════════════
    // BOTTOM NAVBAR — active section highlight
    // ══════════════════════════════════════════════════════════════════
    const sections = document.querySelectorAll('section[id]');
    const navItems = document.querySelectorAll('.nav-item[data-section]');

    if (sections.length && navItems.length) {
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    navItems.forEach(n => n.classList.remove('active'));
                    const active = document.querySelector(`.nav-item[data-section="${entry.target.id}"]`);
                    if (active) active.classList.add('active');
                }
            });
        }, { threshold: 0.4 });

        sections.forEach(s => sectionObserver.observe(s));
    }

    // ══════════════════════════════════════════════════════════════════
    // MUSIC FAB — autoplay, toggle play/pause
    // ══════════════════════════════════════════════════════════════════
    const audio = document.getElementById('bgMusic');
    const fab   = document.getElementById('musicFab');

    if (!audio || !fab) return;

    audio.volume = 0.6;

    function setPlaying(state) {
        fab.classList.toggle('playing', state);
        fab.classList.toggle('paused',  !state);
    }

    // Coba autoplay langsung
    audio.play().then(() => {
        setPlaying(true);
    }).catch(() => {
        // Browser blokir — tunggu interaksi pertama
        setPlaying(false);
        const start = () => {
            audio.play().then(() => setPlaying(true)).catch(() => {});
            document.removeEventListener('click',      start);
            document.removeEventListener('touchstart', start);
            document.removeEventListener('scroll',     start);
        };
        document.addEventListener('click',      start, { once: true });
        document.addEventListener('touchstart', start, { once: true });
        document.addEventListener('scroll',     start, { once: true });
    });

    // Klik FAB: toggle play/pause
    fab.addEventListener('click', () => {
        if (audio.paused) {
            audio.play().then(() => setPlaying(true)).catch(() => {});
        } else {
            audio.pause();
            setPlaying(false);
        }
    });
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
    const lb  = document.getElementById('lightbox');
    const img = document.getElementById('lightboxImg');
    img.src = src;
    img.alt = caption || '';
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeLightbox();
});

// ── Slideshow ─────────────────────────────────────────────────────────
(function () {
    const track = document.getElementById('slideshowTrack');
    if (!track) return;

    const slides = track.querySelectorAll('.slide');
    const dots   = document.querySelectorAll('#slideDots .dot');
    let current  = 0;
    let timer;

    function goToSlide(n) {
        current = (n + slides.length) % slides.length;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((d, i) => d.classList.toggle('active', i === current));
    }

    window.goToSlide = goToSlide;
    window.slideNext = () => goToSlide(current + 1);
    window.slidePrev = () => goToSlide(current - 1);

    function startAuto() { timer = setInterval(() => goToSlide(current + 1), 4000); }
    function stopAuto()  { clearInterval(timer); }

    const container = track.closest('.slideshow');
    container?.addEventListener('mouseenter', stopAuto);
    container?.addEventListener('mouseleave', startAuto);
    container?.addEventListener('touchstart', stopAuto, { passive: true });

    startAuto();
})();
