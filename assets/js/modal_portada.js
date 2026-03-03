document.addEventListener('DOMContentLoaded', function () {
    const botonesPortada = document.querySelectorAll('.ver-portada-btn');
    const imagenPortada = document.getElementById('imagenPortada');
    const modalPortada = document.getElementById('modalPortada');
    const tituloModal = document.getElementById('modalPortadaLabel');

    botonesPortada.forEach(boton => {
        boton.addEventListener('click', function () {
            const rutaPortada = this.getAttribute('data-imagen-popup');
            // Valida que la ruta no esté vacía
            if (!rutaPortada) {
                console.error('No se encontró la ruta de la imagen popup');
                return;
            }

            // Actualizar la imagen y el título
            if (imagenPortada) {
                imagenPortada.src = rutaPortada;
                imagenPortada.alt = 'Portada Oficial De La Tesis';

            }

            if (tituloModal) {
                tituloModal.textContent = 'Portada Oficial De La Tesis';
            }

            console.log('Mostrando imagen popup:', rutaPortada);
        });
    });

    // Limpiar cuando se cierra el popup
    if (modalPortada) {
        modalPortada.addEventListener('hidden.bs.modal', function () {
            if (imagenPortada) {
                imagenPortada.src = '';
                imagenPortada.alt = '';
            }
        });
    }

    // Manejar errores de carga
    if (imagenPortada) {
        imagenPortada.addEventListener('error', function () {
            console.error('Error al cargar la imagen:', this.src);

            this.src = '/public/assets/img/portada-default.png';
            this.alt = 'Imagen no disponible';
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {

    function activarSelect(id){
        new SlimSelect({
            select: id,
            settings: {
                showSearch: false,
                search: false,
                maxHeight: '100px',
                placeholderText: placeholder,  
                allowDeselect: false,
            }
        });
    }

    activarSelect('#filtro-estado');
    activarSelect('#filtro-director');
    activarSelect('#filtro-anio');

});