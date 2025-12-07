document.addEventListener('DOMContentLoaded', function() {
    const botonesPortada = document.querySelectorAll('.ver-portada-btn');
    const imagenPortada = document.getElementById('imagenPortada');
    const modalPortada = document.getElementById('modalPortada');

    botonesPortada.forEach(boton => {
        boton.addEventListener('click', function() {
            const rutaPortada = this.getAttribute('data-imagen-popup');
            const tituloTesis = this.getAttribute('data-titulo');

            // ✅ Validar que la ruta no esté vacía
            if (!rutaPortada) {
                console.error('No se encontró la ruta de la imagen popup');
                return;
            }

            // Actualizar la imagen y el título
            if (imagenPortada) {
                imagenPortada.src = rutaPortada;
                imagenPortada.alt = tituloTesis ? 'Portada de: ' + tituloTesis : 'Portada de tesis';
            }

            console.log('Mostrando imagen popup:', rutaPortada);
            console.log('Título:', tituloTesis);
        });
    });

    // Limpiar cuando se cierra el modal
    if (modalPortada) {
        modalPortada.addEventListener('hidden.bs.modal', function() {
            if (imagenPortada) {
                imagenPortada.src = '';
                imagenPortada.alt = '';
            }
        });
    }

    // Manejar errores de carga
    if (imagenPortada) {
        imagenPortada.addEventListener('error', function() {
            console.error('Error al cargar la imagen:', this.src);
            
            // ✅ Ruta absoluta desde la raíz del sitio
            this.src = '/public/assets/img/portada-default.png';
            this.alt = 'Imagen no disponible';
        });
    }
});