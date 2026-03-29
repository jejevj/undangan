/**
 * Template: Sanno — app.js v1.0.0
 * Corporate Event Template
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Reveal on Scroll ──────────────────────────────────────────
    const revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => { 
                if (e.isIntersecting) { 
                    e.target.classList.add('revealed'); 
                    obs.unobserve(e.target); 
                } 
            });
        }, { threshold: 0.12 });
        revealEls.forEach(el => obs.observe(el));
    } else {
        revealEls.forEach(el => el.classList.add('revealed'));
    }

    // ── Countdown Timer ───────────────────────────────────────────
    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
        const target = new Date(countdownEl.dataset.date).getTime();
        function tick() {
            const diff = target - Date.now();
            if (diff <= 0) { 
                countdownEl.innerHTML = '<span class="countdown-done">Acara Telah Dimulai 🎉</span>'; 
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
        tick(); 
        setInterval(tick, 1000);
    }

    // ── Smooth Scroll ─────────────────────────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const t = document.querySelector(a.getAttribute('href'));
            if (t) { 
                e.preventDefault(); 
                t.scrollIntoView({ behavior: 'smooth', block: 'start' }); 
            }
        });
    });

    // ── Active Navigation ─────────────────────────────────────────

    const sections = document.querySelectorAll('section[id]');
    const navItems = document.querySelectorAll('.nav-item[data-section]');
    if (sections.length && 'IntersectionObserver' in window) {
        sections.forEach(s => {
            new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        navItems.forEach(n => n.classList.remove('active'));
                        document.querySelector(`.nav-item[data-section="${e.target.id}"]`)?.classList.add('active');
                    }
                });
            }, { threshold: 0.4 }).observe(s);
        });
    }

    // ── Music Player ──────────────────────────────────────────────
    const audio = document.getElementById('bgMusic');
    const fab   = document.getElementById('musicFab');
    if (audio && fab) {
        audio.volume = 0.5;
        function setPlaying(s) { 
            fab.classList.toggle('playing', s); 
            fab.classList.toggle('paused', !s); 
        }
        audio.play().then(() => setPlaying(true)).catch(() => {
            setPlaying(false);
            const start = () => { 
                audio.play().then(() => setPlaying(true)).catch(() => {}); 
            };
            ['click','touchstart','scroll'].forEach(ev => 
                document.addEventListener(ev, start, { once: true })
            );
        });
        fab.addEventListener('click', () => {
            audio.paused 
                ? audio.play().then(() => setPlaying(true)).catch(() => {}) 
                : (audio.pause(), setPlaying(false));
        });
    }

}); // end DOMContentLoaded

// ── Copy to Clipboard ─────────────────────────────────────────────
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = '✅';
        setTimeout(() => btn.textContent = orig, 2000);
    });
}

// ── Lightbox Gallery ──────────────────────────────────────────────
function openLightbox(src, caption) {
    const lb = document.getElementById('lightbox');
    if (lb) {
        document.getElementById('lightboxImg').src = src;
        lb.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}

function closeLightbox() {
    const lb = document.getElementById('lightbox');
    if (lb) {
        lb.classList.remove('open');
        document.body.style.overflow = '';
    }
}

document.addEventListener('keydown', e => { 
    if (e.key === 'Escape') closeLightbox(); 
});
