document.addEventListener('DOMContentLoaded', function () {
    const vistaLista = document.getElementById('vistaLista');
    const vistaAgregar = document.getElementById('vistaAgregar');
    const vistaEditar = document.getElementById('vistaEditar');

    const btnAgregar = document.getElementById('btnAgregarTesis');
    const btnCancelar = document.getElementById('btnCancelarAgregar');

    if (!vistaLista || !vistaAgregar) {
        console.error('No se encontraron vistaLista o vistaAgregar');
        return;
    }

    /*
       Estado inicial:
       Siempre iniciamos en vista lista.
       No tocamos sidebar, topbar ni estadísticas.
    */
    document.body.classList.add('admin-vista-lista');
    document.body.classList.remove('admin-vista-agregar', 'admin-vista-editar');

    vistaLista.classList.remove('admin-hidden');
    vistaAgregar.classList.add('admin-hidden');

    if (vistaEditar) {
        vistaEditar.classList.add('admin-hidden');
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
   Solo cambia la vista central.
   NO oculta menú, filtros, topbar ni stats.
========================================== */
function mostrarVistaAgregar() {
    const vistaLista = document.getElementById('vistaLista');
    const vistaAgregar = document.getElementById('vistaAgregar');
    const vistaEditar = document.getElementById('vistaEditar');

    if (!vistaLista || !vistaAgregar) {
        console.error('No se encontraron vistaLista o vistaAgregar');
        return;
    }

    document.body.classList.remove('admin-vista-lista', 'admin-vista-editar');
    document.body.classList.add('admin-vista-agregar');

    vistaLista.classList.add('admin-hidden');
    vistaAgregar.classList.remove('admin-hidden');

    if (vistaEditar) {
        vistaEditar.classList.add('admin-hidden');
    }

    subirArribaDashboard();
}

/* ==========================================
   MOSTRAR VISTA EDITAR
   Solo cambia la vista central.
   NO oculta menú, filtros, topbar ni stats.
========================================== */
function mostrarVistaEditar() {
    const vistaLista = document.getElementById('vistaLista');
    const vistaAgregar = document.getElementById('vistaAgregar');
    const vistaEditar = document.getElementById('vistaEditar');

    if (!vistaLista || !vistaEditar) {
        console.error('No se encontraron vistaLista o vistaEditar');
        return;
    }

    document.body.classList.remove('admin-vista-lista', 'admin-vista-agregar');
    document.body.classList.add('admin-vista-editar');

    vistaLista.classList.add('admin-hidden');

    if (vistaAgregar) {
        vistaAgregar.classList.add('admin-hidden');
    }

    vistaEditar.classList.remove('admin-hidden');

    subirArribaDashboard();
}

/* ==========================================
   MOSTRAR VISTA LISTA
   Regresa a la lista y oculta agregar/editar.
========================================== */
function mostrarVistaLista() {
    const vistaLista = document.getElementById('vistaLista');
    const vistaAgregar = document.getElementById('vistaAgregar');
    const vistaEditar = document.getElementById('vistaEditar');

    if (!vistaLista) {
        console.error('No se encontró vistaLista');
        return;
    }

    document.body.classList.remove('admin-vista-agregar', 'admin-vista-editar');
    document.body.classList.add('admin-vista-lista');

    if (vistaAgregar) {
        vistaAgregar.classList.add('admin-hidden');
    }

    if (vistaEditar) {
        vistaEditar.classList.add('admin-hidden');
    }

    vistaLista.classList.remove('admin-hidden');

    subirArribaDashboard();
}

/* ==========================================
   SUBIR ARRIBA DEL DASHBOARD
   Se usa cuando cambiamos entre vistas.
========================================== */
function subirArribaDashboard() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}