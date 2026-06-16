// =============================
// CARGAR AÑOS AUTOMÁTICO
// =============================
function cargarAnios() {
    const select = document.getElementById('filtro-anio');

    if (!select) return;

    const anioActual = new Date().getFullYear();

    // evita duplicar opciones si vuelve a ejecutarse
    if (select.options.length > 1) return;

    for (let anio = anioActual; anio >= 2016; anio--) {
        const option = document.createElement('option');
        option.value = anio;
        option.textContent = anio;
        select.appendChild(option);
    }
}

// =============================
// OBTENER FILTROS
// =============================
function obtenerFiltros() {
    const lies = Array.from(document.querySelectorAll('.filtro-lies:checked'))
        .map(el => el.value);

    const estado = Array.from(document.querySelectorAll('.filtro-estado:checked'))
        .map(el => el.value);

    const anio = document.getElementById('filtro-anio')?.value || '';
    const director = document.getElementById('filtro-director')?.value.trim() || '';

    return {
        lies,
        estado,
        anio,
        director
    };
}

// =============================
// FETCH FILTROS
// =============================
async function filtrarTesis() {
    const filtros = obtenerFiltros();

    try {
        const response = await fetch(`${BASE_URL}/index.php/filtrar-tesis`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(filtros)
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        renderizarTesis(Array.isArray(data) ? data : []);
        actualizarTextoFiltrosActivos(filtros);

    } catch (error) {
        console.error('Error filtros:', error);
    }
}

// =============================
// RENDER TESIS
// =============================
function renderizarTesis(tesis) {
    const contenedor = document.getElementById('contenedor-tesis');
    const total = document.getElementById('tesis-total');

    if (!contenedor || !total) return;

    contenedor.innerHTML = '';
    total.textContent = `${tesis.length} Tesis`;

    if (tesis.length === 0) {
        contenedor.innerHTML = `
            <div class="tesis-item">
                <div class="tesis-item__content">
                    <h3 class="tesis-item__title">No se encontraron resultados</h3>
                </div>
            </div>
        `;
        return;
    }

    tesis.forEach(t => {
        /*
           IMPORTANTE:
           El problema estaba aquí.
           Antes este render pintaba los botones Editar/Eliminar sin data-id.
           Ahora tomamos el id_tesis de forma segura y lo ponemos en:
           - la tarjeta .tesis-item
           - el botón Editar
           - el botón Eliminar
        */
        const idTesis = String(
            t.id_tesis ??
            t.idTesis ??
            t.id ??
            ''
        ).trim();

        const imagen = t.imagen && String(t.imagen).trim() !== ''
            ? t.imagen
            : `${BASE_URL}/assets/img/no-image.png`;

        const div = document.createElement('div');
        div.className = 'tesis-item';

        div.setAttribute('data-id', idTesis);
        div.setAttribute('data-id-tesis', idTesis);

        div.innerHTML = `
            <div class="tesis-item__cover">
                <img 
                    src="${imagen}" 
                    alt="Portada de tesis"
                    onerror="this.src='${BASE_URL}/assets/img/default.jpg'"
                >
            </div>

            <div class="tesis-item__content">
                <h3 class="tesis-item__title">${t.titulo ?? ''}</h3>

                <p class="tesis-item__meta"><strong>Autor:</strong> ${t.autor ?? ''}</p>
                <p class="tesis-item__meta"><strong>Director:</strong> ${t.director ?? ''}</p>
                <p class="tesis-item__meta"><strong>LIES:</strong> ${t.lies ?? ''}</p>
                <p class="tesis-item__meta"><strong>Estado:</strong> ${t.estado ?? ''}</p>

                <div class="tesis-item__actions">
                    <button 
                        type="button" 
                        class="tesis-item__edit"
                        data-id="${idTesis}"
                        data-id-tesis="${idTesis}"
                        aria-label="Editar tesis"
                    >
                        Editar
                    </button>

                    <button 
                        type="button" 
                        class="tesis-item__delete"
                        data-id="${idTesis}"
                        data-id-tesis="${idTesis}"
                        aria-label="Eliminar tesis"
                    >
                        Eliminar
                    </button>
                </div>
            </div>

            <div class="tesis-item__year">${t.anio ?? ''}</div>
        `;

        /*
           Refuerzo extra:
           Después de crear el HTML, volvemos a asegurar
           que los botones tengan el id.
        */
        const btnEditar = div.querySelector('.tesis-item__edit');
        const btnEliminar = div.querySelector('.tesis-item__delete');

        if (btnEditar) {
            btnEditar.setAttribute('data-id', idTesis);
            btnEditar.setAttribute('data-id-tesis', idTesis);
        }

        if (btnEliminar) {
            btnEliminar.setAttribute('data-id', idTesis);
            btnEliminar.setAttribute('data-id-tesis', idTesis);
        }

        contenedor.appendChild(div);
    });
}

// =============================
// TEXTO FILTROS ACTIVOS
// =============================
function actualizarTextoFiltrosActivos(filtros) {
    const texto = document.getElementById('filtros-activos-texto');
    if (!texto) return;

    const activos = [];

    if (filtros.lies.length) activos.push(`LIES: ${filtros.lies.join(', ')}`);
    if (filtros.estado.length) activos.push(`Estado: ${filtros.estado.join(', ')}`);
    if (filtros.anio) activos.push(`Año: ${filtros.anio}`);
    if (filtros.director) activos.push(`Director: ${filtros.director}`);

    texto.textContent = activos.length ? activos.join(' | ') : 'Ninguno';
}

// =============================
// LIMPIAR FILTROS
// =============================
function limpiarFiltros() {
    document.querySelectorAll('.filtro-lies').forEach(el => {
        el.checked = false;
    });

    document.querySelectorAll('.filtro-estado').forEach(el => {
        el.checked = false;
    });

    const selectAnio = document.getElementById('filtro-anio');
    if (selectAnio) {
        selectAnio.value = '';
    }

    const directorInput = document.getElementById('filtro-director');
    if (directorInput) {
        directorInput.value = '';
    }

    filtrarTesis();
}

// =============================
// EVENTOS
// =============================
document.addEventListener('DOMContentLoaded', () => {
    cargarAnios();

    const checkboxesLies = document.querySelectorAll('.filtro-lies');
    checkboxesLies.forEach(el => {
        el.addEventListener('change', filtrarTesis);
    });

    const checkboxesEstado = document.querySelectorAll('.filtro-estado');
    checkboxesEstado.forEach(el => {
        el.addEventListener('change', filtrarTesis);
    });

    const selectAnio = document.getElementById('filtro-anio');
    if (selectAnio) {
        selectAnio.addEventListener('change', filtrarTesis);
    }

    const directorInput = document.getElementById('filtro-director');
    if (directorInput) {
        directorInput.addEventListener('input', filtrarTesis);
    }

    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', limpiarFiltros);
    }

    // carga inicial
    filtrarTesis();
});