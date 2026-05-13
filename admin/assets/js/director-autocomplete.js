document.addEventListener('DOMContentLoaded', () => {
    const inputDirector = document.getElementById('director');
    const inputIdDirector = document.getElementById('idDirector');
    const inputIdDirectorLinea = document.getElementById('idDirectorLinea');
    const sugerencias = document.getElementById('director-sugerencias');

    if (!inputDirector || !inputIdDirector || !sugerencias) return;

    let timeout = null;

    inputDirector.addEventListener('input', () => {
        inputIdDirector.value = '';

        if (inputIdDirectorLinea) {
            inputIdDirectorLinea.value = '';
        }

        clearTimeout(timeout);

        const query = inputDirector.value.trim();

        if (query.length < 2) {
            cerrarSugerencias();
            return;
        }

        timeout = setTimeout(() => {
            buscarDirectores(query);
        }, 250);
    });

    async function buscarDirectores(query) {
        try {
            const url = `${BASE_URL}/index.php/directores/buscar?q=${encodeURIComponent(query)}`;

            const res = await fetch(url);

            if (!res.ok) {
                cerrarSugerencias();
                return;
            }

            const data = await res.json();

            renderSugerencias(data);

        } catch (e) {
            cerrarSugerencias();
        }
    }

    function renderSugerencias(lista) {
        sugerencias.innerHTML = '';

        if (!Array.isArray(lista) || lista.length === 0) {
            cerrarSugerencias();
            return;
        }

        lista.forEach(director => {
            const item = document.createElement('div');
            item.className = 'director-sugerencias__item';
            item.textContent = director.nombre_completo;

            item.addEventListener('click', () => {
                inputDirector.value = director.nombre_completo;
                inputIdDirector.value = director.id_director;

                if (inputIdDirectorLinea) {
                    inputIdDirectorLinea.value = director.id_linea;
                }

                cerrarSugerencias();
            });

            sugerencias.appendChild(item);
        });

        sugerencias.style.display = 'block';
    }

    function cerrarSugerencias() {
        sugerencias.innerHTML = '';
        sugerencias.style.display = 'none';
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.director-autocomplete-wrap')) {
            cerrarSugerencias();
        }
    });
});