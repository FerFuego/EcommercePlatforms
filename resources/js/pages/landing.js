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

export function initPreInscripcionesSection() {
    const section = document.getElementById('pre-inscripciones');
    if (!section) return;

    if ('IntersectionObserver' in window) {
        section.classList.add('pre-ready');
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        section.querySelectorAll('.pre-reveal').forEach((el) => io.observe(el));
    }
}

export function ratingExperience() {
    return {
        selectedRating: 5,
        hoverRating: 0,
        activeStoryIndex: 0,
        stories: [
            {
                name: 'Marta L.',
                location: 'Córdoba Capital',
                dish: 'Sorrentinos de calabaza y muzzarella',
                cook: 'Doña Rosa',
                score: '5.0',
                quote: '¡Masa fresca casera inolvidable! Llegó calentito y con salsa abundante. Se nota el amor con que cocinan.',
                time: 'Hace 12 min'
            },
            {
                name: 'Joaquín G.',
                location: 'Villa Allende',
                dish: 'Empanadas criollas al horno de barro',
                cook: 'Paula Nieva',
                score: '5.0',
                quote: 'El relleno es puro sabor de verdad. Mucho mejor que cualquier delivery común. Calificarlos con 5 estrellas es poco.',
                time: 'Hace 28 min'
            },
            {
                name: 'Carolina V.',
                location: 'Bell Ville',
                dish: 'Guiso de lentejas tradicional',
                cook: 'Soledad Pérez',
                score: '5.0',
                quote: 'Comida con gusto a infancia, sana y reconfortante. Da gusto apoyar a cocineros independientes del barrio.',
                time: 'Hace 45 min'
            }
        ],
        timer: null,
        init() {
            this.startLoop();
        },
        startLoop() {
            this.stopLoop();
            this.timer = setInterval(() => {
                this.activeStoryIndex = (this.activeStoryIndex + 1) % this.stories.length;
            }, 4200);
        },
        stopLoop() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },
        setRating(stars) {
            this.selectedRating = stars;
            this.stopLoop();
        },
        get currentScoreLabel() {
            const current = this.hoverRating || this.selectedRating;
            if (current === 5) return '¡Excelente! Sabor casero 10/10 ⭐';
            if (current === 4) return '¡Muy rico y fresco! 👌';
            if (current === 3) return 'Buen sabor casero 👍';
            if (current === 2) return 'Aceptable 🙂';
            return 'Para mejorar 💡';
        }
    };
}

// Registro global para Alpine.js
window.cooksSlider = cooksSlider;
window.testimonialsSlider = testimonialsSlider;
window.ratingExperience = ratingExperience;

if (window.Alpine) {
    window.Alpine.data('cooksSlider', cooksSlider);
    window.Alpine.data('testimonialsSlider', testimonialsSlider);
    window.Alpine.data('ratingExperience', ratingExperience);
} else {
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('cooksSlider', cooksSlider);
        window.Alpine.data('testimonialsSlider', testimonialsSlider);
        window.Alpine.data('ratingExperience', ratingExperience);
    });
}

// Inicialización de efectos DOM
function initLandingEffects() {
    initValuePropSection();
    initPreInscripcionesSection();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLandingEffects);
} else {
    initLandingEffects();
}

