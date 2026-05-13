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

            render(data);

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
            const div = document.createElement('div');
            div.className = 'tesis-item';

            div.innerHTML = `
                <div class="tesis-item__cover">
                    <img 
                        src="${t.imagen}" 
                        alt="Portada de tesis"
                        onerror="this.src='${BASE_URL}/assets/img/default.jpg'"
                    >
                </div>

                <div class="tesis-item__content">
                    <h3 class="tesis-item__title">${t.titulo}</h3>
                    <p><strong>Autor:</strong> ${t.autor}</p>
                    <p><strong>Director:</strong> ${t.director}</p>
                    <p><strong>LIES:</strong> ${t.lies}</p>
                    <p><strong>Estado:</strong> ${t.estado}</p>

                    <div class="tesis-item__actions">
                        <button class="tesis-item__edit" data-id="${t.id_tesis}">Editar</button>
                        <button class="tesis-item__delete" data-id="${t.id_tesis}">Eliminar</button>
                    </div>
                </div>

                <div class="tesis-item__year">${t.anio}</div>
            `;

            contenedor.appendChild(div);
        });
    }

    buscar();

});