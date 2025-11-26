function cargarResultados() {
    const params = new URLSearchParams({
        busqueda: document.querySelector('input[name="busqueda"]').value,
        estado: document.querySelector('select[name="estado"]').value,
        director: document.querySelector('select[name="director"]').value,
        anio: document.querySelector('select[name="anio"]').value
    });

    fetch('/repositorio_MIIDT/repositorio-miidt/public/php/busqueda_ajax.php?' + params.toString())
        .then(response => response.text())
        .then(html => {
            document.getElementById('contenedor-resultados').innerHTML = html;
        });
}

// 🟦 Evento al escribir en el buscador
document.querySelector('input[name="busqueda"]').addEventListener('input', () => {
    cargarResultados();
});

// 🟩 Evento al cambiar filtros
document.querySelectorAll('select').forEach(select => {
    select.addEventListener('change', () => {
        cargarResultados();
    });
});

