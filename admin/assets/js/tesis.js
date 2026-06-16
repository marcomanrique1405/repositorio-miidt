document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('buscador-tesis');
    const contenedor = document.getElementById('contenedor-tesis');
    const total = document.getElementById('tesis-total');

    if (!input || !contenedor || !total) return;

    let timeout;

    input.addEventListener('input', () => {
        clearTimeout(timeout);

        timeout = setTimeout(() => {
            buscar(input.value.trim());
        }, 300);
    });

    async function buscar(query = '') {
        try {
            const url = `${BASE_URL}/index.php/tesis/buscar?q=${encodeURIComponent(query)}`;

            const res = await fetch(url);

            if (!res.ok) {
                throw new Error("Error HTTP: " + res.status);
            }

            const data = await res.json();

            render(Array.isArray(data) ? data : []);

        } catch (e) {
            contenedor.innerHTML = `<p>No se pudieron cargar las tesis.</p>`;
            total.textContent = `0 Tesis`;
        }
    }

    function render(lista) {
        contenedor.innerHTML = '';
        total.textContent = `${lista.length} Tesis`;

        if (!lista.length) {
            contenedor.innerHTML = `<p>No hay resultados</p>`;
            return;
        }

        lista.forEach(t => {

            /*
               IMPORTANTE:
               Tomamos el ID de forma segura.
               Normalmente viene como id_tesis, pero dejamos respaldos por si algún endpoint
               lo devuelve con otro nombre.
            */
            const idTesis = String(
                t.id_tesis ??
                t.idTesis ??
                t.id ??
                ''
            ).trim();

            const titulo = escaparHTML(t.titulo || '');
            const autor = escaparHTML(t.autor || '');
            const director = escaparHTML(t.director || '');
            const lies = escaparHTML(t.lies || '');
            const estado = escaparHTML(t.estado || '');
            const anio = escaparHTML(t.anio || '');

            const imagen = t.imagen && String(t.imagen).trim() !== ''
                ? String(t.imagen).trim()
                : `${BASE_URL}/assets/img/default.jpg`;

            const div = document.createElement('div');
            div.className = 'tesis-item';

            /*
               Guardamos el id_tesis también en la tarjeta completa.
               Así, aunque otro script intente leerlo desde el contenedor,
               siempre estará disponible.
            */
            div.setAttribute('data-id', idTesis);
            div.setAttribute('data-id-tesis', idTesis);

            div.innerHTML = `
                <div class="tesis-item__cover">
                    <img 
                        src="${escaparAtributo(imagen)}" 
                        alt="Portada de tesis"
                        onerror="this.src='${BASE_URL}/assets/img/default.jpg'"
                    >
                </div>

                <div class="tesis-item__content">
                    <h3 class="tesis-item__title">${titulo}</h3>
                    <p><strong>Autor:</strong> ${autor}</p>
                    <p><strong>Director:</strong> ${director}</p>
                    <p><strong>LIES:</strong> ${lies}</p>
                    <p><strong>Estado:</strong> ${estado}</p>

                    <div class="tesis-item__actions">
                        <button 
                            type="button"
                            class="tesis-item__edit" 
                            data-id="${escaparAtributo(idTesis)}"
                            data-id-tesis="${escaparAtributo(idTesis)}"
                            aria-label="Editar tesis"
                        >
                            <span class="tesis-item__action-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="m7 17.013l4.413-.015l9.632-9.54c.378-.378.586-.88.586-1.414s-.208-1.036-.586-1.414l-1.586-1.586c-.756-.756-2.075-.752-2.825-.003L7 12.583zM18.045 4.458l1.589 1.583l-1.597 1.582l-1.586-1.585zM9 13.417l6.03-5.973l1.586 1.586l-6.029 5.971L9 15.006z"/>
                                    <path fill="currentColor" d="M5 21h14c1.103 0 2-.897 2-2v-8.668l-2 2V19H8.158c-.026 0-.053.01-.079.01c-.033 0-.066-.009-.1-.01H5V5h6.847l2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2"/>
                                </svg>
                            </span>
                            <span>Editar</span>
                        </button>

                        <button 
                            type="button"
                            class="tesis-item__delete" 
                            data-id="${escaparAtributo(idTesis)}"
                            data-id-tesis="${escaparAtributo(idTesis)}"
                            aria-label="Eliminar tesis"
                        >
                            <span class="tesis-item__action-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM19 4h-3.5l-1-1h-5l-1 1H5v2h14z"/>
                                </svg>
                            </span>
                            <span>Eliminar</span>
                        </button>
                    </div>
                </div>

                <div class="tesis-item__year">${anio}</div>
            `;

            /*
               Refuerzo extra:
               Después de crear el HTML, volvemos a poner los atributos directamente
               en los botones para asegurar que nunca queden vacíos.
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

    /*
       Dejamos esta función disponible globalmente.
       Sirve para recargar la lista desde otros archivos JS después de editar,
       guardar o eliminar sin romper este archivo.
    */
    window.recargarListaTesis = function () {
        buscar(input.value.trim());
    };

    buscar();

});

/* ==========================================
   HELPERS DE SEGURIDAD
========================================== */

function escaparHTML(valor) {
    return String(valor)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function escaparAtributo(valor) {
    return escaparHTML(valor);
}