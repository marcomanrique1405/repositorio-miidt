/* ==========================================
   ELIMINAR TESIS
   Modal bonito + eliminación por AJAX
========================================== */

document.addEventListener('DOMContentLoaded', () => {
    configurarEliminarTesis();
});

function configurarEliminarTesis() {
    document.addEventListener('click', async (e) => {
        const btnEliminar = e.target.closest('.tesis-item__delete');

        if (!btnEliminar) return;

        e.preventDefault();
        e.stopPropagation();

        const idTesis = obtenerIdTesisEliminar(btnEliminar);

        if (!idTesis) {
            await mostrarErrorEliminarTesis('No se encontró el identificador de la tesis.');
            return;
        }

        const tarjeta = btnEliminar.closest('.tesis-item');

        const titulo = tarjeta?.querySelector('.tesis-item__title')?.textContent?.trim() || 'Esta tesis';
        const autor = obtenerTextoMetaEliminar(tarjeta, 'Autor:') || 'Autor no disponible';

        const confirmado = await mostrarModalConfirmarEliminar({
            titulo,
            autor
        });

        if (!confirmado) return;

        await eliminarTesis(idTesis);
    });
}

function obtenerIdTesisEliminar(btnEliminar) {
    if (!btnEliminar) return '';

    const posiblesIds = [
        btnEliminar.getAttribute('data-id'),
        btnEliminar.getAttribute('data-id-tesis'),
        btnEliminar.dataset ? btnEliminar.dataset.id : '',
        btnEliminar.dataset ? btnEliminar.dataset.idTesis : ''
    ];

    for (const id of posiblesIds) {
        if (id && String(id).trim() !== '') {
            return String(id).trim();
        }
    }

    const tarjeta = btnEliminar.closest('.tesis-item');

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

function obtenerTextoMetaEliminar(tarjeta, etiqueta) {
    if (!tarjeta) return '';

    const parrafos = tarjeta.querySelectorAll('p');

    for (const p of parrafos) {
        const texto = p.textContent.trim();

        if (texto.startsWith(etiqueta)) {
            return texto.replace(etiqueta, '').trim();
        }
    }

    return '';
}

async function eliminarTesis(idTesis) {
    try {
        const response = await fetch(`${BASE_URL}/index.php/tesis/eliminar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_tesis: idTesis
            })
        });

        const data = await response.json();

        if (!response.ok || !data.ok) {
            throw new Error(data.message || 'No se pudo eliminar la tesis.');
        }

        /*
           IMPORTANTE:
           No usamos mostrarModalTesisExito porque ese modal es de REGISTRAR.
           Aquí usamos el modal propio de eliminar.
        */
        await mostrarModalEliminarResultado({
            tipo: 'success',
            titulo: 'Tesis eliminada exitosamente',
            mensaje: ''
        });

        if (typeof window.recargarListaTesis === 'function') {
            window.recargarListaTesis();
        } else {
            window.location.reload();
        }

    } catch (error) {
        await mostrarErrorEliminarTesis(error.message);
    }
}

/* ==========================================
   MODAL CONFIRMAR ELIMINACIÓN
========================================== */

function mostrarModalConfirmarEliminar({ titulo, autor }) {
    return new Promise((resolve) => {
        cerrarModalEliminar();

        const overlay = document.createElement('div');
        overlay.className = 'eliminar-tesis-overlay';
        overlay.id = 'eliminarTesisOverlay';

        overlay.innerHTML = `
            <div class="eliminar-tesis-modal" role="dialog" aria-modal="true">
                <div class="eliminar-tesis-icon-wrap">
                    <div class="eliminar-tesis-icon">!</div>
                </div>

                <h2 class="eliminar-tesis-title">¿Eliminar esta tesis?</h2>

                <p class="eliminar-tesis-text">
                    Esta acción eliminará la tesis del repositorio.
                </p>

                <div class="eliminar-tesis-info">
                    <strong>${escaparHtmlEliminar(titulo)}</strong>
                    <span>${escaparHtmlEliminar(autor)}</span>
                </div>

                <div class="eliminar-tesis-actions">
                    <button type="button" class="eliminar-tesis-btn cancelar">
                        Cancelar
                    </button>

                    <button type="button" class="eliminar-tesis-btn eliminar">
                        Sí, eliminar
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);

        const btnCancelar = overlay.querySelector('.eliminar-tesis-btn.cancelar');
        const btnEliminar = overlay.querySelector('.eliminar-tesis-btn.eliminar');

        btnCancelar.addEventListener('click', () => {
            cerrarModalEliminar();
            resolve(false);
        });

        btnEliminar.addEventListener('click', () => {
            cerrarModalEliminar();
            resolve(true);
        });

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                cerrarModalEliminar();
                resolve(false);
            }
        });
    });
}

async function mostrarErrorEliminarTesis(mensaje) {
    if (typeof mostrarModalTesisError === 'function') {
        await mostrarModalTesisError(mensaje);
        return;
    }

    await mostrarModalEliminarResultado({
        tipo: 'error',
        titulo: 'Error',
        mensaje
    });
}

function mostrarModalEliminarResultado({ tipo, titulo, mensaje }) {
    return new Promise((resolve) => {
        cerrarModalEliminar();

        const overlay = document.createElement('div');
        overlay.className = 'eliminar-tesis-overlay';
        overlay.id = 'eliminarTesisOverlay';

        const esError = tipo === 'error';

        overlay.innerHTML = `
            <div class="eliminar-tesis-modal eliminar-tesis-modal--resultado" role="dialog" aria-modal="true">
                <div class="eliminar-tesis-icon-wrap ${esError ? 'error' : 'success'}">
                    <div class="eliminar-tesis-icon ${esError ? 'error' : 'success'}">
                        ${esError ? '×' : '✓'}
                    </div>
                </div>

                <h2 class="eliminar-tesis-title eliminar-tesis-title--resultado">
                    ${escaparHtmlEliminar(titulo)}
                </h2>

                ${
                    mensaje
                        ? `<p class="eliminar-tesis-text">${escaparHtmlEliminar(mensaje)}</p>`
                        : ''
                }
            </div>
        `;

        document.body.appendChild(overlay);

        /*
           Para que se vea como la tercera imagen:
           aparece el mensaje y se cierra solo.
        */
        setTimeout(() => {
            cerrarModalEliminar();
            resolve(true);
        }, 1300);

        overlay.addEventListener('click', () => {
            cerrarModalEliminar();
            resolve(true);
        });
    });
}

function cerrarModalEliminar() {
    const modal = document.getElementById('eliminarTesisOverlay');

    if (modal) {
        modal.remove();
    }
}

function escaparHtmlEliminar(valor) {
    return String(valor || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}