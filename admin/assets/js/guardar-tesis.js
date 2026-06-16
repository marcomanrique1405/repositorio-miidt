/* ==========================================
   GUARDAR TESIS
   Conecta el formulario Alta de tesis con el backend
========================================== */

async function guardarTesis() {
    try {
        const titulo = obtenerValor('titulo');
        const autorVisible = obtenerValor('autor');
        const directorTexto = obtenerValor('director');
        const idDirector = obtenerValor('idDirector');
        const idDirectorLinea = obtenerValor('idDirectorLinea');
        const fechaTesis = obtenerValor('fechaTesis');
        const url = obtenerValor('url');

        const autorMatricula = obtenerValor('autorMatriculaHidden');
        const autorNombre = obtenerValor('autorNombreHidden');
        const autorApellidoPaterno = obtenerValor('autorApellidoPaternoHidden');
        const autorApellidoMaterno = obtenerValor('autorApellidoMaternoHidden');
        const autorCorreo = obtenerValor('autorCorreoHidden');
        const autorSexo = obtenerValor('autorSexoHidden');

        const lineaSeleccionada = document.querySelector(
            '#agregarTesisPanel .admin-checkbox-group input[data-id-linea]:checked'
        );

        const estadoFisico = document.querySelector(
            '#agregarTesisPanel .admin-checkbox-group input[value="Fisico"]'
        );

        const estadoDigital = document.querySelector(
            '#agregarTesisPanel .admin-checkbox-group input[value="Digital"]'
        );

        const estado = obtenerEstadoTesis(estadoFisico, estadoDigital);

        validarDatosTesis({
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

        formData.append('id_linea', lineaSeleccionada.dataset.idLinea);

        formData.append('autor_matricula', autorMatricula);
        formData.append('autor_nombre', autorNombre);
        formData.append('autor_apellido_paterno', autorApellidoPaterno);
        formData.append('autor_apellido_materno', autorApellidoMaterno);
        formData.append('autor_correo', autorCorreo);
        formData.append('autor_sexo', autorSexo);

        /*
            Seguridad CSRF:
            Se envía al backend para validar que la petición viene del panel admin.
            El token debe existir en window.CSRF_TOKEN, meta[name="csrf-token"]
            o input[name="csrf_token"].
        */
        formData.append('csrf_token', obtenerCsrfToken());

        /*
            Estas imágenes vienen del archivo upload-imagenes-tesis.js
            Pasta física -> WEBP
            Portada institucional -> PNG
        */
        formData.append(
            'pasta_fisica',
            window.AltaTesisArchivos.pastaFisica,
            'pasta-fisica.webp'
        );

        formData.append(
            'portada_institucional',
            window.AltaTesisArchivos.portadaInstitucional,
            'portada-institucional.png'
        );

        const botonGuardar = document.querySelector('.btn-guardar');
        cambiarEstadoBotonGuardar(botonGuardar, true);

        const response = await fetch(`${BASE_URL}/index.php/tesis/guardar`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (!response.ok || !data.ok) {
            throw new Error(data.message || 'No se pudo guardar la tesis.');
        }

        if (typeof mostrarModalTesisExito === 'function') {
            await mostrarModalTesisExito({
                autor: autorVisible,
                linea: obtenerNombreLineaPorId(lineaSeleccionada.dataset.idLinea)
            });
        } else {
            console.log(data.message || 'Tesis agregada correctamente.');
        }

        limpiarFormularioAltaTesis();

        if (typeof mostrarVistaLista === 'function') {
            mostrarVistaLista();
        }

        /*
            Recarga la página para actualizar:
            - total de tesis
            - lista de tesis
            - estadísticas del dashboard
            Esto evita tocar funciones que ya tienes funcionando.
        */
        window.location.reload();

    } catch (error) {
        if (typeof mostrarModalTesisError === 'function') {
            await mostrarModalTesisError(error.message);
        } else {
            console.error(error.message);
        }
    } finally {
        const botonGuardar = document.querySelector('.btn-guardar');
        cambiarEstadoBotonGuardar(botonGuardar, false);
    }
}

function obtenerValor(id) {
    const elemento = document.getElementById(id);
    return elemento ? elemento.value.trim() : '';
}

function obtenerEstadoTesis(fisico, digital) {
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

function validarDatosTesis(datos) {
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
        REGLA:
        - Si es solo Fisico: NO pide link.
        - Si es Digital: SÍ pide link.
        - Si es Digital y Fisico: SÍ pide link.
    */
    const requiereLinkDigital = datos.estado === 'Digital' || datos.estado === 'Digital y Fisico';

    if (requiereLinkDigital && !datos.url) {
        throw new Error('Agrega el link del archivo digital de la tesis.');
    }

    if (datos.url && !esUrlValida(datos.url)) {
        throw new Error('El link del archivo digital de la tesis no tiene un formato válido.');
    }

    if (!window.AltaTesisArchivos || !window.AltaTesisArchivos.pastaFisica) {
        throw new Error('Sube la imagen de la pasta física.');
    }

    if (!window.AltaTesisArchivos || !window.AltaTesisArchivos.portadaInstitucional) {
        throw new Error('Sube la imagen de la portada institucional.');
    }
}

function esUrlValida(url) {
    try {
        const urlObj = new URL(url);
        return urlObj.protocol === 'http:' || urlObj.protocol === 'https:';
    } catch (e) {
        return false;
    }
}

function obtenerNombreLineaPorId(idLinea) {
    const mapa = {
        '1': 'CSR',
        '2': 'TICs',
        '3': 'Geomática'
    };

    return mapa[String(idLinea)] || 'una línea diferente';
}

function cambiarEstadoBotonGuardar(boton, cargando) {
    if (!boton) return;

    if (cargando) {
        boton.disabled = true;
        boton.dataset.textoOriginal = boton.textContent;
        boton.textContent = 'Guardando...';
        boton.style.opacity = '0.7';
        boton.style.cursor = 'not-allowed';
    } else {
        boton.disabled = false;
        boton.textContent = boton.dataset.textoOriginal || 'Agregar';
        boton.style.opacity = '';
        boton.style.cursor = '';
    }
}

function cancelarAgregarTesis() {
    limpiarFormularioAltaTesis();

    if (typeof mostrarVistaLista === 'function') {
        mostrarVistaLista();
    }
}

function limpiarFormularioAltaTesis() {
    limpiarInput('titulo');
    limpiarInput('autor');
    limpiarInput('director');
    limpiarInput('idDirector');
    limpiarInput('idDirectorLinea');
    limpiarInput('fechaTesis');
    limpiarInput('url');

    limpiarInput('autorMatriculaHidden');
    limpiarInput('autorNombreHidden');
    limpiarInput('autorApellidoPaternoHidden');
    limpiarInput('autorApellidoMaternoHidden');
    limpiarInput('autorCorreoHidden');
    limpiarInput('autorSexoHidden');

    document.querySelectorAll('#agregarTesisPanel input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    const pastaInput = document.getElementById('pastaFisicaInput');
    const portadaInput = document.getElementById('portadaInstitucionalInput');

    if (pastaInput) pastaInput.value = '';
    if (portadaInput) portadaInput.value = '';

    const btnPasta = document.getElementById('btnPastaFisica');
    const btnPortada = document.getElementById('btnPortadaInstitucional');

    if (btnPasta) btnPasta.textContent = 'Subir imagen';
    if (btnPortada) btnPortada.textContent = 'Subir imagen';

    if (window.AltaTesisArchivos) {
        window.AltaTesisArchivos.pastaFisica = null;
        window.AltaTesisArchivos.portadaInstitucional = null;
    }
}

function limpiarInput(id) {
    const input = document.getElementById(id);

    if (input) {
        input.value = '';
    }
}

/* ==========================================
   CSRF TOKEN
========================================== */

function obtenerCsrfToken() {
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