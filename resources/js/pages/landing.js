/**
 * Landing Page - Cocinarte
 * resources/js/pages/landing.js
 */

export function cooksSlider() {
    return {
        timer: null,
        scrollSlider(direction) {
            const container = this.$refs.cardsContainer;
            if (!container) return;
            const cardWidth = container.querySelector('.cook-card')?.offsetWidth || 300;
            const maxScroll = container.scrollWidth - container.clientWidth;

            if (maxScroll <= 10) return;

            // Loop infinito suave: al llegar al final vuelve al inicio
            if (direction > 0 && container.scrollLeft >= maxScroll - 20) {
                container.scrollTo({ left: 0, behavior: 'smooth' });
            } else if (direction < 0 && container.scrollLeft <= 20) {
                container.scrollTo({ left: maxScroll, behavior: 'smooth' });
            } else {
                container.scrollBy({ left: direction * (cardWidth + 20), behavior: 'smooth' });
            }
        },
        startAutoplay() {
            this.stopAutoplay();
            this.timer = setInterval(() => {
                this.scrollSlider(1);
            }, 4000);
        },
        stopAutoplay() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        }
    };
}

export function testimonialsSlider(items = []) {
    return {
        current: 0,
        timer: null,
        items: Array.isArray(items) ? items : [],
        startAutoplay() {
            this.stopAutoplay();
            if (!this.items || this.items.length <= 1) return;
            this.timer = setInterval(() => {
                this.current = (this.current + 1) % this.items.length;
            }, 4500);
        },
        stopAutoplay() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        }
    };
}

export function initValuePropSection() {
    const section = document.getElementById('que-ofrecemos');
    if (!section) return;

    if ('IntersectionObserver' in window) {
        section.classList.add('vp-ready');
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
        section.querySelectorAll('.vp-reveal').forEach((el) => io.observe(el));
    }

    section.querySelectorAll('.vp-card').forEach((card) => {
        card.addEventListener('pointermove', (ev) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', (ev.clientX - rect.left) + 'px');
            card.style.setProperty('--my', (ev.clientY - rect.top) + 'px');
        });
    });
}

// Registro global para Alpine.js
window.cooksSlider = cooksSlider;
window.testimonialsSlider = testimonialsSlider;

if (window.Alpine) {
    window.Alpine.data('cooksSlider', cooksSlider);
    window.Alpine.data('testimonialsSlider', testimonialsSlider);
} else {
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('cooksSlider', cooksSlider);
        window.Alpine.data('testimonialsSlider', testimonialsSlider);
    });
}

// Inicialización de efectos DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initValuePropSection);
} else {
    initValuePropSection();
}
