/* ==========================================
   EDITAR TESIS
   Carga una tesis existente y actualiza el mismo registro
========================================== */

window.EditarTesisArchivos = {
    pastaFisica: null,
    portadaInstitucional: null
};

document.addEventListener('DOMContentLoaded', () => {
    configurarClickEditarTesis();
    configurarDirectorEditar();
    configurarCheckboxEditar();
    configurarUploadsEditar();

    /*
       IMPORTANTE:
       Si ya tienes calendario-tesis.js cargando Flatpickr,
       este bloque no rompe nada, solo evita error si no existe.
    */
    if (typeof flatpickr === 'function') {
        const editarFechaTesis = document.getElementById('editarFechaTesis');

        if (editarFechaTesis && !editarFechaTesis._flatpickr) {
            flatpickr("#editarFechaTesis", {
                dateFormat: "d/m/Y",
                allowInput: false,
                disableMobile: true
            });
        }
    }
});

/* ==========================================
   CLICK EN BOTÓN EDITAR DE CADA TESIS
   Reforzado para que no falle cuando una tarjeta
   se vuelve a pintar desde búsqueda, filtros o recarga.
========================================== */

function configurarClickEditarTesis() {
    document.addEventListener('click', async (e) => {
        const btnEditar = e.target.closest('.tesis-item__edit');

        if (!btnEditar) return;

        e.preventDefault();
        e.stopPropagation();

        let idTesis = obtenerIdTesisSeguro(btnEditar);

        /*
           Respaldo final:
           si algún render dejó el botón sin data-id,
           buscamos el id por el título visible de la tarjeta.
        */
        if (!idTesis) {
            idTesis = await buscarIdTesisPorTitulo(btnEditar);
        }

        if (!idTesis) {
            console.error('No se encontró id_tesis en este botón:', btnEditar);
            await mostrarErrorEditarTesis('No se encontró el identificador de la tesis.');
            return;
        }

        await cargarTesisParaEditar(idTesis);
    });
}

function obtenerIdTesisSeguro(btnEditar) {
    if (!btnEditar) return '';

    const posiblesIds = [
        btnEditar.getAttribute('data-id'),
        btnEditar.getAttribute('data-id-tesis'),
        btnEditar.dataset ? btnEditar.dataset.id : '',
        btnEditar.dataset ? btnEditar.dataset.idTesis : ''
    ];

    for (const id of posiblesIds) {
        if (id && String(id).trim() !== '') {
            return String(id).trim();
        }
    }

    const tarjeta = btnEditar.closest('.tesis-item');

    if (!tarjeta) return '';

    const posiblesIdsTarjeta = [
        tarjeta.getAttribute('data-id'),
        tarjeta.getAttribute('data-id-tesis'),
        tarjeta.dataset ? tarjeta.dataset.id : '',
        tarjeta.dataset ? tarjeta.dataset.idTesis : ''
    ];

    for (const id of posiblesIdsTarjeta) {
        if (id && String(id).trim() !== '') {
            return String(id).trim();
        }
    }

    return '';
}

async function buscarIdTesisPorTitulo(btnEditar) {
    try {
        const tarjeta = btnEditar.closest('.tesis-item');

        if (!tarjeta) return '';

        const tituloElemento = tarjeta.querySelector('.tesis-item__title');

        if (!tituloElemento) return '';

        const tituloVisible = limpiarTexto(tituloElemento.textContent);

        if (!tituloVisible) return '';

        const response = await fetch(`${BASE_URL}/index.php/tesis/buscar?q=${encodeURIComponent(tituloVisible)}`);

        if (!response.ok) return '';

        const lista = await response.json();

        if (!Array.isArray(lista) || lista.length === 0) return '';

        const tesisExacta = lista.find(item => {
            return limpiarTexto(item.titulo || '').toLowerCase() === tituloVisible.toLowerCase();
        });

        if (tesisExacta && tesisExacta.id_tesis) {
            return String(tesisExacta.id_tesis);
        }

        if (lista.length === 1 && lista[0].id_tesis) {
            return String(lista[0].id_tesis);
        }

        return '';

    } catch (error) {
        console.error('Error buscando id_tesis por título:', error);
        return '';
    }
}

/* ==========================================
   CARGAR DATOS DE LA TESIS
========================================== */

async function cargarTesisParaEditar(idTesis) {
    try {
        const response = await fetch(`${BASE_URL}/index.php/tesis/obtener?id=${encodeURIComponent(idTesis)}`);
        const data = await response.json();

        if (!response.ok || !data.ok) {
            throw new Error(data.message || 'No se pudo cargar la tesis.');
        }

        llenarFormularioEditar(data.tesis);

        if (typeof mostrarVistaEditar === 'function') {
            mostrarVistaEditar();
        }

    } catch (error) {
        await mostrarErrorEditarTesis(error.message);
    }
}

/* ==========================================
   LLENAR FORMULARIO EDITAR
========================================== */

function llenarFormularioEditar(tesis) {
    setValor('editarIdTesis', tesis.id_tesis);
    setValor('editarTitulo', tesis.titulo || '');
    setValor('editarUrl', tesis.url || '');
    setValor('editarFechaTesis', tesis.fecha_tesis || '');

    setValor(
        'editarAutor',
        limpiarTexto(`${tesis.autor_nombre || ''} ${tesis.autor_apellido_paterno || ''} ${tesis.autor_apellido_materno || ''}`)
    );

    setValor('editarAutorMatriculaHidden', tesis.matricula || '');
    setValor('editarAutorNombreHidden', tesis.autor_nombre || '');
    setValor('editarAutorApellidoPaternoHidden', tesis.autor_apellido_paterno || '');
    setValor('editarAutorApellidoMaternoHidden', tesis.autor_apellido_materno || '');
    setValor('editarAutorCorreoHidden', tesis.autor_correo || '');
    setValor('editarAutorSexoHidden', tesis.autor_sexo || '');

    setValor(
        'editarDirector',
        limpiarTexto(`${tesis.director_nombre || ''} ${tesis.director_apellido_paterno || ''} ${tesis.director_apellido_materno || ''}`)
    );

    setValor('editarIdDirector', tesis.id_director || '');
    setValor('editarIdDirectorLinea', tesis.director_id_linea || '');

    limpiarChecksEditar();

    const linea = document.querySelector(
        `#editarTesisPanel input[data-editar-id-linea="${tesis.id_linea}"]`
    );

    if (linea) {
        linea.checked = true;
    }

    const estado = tesis.estado || '';

    const estadoFisico = document.querySelector('#editarTesisPanel input[data-editar-estado="Fisico"]');
    const estadoDigital = document.querySelector('#editarTesisPanel input[data-editar-estado="Digital"]');

    if (estado === 'Fisico' && estadoFisico) {
        estadoFisico.checked = true;
    }

    if (estado === 'Digital' && estadoDigital) {
        estadoDigital.checked = true;
    }

    if (estado === 'Digital y Fisico') {
        if (estadoFisico) estadoFisico.checked = true;
        if (estadoDigital) estadoDigital.checked = true;
    }

    window.EditarTesisArchivos.pastaFisica = null;
    window.EditarTesisArchivos.portadaInstitucional = null;

    const btnPasta = document.getElementById('editarBtnPastaFisica');
    const btnPortada = document.getElementById('editarBtnPortadaInstitucional');

    if (btnPasta) btnPasta.textContent = 'Conservar imagen';
    if (btnPortada) btnPortada.textContent = 'Conservar imagen';
}

function limpiarChecksEditar() {
    document.querySelectorAll('#editarTesisPanel input[type="checkbox"]').forEach(check => {
        check.checked = false;
    });
}

/* ==========================================
   AUTOCOMPLETE DIRECTOR EDITAR
========================================== */

function configurarDirectorEditar() {
    const inputDirector = document.getElementById('editarDirector');
    const inputIdDirector = document.getElementById('editarIdDirector');
    const inputIdDirectorLinea = document.getElementById('editarIdDirectorLinea');
    const sugerencias = document.getElementById('editarDirectorSugerencias');

    if (!inputDirector || !inputIdDirector || !inputIdDirectorLinea || !sugerencias) return;

    let timeout = null;

    inputDirector.addEventListener('input', () => {
        inputIdDirector.value = '';
        inputIdDirectorLinea.value = '';

        clearTimeout(timeout);

        const query = inputDirector.value.trim();

        if (query.length < 2) {
            cerrarSugerenciasEditar();
            return;
        }

        timeout = setTimeout(() => {
            buscarDirectoresEditar(query);
        }, 250);
    });

    async function buscarDirectoresEditar(query) {
        try {
            const response = await fetch(`${BASE_URL}/index.php/directores/buscar?q=${encodeURIComponent(query)}`);

            if (!response.ok) {
                cerrarSugerenciasEditar();
                return;
            }

            const data = await response.json();

            renderDirectoresEditar(data);

        } catch (error) {
            cerrarSugerenciasEditar();
        }
    }

    function renderDirectoresEditar(lista) {
        sugerencias.innerHTML = '';

        if (!Array.isArray(lista) || lista.length === 0) {
            cerrarSugerenciasEditar();
            return;
        }

        lista.forEach(director => {
            const item = document.createElement('div');
            item.className = 'director-sugerencias__item';
            item.textContent = director.nombre_completo;

            item.addEventListener('click', () => {
                inputDirector.value = director.nombre_completo;
                inputIdDirector.value = director.id_director;
                inputIdDirectorLinea.value = director.id_linea;

                cerrarSugerenciasEditar();
            });

            sugerencias.appendChild(item);
        });

        sugerencias.style.display = 'block';
    }

    function cerrarSugerenciasEditar() {
        sugerencias.innerHTML = '';
        sugerencias.style.display = 'none';
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#editarTesisPanel .director-autocomplete-wrap')) {
            cerrarSugerenciasEditar();
        }
    });
}

/* ==========================================
   CHECKBOXES EDITAR
========================================== */

function configurarCheckboxEditar() {
    document.querySelectorAll('#editarTesisPanel input[data-editar-id-linea]').forEach(checkbox => {
        checkbox.addEventListener('click', function () {
            document.querySelectorAll('#editarTesisPanel input[data-editar-id-linea]').forEach(item => {
                item.checked = false;
            });

            this.checked = true;
        });
    });
}

/* ==========================================
   SUBIR IMÁGENES EDITAR
   Si no subes nada, se conserva la imagen anterior.
========================================== */

function configurarUploadsEditar() {
    const btnPasta = document.getElementById('editarBtnPastaFisica');
    const inputPasta = document.getElementById('editarPastaFisicaInput');

    const btnPortada = document.getElementById('editarBtnPortadaInstitucional');
    const inputPortada = document.getElementById('editarPortadaInstitucionalInput');

    if (btnPasta && inputPasta) {
        btnPasta.addEventListener('click', () => {
            abrirModalEditarImagen({
                titulo: 'Actualizar pasta',
                extensionFinal: '.webp',
                tipoSalida: 'image/webp',
                calidad: 0.85,
                inputFile: inputPasta,
                boton: btnPasta,
                destino: 'pastaFisica'
            });
        });
    }

    if (btnPortada && inputPortada) {
        btnPortada.addEventListener('click', () => {
            abrirModalEditarImagen({
                titulo: 'Actualizar portada',
                extensionFinal: '.png',
                tipoSalida: 'image/png',
                calidad: 0.92,
                inputFile: inputPortada,
                boton: btnPortada,
                destino: 'portadaInstitucional'
            });
        });
    }
}

function abrirModalEditarImagen(config) {
    cerrarModalEditarImagen();

    let archivoConvertido = null;

    const overlay = document.createElement('div');
    overlay.className = 'upload-tesis-overlay is-active';
    overlay.id = 'uploadEditarTesisOverlay';

    overlay.innerHTML = `
        <div class="upload-tesis-modal" role="dialog" aria-modal="true">
            <div class="upload-tesis-header">
                <h2 class="upload-tesis-title">${config.titulo}</h2>
                <button type="button" class="upload-tesis-close" aria-label="Cerrar">&times;</button>
            </div>

            <div class="upload-tesis-body">
                <p class="upload-tesis-text">Agrega tu imagen aquí</p>

                <div class="upload-tesis-dropzone" id="uploadEditarTesisDropzone">
                    <div class="upload-tesis-preview-wrap" id="uploadEditarTesisPreviewWrap">
                        <img class="upload-tesis-preview" id="uploadEditarTesisPreview" alt="Vista previa">
                        <button type="button" class="upload-tesis-remove" id="uploadEditarTesisRemove">&times;</button>
                    </div>

                    <div class="upload-tesis-empty" id="uploadEditarTesisEmpty">
                        <div class="upload-tesis-icon">⬆</div>
                        <div class="upload-tesis-instruction">
                            Arrastra la imagen aquí, <span>o click para buscar</span>
                        </div>
                    </div>
                </div>

                <div class="upload-tesis-info">
                    <span class="upload-tesis-info-icon">i</span>
                    <span>Archivos compatibles: <strong>${config.extensionFinal}</strong></span>
                </div>

                <button type="button" class="upload-tesis-submit" id="uploadEditarTesisSubmit" disabled>
                    Subir
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    const closeBtn = overlay.querySelector('.upload-tesis-close');
    const dropzone = overlay.querySelector('#uploadEditarTesisDropzone');
    const preview = overlay.querySelector('#uploadEditarTesisPreview');
    const previewWrap = overlay.querySelector('#uploadEditarTesisPreviewWrap');
    const emptyContent = overlay.querySelector('#uploadEditarTesisEmpty');
    const removeBtn = overlay.querySelector('#uploadEditarTesisRemove');
    const submitBtn = overlay.querySelector('#uploadEditarTesisSubmit');

    closeBtn.addEventListener('click', cerrarModalEditarImagen);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            cerrarModalEditarImagen();
        }
    });

    dropzone.addEventListener('click', (e) => {
        if (e.target === removeBtn) return;
        config.inputFile.click();
    });

    config.inputFile.onchange = async () => {
        const file = config.inputFile.files[0];
        if (!file) return;

        try {
            archivoConvertido = await convertirImagenEditar(file, config.tipoSalida, config.calidad);
            mostrarPreviewEditar(file, preview, previewWrap, emptyContent);
            submitBtn.disabled = false;
        } catch (error) {
            await mostrarErrorEditarTesis(error.message);
            limpiarSeleccionEditar();
        }
    };

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('is-dragover');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('is-dragover');
    });

    dropzone.addEventListener('drop', async (e) => {
        e.preventDefault();
        dropzone.classList.remove('is-dragover');

        const file = e.dataTransfer.files[0];
        if (!file) return;

        try {
            archivoConvertido = await convertirImagenEditar(file, config.tipoSalida, config.calidad);
            mostrarPreviewEditar(file, preview, previewWrap, emptyContent);
            submitBtn.disabled = false;
        } catch (error) {
            await mostrarErrorEditarTesis(error.message);
            limpiarSeleccionEditar();
        }
    });

    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        limpiarSeleccionEditar();
    });

    submitBtn.addEventListener('click', () => {
        if (!archivoConvertido) return;

        window.EditarTesisArchivos[config.destino] = archivoConvertido;
        config.boton.textContent = 'Imagen nueva';

        cerrarModalEditarImagen();
    });

    function limpiarSeleccionEditar() {
        archivoConvertido = null;
        config.inputFile.value = '';
        preview.src = '';
        previewWrap.classList.remove('has-image');
        emptyContent.style.display = 'block';
        submitBtn.disabled = true;
    }
}

function cerrarModalEditarImagen() {
    const modal = document.getElementById('uploadEditarTesisOverlay');

    if (modal) {
        modal.remove();
    }
}

/* ==========================================
   ACTUALIZAR TESIS
========================================== */

async function actualizarTesis() {
    const btnActualizar = document.querySelector('#editarTesisPanel .btn-guardar');

    try {
        const idTesis = obtenerValorEditar('editarIdTesis');
        const titulo = obtenerValorEditar('editarTitulo');
        const autorVisible = obtenerValorEditar('editarAutor');
        const directorTexto = obtenerValorEditar('editarDirector');
        const idDirector = obtenerValorEditar('editarIdDirector');
        const idDirectorLinea = obtenerValorEditar('editarIdDirectorLinea');
        const fechaTesis = obtenerValorEditar('editarFechaTesis');
        const url = obtenerValorEditar('editarUrl');

        const autorMatricula = obtenerValorEditar('editarAutorMatriculaHidden');
        const autorNombre = obtenerValorEditar('editarAutorNombreHidden');
        const autorApellidoPaterno = obtenerValorEditar('editarAutorApellidoPaternoHidden');
        const autorApellidoMaterno = obtenerValorEditar('editarAutorApellidoMaternoHidden');
        const autorCorreo = obtenerValorEditar('editarAutorCorreoHidden');
        const autorSexo = obtenerValorEditar('editarAutorSexoHidden');

        const lineaSeleccionada = document.querySelector(
            '#editarTesisPanel input[data-editar-id-linea]:checked'
        );

        const estadoFisico = document.querySelector(
            '#editarTesisPanel input[data-editar-estado="Fisico"]'
        );

        const estadoDigital = document.querySelector(
            '#editarTesisPanel input[data-editar-estado="Digital"]'
        );

        const estado = obtenerEstadoEditar(estadoFisico, estadoDigital);

        validarDatosEditar({
            idTesis,
            titulo,
            autorVisible,
            directorTexto,
            autorMatricula,
            autorNombre,
            autorApellidoPaterno,
            autorSexo,
            idDirector,
            idDirectorLinea,
            lineaSeleccionada,
            fechaTesis,
            estado,
            url
        });

        const formData = new FormData();

        formData.append('id_tesis', idTesis);
        formData.append('titulo', titulo);
        formData.append('url', url);
        formData.append('fecha_registro', fechaTesis);
        formData.append('estado', estado);

        /*
            NUEVA MEJORA:
            - Si el director fue seleccionado de la lista, id_director trae valor.
            - Si el director fue escrito manualmente y no existe, id_director va vacío,
              pero director_texto se manda al backend para crearlo automáticamente.
        */
        formData.append('id_director', idDirector);
        formData.append('director_texto', directorTexto);

        formData.append('id_linea', lineaSeleccionada.dataset.editarIdLinea);

        formData.append('autor_matricula', autorMatricula);
        formData.append('autor_nombre', autorNombre);
        formData.append('autor_apellido_paterno', autorApellidoPaterno);
        formData.append('autor_apellido_materno', autorApellidoMaterno);
        formData.append('autor_correo', autorCorreo);
        formData.append('autor_sexo', autorSexo);

        /*
            Seguridad CSRF:
            Se envía al backend para validar que la petición viene del panel admin.
        */
        formData.append('csrf_token', obtenerCsrfTokenEditar());

        if (window.EditarTesisArchivos && window.EditarTesisArchivos.pastaFisica) {
            formData.append(
                'pasta_fisica',
                window.EditarTesisArchivos.pastaFisica,
                'pasta-fisica.webp'
            );
        }

        if (window.EditarTesisArchivos && window.EditarTesisArchivos.portadaInstitucional) {
            formData.append(
                'portada_institucional',
                window.EditarTesisArchivos.portadaInstitucional,
                'portada-institucional.png'
            );
        }

        cambiarEstadoBotonEditar(btnActualizar, true);

        const response = await fetch(`${BASE_URL}/index.php/tesis/actualizar`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (!response.ok || !data.ok) {
            throw new Error(data.message || 'No se pudo actualizar la tesis.');
        }

        if (typeof mostrarModalTesisExito === 'function') {
            await mostrarModalTesisExito({
                autor: autorVisible,
                linea: obtenerNombreLineaEditar(lineaSeleccionada.dataset.editarIdLinea)
            });
        }

        limpiarFormularioEditar();

        if (typeof mostrarVistaLista === 'function') {
            mostrarVistaLista();
        }

        /*
           Mejor que reload:
           recarga la lista con el mismo JS de tesis.js,
           sin perder eventos ni dejar botones sin id.
        */
        if (typeof window.recargarListaTesis === 'function') {
            window.recargarListaTesis();
        } else {
            window.location.reload();
        }

    } catch (error) {
        await mostrarErrorEditarTesis(error.message);
    } finally {
        cambiarEstadoBotonEditar(btnActualizar, false);
    }
}

/* ==========================================
   VALIDACIONES EDITAR
========================================== */

function validarDatosEditar(datos) {
    if (!datos.idTesis) {
        throw new Error('No se encontró la tesis que deseas actualizar.');
    }

    if (!datos.titulo) {
        throw new Error('Escribe el título de la tesis.');
    }

    if (!datos.autorVisible) {
        throw new Error('Agrega los datos del autor.');
    }

    if (
        !datos.autorMatricula ||
        !datos.autorNombre ||
        !datos.autorApellidoPaterno ||
        !datos.autorSexo
    ) {
        throw new Error('Completa los datos obligatorios del autor.');
    }

    /*
        NUEVA MEJORA:
        Antes era obligatorio seleccionar un director de la lista.
        Ahora puede:
        - Seleccionarlo de la lista: idDirector tiene valor.
        - Escribirlo manualmente: directorTexto tiene valor y el backend lo crea.
    */
    if (!datos.idDirector && !datos.directorTexto) {
        throw new Error('Selecciona o escribe el nombre del director de tesis.');
    }

    if (!datos.lineaSeleccionada) {
        throw new Error('Selecciona una línea de investigación.');
    }

    if (!datos.fechaTesis) {
        throw new Error('Selecciona la fecha de la tesis.');
    }

    if (!datos.estado) {
        throw new Error('Selecciona el estado de la tesis.');
    }

    /*
        REGLA DEL LINK EN EDITAR:
        - Fisico: NO requiere link.
        - Digital: SÍ requiere link.
        - Digital y Fisico: SÍ requiere link.
    */
    const requiereLinkDigital = datos.estado === 'Digital' || datos.estado === 'Digital y Fisico';

    if (requiereLinkDigital && !datos.url) {
        throw new Error('Agrega el link del archivo digital de la tesis.');
    }

    if (datos.url && !esUrlValidaEditar(datos.url)) {
        throw new Error('El link del archivo digital de la tesis no tiene un formato válido.');
    }
}

/* ==========================================
   HELPERS
========================================== */

function obtenerValorEditar(id) {
    const input = document.getElementById(id);
    return input ? input.value.trim() : '';
}

function setValor(id, valor) {
    const input = document.getElementById(id);

    if (input) {
        input.value = valor ?? '';
    }
}

function limpiarTexto(texto) {
    return String(texto || '').replace(/\s+/g, ' ').trim();
}

function obtenerEstadoEditar(fisico, digital) {
    const tieneFisico = fisico && fisico.checked;
    const tieneDigital = digital && digital.checked;

    if (tieneFisico && tieneDigital) {
        return 'Digital y Fisico';
    }

    if (tieneFisico) {
        return 'Fisico';
    }

    if (tieneDigital) {
        return 'Digital';
    }

    return '';
}

function obtenerNombreLineaEditar(idLinea) {
    const mapa = {
        '1': 'CSR',
        '2': 'TICs',
        '3': 'Geomática'
    };

    return mapa[String(idLinea)] || 'una línea diferente';
}

function esUrlValidaEditar(url) {
    try {
        const urlObj = new URL(url);
        return urlObj.protocol === 'http:' || urlObj.protocol === 'https:';
    } catch (e) {
        return false;
    }
}

function cambiarEstadoBotonEditar(boton, cargando) {
    if (!boton) return;

    if (cargando) {
        boton.disabled = true;
        boton.dataset.textoOriginal = boton.textContent;
        boton.textContent = 'Actualizando...';
        boton.style.opacity = '0.7';
        boton.style.cursor = 'not-allowed';
    } else {
        boton.disabled = false;
        boton.textContent = boton.dataset.textoOriginal || 'Actualizar';
        boton.style.opacity = '';
        boton.style.cursor = '';
    }
}

function limpiarFormularioEditar() {
    [
        'editarIdTesis',
        'editarTitulo',
        'editarAutor',
        'editarAutorMatriculaHidden',
        'editarAutorNombreHidden',
        'editarAutorApellidoPaternoHidden',
        'editarAutorApellidoMaternoHidden',
        'editarAutorCorreoHidden',
        'editarAutorSexoHidden',
        'editarDirector',
        'editarIdDirector',
        'editarIdDirectorLinea',
        'editarFechaTesis',
        'editarUrl'
    ].forEach(id => setValor(id, ''));

    limpiarChecksEditar();

    const btnPasta = document.getElementById('editarBtnPastaFisica');
    const btnPortada = document.getElementById('editarBtnPortadaInstitucional');

    if (btnPasta) btnPasta.textContent = 'Conservar imagen';
    if (btnPortada) btnPortada.textContent = 'Conservar imagen';

    if (window.EditarTesisArchivos) {
        window.EditarTesisArchivos.pastaFisica = null;
        window.EditarTesisArchivos.portadaInstitucional = null;
    }
}

async function mostrarErrorEditarTesis(mensaje) {
    if (typeof mostrarModalTesisError === 'function') {
        await mostrarModalTesisError(mensaje);
    } else {
        console.error(mensaje);
    }
}

/* ==========================================
   CONVERSIÓN DE IMÁGENES
========================================== */

function mostrarPreviewEditar(file, preview, previewWrap, emptyContent) {
    const url = URL.createObjectURL(file);

    preview.onload = () => {
        URL.revokeObjectURL(url);
    };

    preview.src = url;
    previewWrap.classList.add('has-image');
    emptyContent.style.display = 'none';
}

async function convertirImagenEditar(file, tipoSalida, calidad) {
    validarImagenEditar(file);

    const image = await cargarImagenEditar(file);

    const canvas = document.createElement('canvas');
    canvas.width = image.naturalWidth || image.width;
    canvas.height = image.naturalHeight || image.height;

    const ctx = canvas.getContext('2d');

    if (!ctx) {
        throw new Error('No se pudo procesar la imagen.');
    }

    ctx.drawImage(image, 0, 0, canvas.width, canvas.height);

    return new Promise((resolve, reject) => {
        canvas.toBlob((blob) => {
            if (!blob) {
                reject(new Error('No se pudo convertir la imagen.'));
                return;
            }

            resolve(blob);
        }, tipoSalida, calidad);
    });
}

function validarImagenEditar(file) {
    if (!file.type || !file.type.startsWith('image/')) {
        throw new Error('El archivo seleccionado debe ser una imagen.');
    }

    const maxSize = 8 * 1024 * 1024;

    if (file.size > maxSize) {
        throw new Error('La imagen no debe pesar más de 8 MB.');
    }
}

function cargarImagenEditar(file) {
    return new Promise((resolve, reject) => {
        const url = URL.createObjectURL(file);
        const img = new Image();

        img.onload = () => {
            URL.revokeObjectURL(url);
            resolve(img);
        };

        img.onerror = () => {
            URL.revokeObjectURL(url);
            reject(new Error('No se pudo cargar la imagen.'));
        };

        img.src = url;
    });
}

/* ==========================================
   CSRF TOKEN
========================================== */

function obtenerCsrfTokenEditar() {
    if (typeof window.CSRF_TOKEN === 'string' && window.CSRF_TOKEN.trim() !== '') {
        return window.CSRF_TOKEN.trim();
    }

    const meta = document.querySelector('meta[name="csrf-token"]');

    if (meta && meta.getAttribute('content')) {
        return meta.getAttribute('content').trim();
    }

    const input = document.querySelector('input[name="csrf_token"]');

    if (input && input.value) {
        return input.value.trim();
    }

    return '';
}