import './bootstrap';
import './order-listener';

import Alpine from 'alpinejs';
import { requestPermission } from './push-notifications';
import { cooksSlider, testimonialsSlider } from './pages/landing';

window.Alpine = Alpine;
window.cooksSlider = cooksSlider;
window.testimonialsSlider = testimonialsSlider;

Alpine.data('cooksSlider', cooksSlider);
Alpine.data('testimonialsSlider', testimonialsSlider);

Alpine.start();

// Request permission if authenticated
if (window.isUserAuthenticated) {
    requestPermission();
}

document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll(".hover-card");
    let index = 0;
    let duration = 1500; // ms — tiempo que cada card queda "hovered"

    function animateCards() {
        // remover estado de todos
        cards.forEach(card => card.classList.remove("auto-hover"));

        // activar el actual
        cards[index].classList.add("auto-hover");

        // pasar al siguiente
        index = (index + 1) % cards.length;

        setTimeout(animateCards, duration);
    }

    if (cards.length > 1) {
        animateCards();
    }
});