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
            vistaLista.classList.add('admin-hidden');
            vistaAgregar.classList.remove('admin-hidden');
        });
    }

    if (btnCancelar) {
        btnCancelar.addEventListener('click', function () {
            vistaAgregar.classList.add('admin-hidden');
            vistaLista.classList.remove('admin-hidden');
        });
    }
});