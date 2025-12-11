// busqueda_ajax.js
document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 Iniciando sistema de búsqueda AJAX');

    const contenedor = document.getElementById('resultados-lista');
    if (contenedor) {
        contenedor.addEventListener('click', manejarClickPortada);
    }
    // 🎨 Inicializar SlimSelect para todos los filtros
    let slimEstado, slimDirector, slimAnio;

    try {
        slimEstado = new SlimSelect({
            select: '#filtro-estado',
            settings: {
                placeholderText: 'Estado'
            }
        });

        slimDirector = new SlimSelect({
            select: '#filtro-director',
            settings: {
                placeholderText: 'Director de tesis'
            }
        });

        slimAnio = new SlimSelect({
            select: '#filtro-anio',
            settings: {
                placeholderText: 'Año de publicación'
            }
        });

        console.log('✅ SlimSelect inicializado correctamente');
    } catch (error) {
        console.error('❌ Error al inicializar SlimSelect:', error);
    }

    // 🔍 Función para cargar resultados
    function cargarResultados() {
        const busqueda = document.getElementById('input-busqueda').value;
        const estado = document.getElementById('filtro-estado').value;
        const director = document.getElementById('filtro-director').value;
        const anio = document.getElementById('filtro-anio').value;

        // ✅ NUEVO: Obtener la línea de investigación
        const linea = document.getElementById('linea-investigacion')?.value || 'CSR';

        const params = new URLSearchParams({
            busqueda: busqueda,
            estado: estado,
            director: director,
            anio: anio,
            linea: linea  // ✅ NUEVO: Agregar parámetro línea
        });

        console.log('📡 Cargando resultados:', params.toString());

        fetch('../php/busqueda_ajax.php?' + params.toString())
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en servidor: ' + response.status);
                }
                return response.json(); // ✅ Cambio: parsear JSON en lugar de texto
            })
            .then(data => {
                const contenedor = document.getElementById('resultados-lista');
                if (contenedor) {
                    contenedor.innerHTML = data.html; // ✅ Usar el HTML del JSON
                    reinicializarModales();
                    actualizarFiltrosActivos();
                    actualizarContador(data.count); // ✅ NUEVO: Actualizar contador
                    console.log('✅ Resultados actualizados:', data.count, 'tesis');
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
        inputBusqueda.addEventListener('input', function (e) {
            clearTimeout(timeoutBusqueda);
            timeoutBusqueda = setTimeout(() => {
                console.log('🔎 Búsqueda:', this.value);
                cargarResultados();
            }, 500);
        });

        // Prevenir envío con Enter
        inputBusqueda.addEventListener('keydown', function (e) {
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
        filtroEstado.addEventListener('change', function (e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎚️ Filtro estado:', this.value);
            cargarResultados();
        });
    }

    if (filtroDirector) {
        filtroDirector.addEventListener('change', function (e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎚️ Filtro director:', this.value);
            cargarResultados();
        });
    }

    if (filtroAnio) {
        filtroAnio.addEventListener('change', function (e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🎚️ Filtro año:', this.value);
            cargarResultados();
        });
    }

    // 🧹 Limpiar filtros
    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            console.log('🧹 Limpiando filtros');

            // Limpiar input de búsqueda
            document.getElementById('input-busqueda').value = '';

            // ✅ NUEVO: Resetear SlimSelect correctamente para restaurar placeholders
            if (slimEstado) {
                slimEstado.setSelected([]);
            } else {
                document.getElementById('filtro-estado').value = '';
            }

            if (slimDirector) {
                slimDirector.setSelected([]);
            } else {
                document.getElementById('filtro-director').value = '';
            }

            if (slimAnio) {
                slimAnio.setSelected([]);
            } else {
                document.getElementById('filtro-anio').value = '';
            }

            cargarResultados();
        });
    }

    // 🔄 Reinicializar modales usando DELEGACIÓN DE EVENTOS
function reinicializarModales() {
    // Remover listeners anteriores si existen
    const contenedor = document.getElementById('resultados-lista');
    
    if (contenedor) {
        // Usar delegación de eventos en el contenedor padre
        contenedor.removeEventListener('click', manejarClickPortada);
        contenedor.addEventListener('click', manejarClickPortada);
        
        console.log('✅ Event listener de modal reinicializado');
    }
}

// Función separada para manejar el click
function manejarClickPortada(e) {
    const btn = e.target.closest('.ver-portada-btn');
    
    if (btn) {
        const imagenUrl = btn.getAttribute('data-imagen-popup');
        const titulo = btn.getAttribute('data-titulo');

        console.log('🖼️ Mostrando portada:', imagenUrl); // Debug

        const imgElement = document.getElementById('imagenPortada');
        const titleElement = document.getElementById('modalPortadaLabel');

        if (imgElement) {
            imgElement.src = imagenUrl;
            console.log('✅ Imagen asignada:', imagenUrl);
        }
        if (titleElement) {
            titleElement.textContent = 'Portada Oficial De La Tesis';
        }
    }
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

    // 🔢 NUEVO: Actualizar contador de resultados
    function actualizarContador(count) {
        const resultadoHeader = document.querySelector('.resultado-header .text-primary');
        if (resultadoHeader) {
            resultadoHeader.textContent = `${count} Tesis`;
        }
    }

    // Inicializar modales en carga inicial
    reinicializarModales();

    console.log('✅ Sistema AJAX inicializado correctamente');
});
