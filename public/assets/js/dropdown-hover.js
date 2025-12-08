document.addEventListener('DOMContentLoaded', function() {
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    
    if (!isTouchDevice) {
        // Desktop: hover
        const dropdowns = document.querySelectorAll('.navbar .dropdown');
        
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('mouseenter', function() {
                const menu = this.querySelector('.dropdown-menu');
                const link = this.querySelector('.nav-link');
                if (menu) menu.classList.add('show');
                if (link) link.setAttribute('aria-expanded', 'true');
            });
            
            dropdown.addEventListener('mouseleave', function() {
                const menu = this.querySelector('.dropdown-menu');
                const link = this.querySelector('.nav-link');
                if (menu) menu.classList.remove('show');
                if (link) link.setAttribute('aria-expanded', 'false');
            });
        });
    } else {
        // Mobile: Bootstrap ya maneja el click
        console.log('Modo táctil activado');
    }
});