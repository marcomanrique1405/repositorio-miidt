document.addEventListener('DOMContentLoaded', () => {

    const btn = document.getElementById('btn-filtros-toggle');
    const body = document.body;

    if (!btn) return;

    const MOBILE_WIDTH = 1024;

    const overlay = document.createElement('div');
    overlay.classList.add('menu-overlay');
    document.body.appendChild(overlay);

    function esMobile() {
        return window.innerWidth <= MOBILE_WIDTH;
    }

    function aplicarEstadoInicial() {
        if (esMobile()) {
            body.classList.add('menu-cerrado');
            body.classList.remove('menu-abierto');
        } else {
            body.classList.remove('menu-cerrado');
            body.classList.remove('menu-abierto');
            overlay.classList.remove('activo');
        }
    }

    function abrirMenu(){
        if (!esMobile()) return;

        body.classList.remove('menu-cerrado');
        body.classList.add('menu-abierto');
        overlay.classList.add('activo');
    }

    function cerrarMenu(){
        if (!esMobile()) return;

        body.classList.remove('menu-abierto');
        body.classList.add('menu-cerrado');
        overlay.classList.remove('activo');
    }

    btn.addEventListener('click', () => {

        if (!esMobile()) return;

        if(body.classList.contains('menu-abierto')){
            cerrarMenu();
        } else {
            abrirMenu();
        }
    });

    overlay.addEventListener('click', cerrarMenu);

    window.addEventListener('resize', aplicarEstadoInicial);

    aplicarEstadoInicial();

});