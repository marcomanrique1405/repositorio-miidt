/* ==========================================
   MODAL SUBIR IMÁGENES - ALTA DE TESIS
   Pasta física -> WEBP
   Portada institucional -> PNG
========================================== */

window.AltaTesisArchivos = {
    pastaFisica: null,
    portadaInstitucional: null
};

document.addEventListener('DOMContentLoaded', () => {
    const btnPasta = document.getElementById('btnPastaFisica');
    const inputPasta = document.getElementById('pastaFisicaInput');

    const btnPortada = document.getElementById('btnPortadaInstitucional');
    const inputPortada = document.getElementById('portadaInstitucionalInput');

    if (btnPasta && inputPasta) {
        btnPasta.addEventListener('click', () => {
            abrirModalImagen({
                titulo: 'Subir pasta',
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
            abrirModalImagen({
                titulo: 'Subir portada',
                extensionFinal: '.png',
                tipoSalida: 'image/png',
                calidad: 0.92,
                inputFile: inputPortada,
                boton: btnPortada,
                destino: 'portadaInstitucional'
            });
        });
    }
});

function abrirModalImagen(config) {
    cerrarModalImagen();

    let archivoOriginal = null;
    let archivoConvertido = null;

    const overlay = document.createElement('div');
    overlay.className = 'upload-tesis-overlay is-active';
    overlay.id = 'uploadTesisOverlay';

    overlay.innerHTML = `
        <div class="upload-tesis-modal" role="dialog" aria-modal="true">
            <div class="upload-tesis-header">
                <h2 class="upload-tesis-title">${config.titulo}</h2>
                <button type="button" class="upload-tesis-close" aria-label="Cerrar">&times;</button>
            </div>

            <div class="upload-tesis-body">
                <p class="upload-tesis-text">Agrega tu imagen aquí</p>

                <div class="upload-tesis-dropzone" id="uploadTesisDropzone">
                    <div class="upload-tesis-preview-wrap" id="uploadTesisPreviewWrap">
                        <img class="upload-tesis-preview" id="uploadTesisPreview" alt="Vista previa">
                        <button type="button" class="upload-tesis-remove" id="uploadTesisRemove">&times;</button>
                    </div>

                    <div class="upload-tesis-empty" id="uploadTesisEmpty">
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

                <button type="button" class="upload-tesis-submit" id="uploadTesisSubmit" disabled>
                    Subir
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    const closeBtn = overlay.querySelector('.upload-tesis-close');
    const dropzone = overlay.querySelector('#uploadTesisDropzone');
    const preview = overlay.querySelector('#uploadTesisPreview');
    const previewWrap = overlay.querySelector('#uploadTesisPreviewWrap');
    const emptyContent = overlay.querySelector('#uploadTesisEmpty');
    const removeBtn = overlay.querySelector('#uploadTesisRemove');
    const submitBtn = overlay.querySelector('#uploadTesisSubmit');

    closeBtn.addEventListener('click', cerrarModalImagen);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            cerrarModalImagen();
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
            archivoOriginal = file;
            archivoConvertido = await prepararImagen(file, config.tipoSalida, config.calidad);

            mostrarPreview(file, preview, previewWrap, emptyContent);
            submitBtn.disabled = false;

        } catch (error) {
            alert(error.message);
            limpiarSeleccion();
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
            archivoOriginal = file;
            archivoConvertido = await prepararImagen(file, config.tipoSalida, config.calidad);

            mostrarPreview(file, preview, previewWrap, emptyContent);
            submitBtn.disabled = false;

        } catch (error) {
            alert(error.message);
            limpiarSeleccion();
        }
    });

    removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        limpiarSeleccion();
    });

    submitBtn.addEventListener('click', () => {
        if (!archivoConvertido) {
            alert('Selecciona una imagen.');
            return;
        }

        window.AltaTesisArchivos[config.destino] = archivoConvertido;

        if (config.destino === 'pastaFisica') {
            config.boton.textContent = 'Imagen cargada';
        }

        if (config.destino === 'portadaInstitucional') {
            config.boton.textContent = 'Imagen cargada';
        }

        cerrarModalImagen();
    });

    function limpiarSeleccion() {
        archivoOriginal = null;
        archivoConvertido = null;
        config.inputFile.value = '';

        preview.src = '';
        previewWrap.classList.remove('has-image');
        emptyContent.style.display = 'block';
        submitBtn.disabled = true;
    }
}

function cerrarModalImagen() {
    const modal = document.getElementById('uploadTesisOverlay');

    if (modal) {
        modal.remove();
    }
}

async function prepararImagen(file, tipoSalida, calidad) {
    validarImagen(file);

    return await convertirImagen(file, tipoSalida, calidad);
}

function validarImagen(file) {
    if (!file.type || !file.type.startsWith('image/')) {
        throw new Error('El archivo seleccionado debe ser una imagen.');
    }

    const maxSize = 8 * 1024 * 1024;

    if (file.size > maxSize) {
        throw new Error('La imagen no debe pesar más de 8 MB.');
    }
}

function mostrarPreview(file, preview, previewWrap, emptyContent) {
    const url = URL.createObjectURL(file);

    preview.onload = () => {
        URL.revokeObjectURL(url);
    };

    preview.src = url;
    previewWrap.classList.add('has-image');
    emptyContent.style.display = 'none';
}

async function convertirImagen(file, tipoSalida, calidad) {
    const image = await cargarImagen(file);

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

function cargarImagen(file) {
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