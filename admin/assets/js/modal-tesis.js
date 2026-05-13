/* ==========================================
   MODALES PERSONALIZADOS PARA ALTA DE TESIS
========================================== */

function mostrarModalTesisExito({ autor = '', linea = '' } = {}) {
    return new Promise(resolve => {
        cerrarModalTesis();

        const overlay = document.createElement('div');
        overlay.className = 'tesis-modal-overlay';
        overlay.id = 'tesisModalOverlay';

        overlay.innerHTML = `
            <div class="tesis-modal-card" role="dialog" aria-modal="true">
                <div class="tesis-modal-icon-wrap success">
                    <div class="tesis-modal-icon success">✓</div>
                </div>

                <h2 class="tesis-modal-title">Tesis registrada exitosamente</h2>

                <p class="tesis-modal-text">Autor: ${escaparHTML(autor)}</p>
                <p class="tesis-modal-text">Línea: ${escaparHTML(linea)}</p>

                <button type="button" class="tesis-modal-button" id="tesisModalAceptar">
                    Aceptar
                </button>
            </div>
        `;

        document.body.appendChild(overlay);

        const btn = document.getElementById('tesisModalAceptar');

        btn.addEventListener('click', () => {
            cerrarModalTesis();
            resolve();
        });
    });
}

function mostrarModalTesisError(mensaje = 'No se pudo completar la acción.') {
    return new Promise(resolve => {
        cerrarModalTesis();

        const overlay = document.createElement('div');
        overlay.className = 'tesis-modal-overlay';
        overlay.id = 'tesisModalOverlay';

        overlay.innerHTML = `
            <div class="tesis-modal-card" role="dialog" aria-modal="true">
                <div class="tesis-modal-icon-wrap error">
                    <div class="tesis-modal-icon error">×</div>
                </div>

                <h2 class="tesis-modal-title">No se pudo registrar la tesis</h2>

                <p class="tesis-modal-message">${escaparHTML(mensaje)}</p>

                <button type="button" class="tesis-modal-button" id="tesisModalAceptar">
                    Aceptar
                </button>
            </div>
        `;

        document.body.appendChild(overlay);

        const btn = document.getElementById('tesisModalAceptar');

        btn.addEventListener('click', () => {
            cerrarModalTesis();
            resolve();
        });
    });
}

function cerrarModalTesis() {
    const modal = document.getElementById('tesisModalOverlay');

    if (modal) {
        modal.remove();
    }
}

function escaparHTML(texto) {
    return String(texto)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}