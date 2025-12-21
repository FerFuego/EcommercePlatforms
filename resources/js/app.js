import './bootstrap';
import './order-listener';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

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