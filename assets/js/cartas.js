document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll(".flip-card");

    cards.forEach(card => {
        card.addEventListener("click", function () {

            // Solo activar tap en pantalla móvil
            if (window.innerWidth <= 768) {
                this.classList.toggle("active-card");
            }
        });
    });
});
