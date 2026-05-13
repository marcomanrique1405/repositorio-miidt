document.addEventListener('DOMContentLoaded', function () {
    const vistaLista = document.getElementById('vistaLista');
    const vistaAgregar = document.getElementById('vistaAgregar');
    const btnAgregar = document.getElementById('btnAgregarTesis');
    const btnCancelar = document.getElementById('btnCancelarAgregar');

    if (!vistaLista || !vistaAgregar) {
        console.error('No se encontraron vistaLista o vistaAgregar');
        return;
    }

    if (btnAgregar) {
        btnAgregar.addEventListener('click', function () {
            mostrarVistaAgregar();
        });
    }

    if (btnCancelar) {
        btnCancelar.addEventListener('click', function () {
            mostrarVistaLista();
        });
    }
});

/* ==========================================
   MOSTRAR VISTA AGREGAR
   Esta función queda global para poder usarla
   desde botones o desde otros scripts.
========================================== */
function mostrarVistaAgregar() {
    const vistaLista = document.getElementById('vistaLista');
    const vistaAgregar = document.getElementById('vistaAgregar');

    if (!vistaLista || !vistaAgregar) {
        console.error('No se encontraron vistaLista o vistaAgregar');
        return;
    }

    vistaLista.classList.add('admin-hidden');
    vistaAgregar.classList.remove('admin-hidden');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

/* ==========================================
   MOSTRAR VISTA LISTA
   Esta función es la que usa tu botón:
   onclick="mostrarVistaLista()"
========================================== */
function mostrarVistaLista() {
    const vistaLista = document.getElementById('vistaLista');
    const vistaAgregar = document.getElementById('vistaAgregar');

    if (!vistaLista || !vistaAgregar) {
        console.error('No se encontraron vistaLista o vistaAgregar');
        return;
    }

    vistaAgregar.classList.add('admin-hidden');
    vistaLista.classList.remove('admin-hidden');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}