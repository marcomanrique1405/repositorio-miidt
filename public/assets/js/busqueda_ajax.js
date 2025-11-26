// busqueda_ajax.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Iniciando sistema de búsqueda AJAX');
    
    // 🔍 Función para cargar resultados
    function cargarResultados() {
        const busqueda = document.getElementById('input-busqueda').value;
        const estado = document.getElementById('filtro-estado').value;
        const director = document.getElementById('filtro-director').value;
        const anio = document.getElementById('filtro-anio').value;
        
        const params = new URLSearchParams({
            busqueda: busqueda,
            estado: estado,
            director: director,
            anio: anio
        });

        console.log('📡 Cargando resultados:', params.toString());

        fetch('/repositorio_MIIDT/repositorio-miidt/public/php/busqueda_ajax.php?' + params.toString())
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en servidor: ' + response.status);
                }
                return response.text();
            })
            .then(html => {
                const contenedor = document.getElementById('resultados-lista');
                if (contenedor) {
                    contenedor.innerHTML = html;
                    reinicializarModales();
                    actualizarFiltrosActivos();
                    console.log('✅ Resultados actualizados');
                } else {
                    console.error('❌ No se encontró #resultados-lista');
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);
                const contenedor = document.getElementById('resultados-lista');
                if (contenedor) {
                    contenedor.innerHTML = '<div class="alert alert-danger">Error al cargar resultados. Intenta recargar la página.</div>';
                }
            });
    }

    // 🔎 Búsqueda con debounce
    let timeoutBusqueda;
    const inputBusqueda = document.getElementById('input-busqueda');
    
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function(e) {
            clearTimeout(timeoutBusqueda);
            timeoutBusqueda = setTimeout(() => {
                console.log('🔎 Búsqueda:', this.value);
                cargarResultados();
            }, 500);
        });

        // Prevenir envío con Enter
        inputBusqueda.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(timeoutBusqueda);
                cargarResultados();
            }
        });
    }

    // 🎚️ Filtros
    const filtroEstado = document.getElementById('filtro-estado');
    const filtroDirector = document.getElementById('filtro-director');
    const filtroAnio = document.getElementById('filtro-anio');

    if (filtroEstado) {
        filtroEstado.addEventListener('change', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎚️ Filtro estado:', this.value);
            cargarResultados();
        });
    }

    if (filtroDirector) {
        filtroDirector.addEventListener('change', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎚️ Filtro director:', this.value);
            cargarResultados();
        });
    }

    if (filtroAnio) {
        filtroAnio.addEventListener('change', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎚️ Filtro año:', this.value);
            cargarResultados();
        });
    }

    // 🧹 Limpiar filtros
    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('🧹 Limpiando filtros');
            
            document.getElementById('input-busqueda').value = '';
            document.getElementById('filtro-estado').value = '';
            document.getElementById('filtro-director').value = '';
            document.getElementById('filtro-anio').value = '';
            
            cargarResultados();
        });
    }

    // 🔄 Reinicializar modales
    function reinicializarModales() {
        document.querySelectorAll('.ver-portada-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const imagenUrl = this.getAttribute('data-imagen-popup');
                const titulo = this.getAttribute('data-titulo');
                
                const imgElement = document.getElementById('imagenPortada');
                const titleElement = document.getElementById('modalPortadaLabel');
                
                if (imgElement) imgElement.src = imagenUrl;
                if (titleElement) titleElement.textContent = titulo;
            });
        });
    }

    // 📊 Actualizar texto filtros activos
    function actualizarFiltrosActivos() {
        const filtrosActivos = [];
        
        const estado = document.getElementById('filtro-estado').value;
        const director = document.getElementById('filtro-director').value;
        const anio = document.getElementById('filtro-anio').value;
        
        if (estado) filtrosActivos.push(`Estado: ${estado}`);
        if (director) filtrosActivos.push('Director');
        if (anio) filtrosActivos.push(`Año: ${anio}`);
        
        const textoFiltros = document.getElementById('filtros-activos-texto');
        if (textoFiltros) {
            textoFiltros.textContent = filtrosActivos.length > 0 ? filtrosActivos.join(', ') : 'Ninguno';
        }
    }

    // Inicializar modales en carga inicial
    reinicializarModales();
    
    console.log('✅ Sistema AJAX inicializado correctamente');
});