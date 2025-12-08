document.addEventListener('DOMContentLoaded', function() {
    const botonesPortada = document.querySelectorAll('.ver-portada-btn');
    const imagenPortada = document.getElementById('imagenPortada');
    const modalPortada = document.getElementById('modalPortada');

    botonesPortada.forEach(boton => {
        boton.addEventListener('click', function() {
            const rutaPortada = this.getAttribute('data-imagen-popup');

            // Actualizar solo la imagen
            if (imagenPortada) {
                imagenPortada.src = rutaPortada;
                imagenPortada.alt = 'Portada de tesis';
            }

            console.log('Mostrando imagen popup:', rutaPortada);
        });
    });

    // Limpiar cuando se cierra el modal
    if (modalPortada) {
        modalPortada.addEventListener('hidden.bs.modal', function() {
            if (imagenPortada) {
                imagenPortada.src = '';
            }
        });
    }

    // Manejar errores de carga
    if (imagenPortada) {
        imagenPortada.addEventListener('error', function() {
            console.error('Error al cargar la imagen:', this.src);
            this.src = '../assets/img/portada-default.png';
            this.alt = 'Imagen no disponible';
        });
    }

    /* ============================
       SELECTS MIIDT – SlimSelect
    ============================ */
    function activarSelect(id){
        new SlimSelect({
            select: id,
            settings: {
                showSearch: false,
                search: false,
                maxHeight: '180px',       // ALTURA IDEAL PARA QUE NO SALTE
                placeholderText: '',      // SIN TEXTO ARRIBA
                allowDeselect: false      // NO OPCIÓN VACÍA
            }
        });
    }

    activarSelect('#filtro-estado');
    activarSelect('#filtro-director');
    activarSelect('#filtro-anio');

});
