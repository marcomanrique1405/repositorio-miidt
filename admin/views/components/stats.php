<div class="admin-dashboard__stats"> 

    <div class="admin-dashboard__stat admin-dashboard__stat--tesis">
        <div class="admin-dashboard__stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="currentColor" d="M6 13H2c-.6 0-1 .4-1 1v8c0 .6.4 1 1 1h4c.6 0 1-.4 1-1v-8c0-.6-.4-1-1-1m16-4h-4c-.6 0-1 .4-1 1v12c0 .6.4 1 1 1h4c.6 0 1-.4 1-1V10c0-.6-.4-1-1-1m-8-8h-4c-.6 0-1 .4-1 1v20c0 .6.4 1 1 1h4c.6 0 1-.4 1-1V2c0-.6-.4-1-1-1"/>
            </svg>
        </div>
        <div class="admin-dashboard__stat-content">
            <span id="stat-tesis" class="admin-dashboard__stat-number">
                <?= $totalTesis ?? 0 ?>
            </span>
            <span class="admin-dashboard__stat-label">TESIS</span>
        </div>
    </div>

    <div class="admin-dashboard__stat admin-dashboard__stat--directores">
        <div class="admin-dashboard__stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="currentColor" d="M12 3L1 9l11 6l9-4.91V17h2V9z"/>
                <path fill="currentColor" d="M5 12.18V17l7 4l7-4v-4.82l-7 3.82z"/>
            </svg>
        </div>
        <div class="admin-dashboard__stat-content">
            <span id="stat-directores" class="admin-dashboard__stat-number">
                <?= $totalDirectores ?? 0 ?>
            </span>
            <span class="admin-dashboard__stat-label">DIRECTORES</span>
        </div>
    </div>

    <div class="admin-dashboard__stat admin-dashboard__stat--fisico">
        <div class="admin-dashboard__stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                <path fill="currentColor" d="M4.5 2c-.277 0-.5.223-.5.5v9c0 .277.223.5.5.5h1c.277 0 .5-.223.5-.5v-9c0-.277-.223-.5-.5-.5zm3 1c-.277 0-.5.223-.5.5v8c0 .277.223.5.5.5h1c.277 0 .5-.223.5-.5v-8c0-.277-.223-.5-.5-.5zm3.549.99a.5.5 0 0 0-.102.018l-.965.258a.5.5 0 0 0-.353.613l1.81 6.762a.5.5 0 0 0 .614.353l.965-.26a.5.5 0 0 0 .353-.611l-1.81-6.762a.5.5 0 0 0-.512-.37M1.5 4c-.277 0-.5.223-.5.5v7c0 .277.223.5.5.5h1c.277 0 .5-.223.5-.5v-7c0-.277-.223-.5-.5-.5zm0 9a.499.499 0 1 0 0 1h12a.499.499 0 1 0 0-1z"/>
            </svg>
        </div>
        <div class="admin-dashboard__stat-content">
            <span id="stat-fisico" class="admin-dashboard__stat-number">
                <?= $totalFisico ?? 0 ?>
            </span>
            <span class="admin-dashboard__stat-label">FÍSICO</span>
        </div>
    </div>

    <div class="admin-dashboard__stat admin-dashboard__stat--digital">
        <div class="admin-dashboard__stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" fill-opacity="0" d="M7 19c-2.5 0 -4 -2 -4 -4c0 -2 1.5 -4 4 -4c0 -3.5 2 -6 5 -6c3 0 5 2.5 5 5v1c2.5 0 4 2 4 4c0 2 -1.5 4 -4 4Z"><animate fill="freeze" attributeName="fill-opacity" begin="0.9s" dur="0.4s" to="1"/></path><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="22" d="M12 19h-5c-2.5 0 -4 -2 -4 -4c0 -2 1.5 -4 4 -4c1 0 1.5 0.5 1.5 0.5M12 19h5c2.5 0 4 -2 4 -4c0 -2 -1.5 -4 -4 -4c-1 0 -1.5 0.5 -1.5 0.5"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.5s" values="22;0"/></path><path stroke-dasharray="12" stroke-dashoffset="12" d="M7 11v-1c0 -2.5 2 -5 5 -5M17 11v-1c0 -2.5 -2 -5 -5 -5"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.4s" to="0"/></path></g></svg>
        </div>
        <div class="admin-dashboard__stat-content">
            <span id="stat-digital" class="admin-dashboard__stat-number">
                <?= $totalDigital ?? 0 ?>
            </span>
            <span class="admin-dashboard__stat-label">DIGITAL</span>
        </div>
    </div>

</div>

<script>
async function actualizarStats() {
    try {
        const response = await fetch('<?= htmlspecialchars($base) ?>/index.php/stats');
        const data = await response.json();

        document.getElementById('stat-tesis').textContent = data.tesis;
        document.getElementById('stat-directores').textContent = data.directores;
        document.getElementById('stat-fisico').textContent = data.fisico;
        document.getElementById('stat-digital').textContent = data.digital;

    } catch (error) {
        console.error('Error actualizando stats:', error);
    }
}

actualizarStats();

setInterval(actualizarStats, 5000);
</script>

<script>
function animarContador(elemento, valorFinal, duracion = 1000) {
    let inicio = 0;
    let incremento = valorFinal / (duracion / 16);

    function actualizar() {
        inicio += incremento;

        if (inicio >= valorFinal) {
            elemento.textContent = valorFinal;
        } else {
            elemento.textContent = Math.floor(inicio);
            requestAnimationFrame(actualizar);
        }
    }

    actualizar();
}

function aplicarAnimacion() {
    const tesis = document.getElementById('stat-tesis');
    const directores = document.getElementById('stat-directores');
    const fisico = document.getElementById('stat-fisico');
    const digital = document.getElementById('stat-digital');

    animarContador(tesis, parseInt(tesis.textContent));
    animarContador(directores, parseInt(directores.textContent));
    animarContador(fisico, parseInt(fisico.textContent));
    animarContador(digital, parseInt(digital.textContent));
}

setTimeout(aplicarAnimacion, 300);
</script>
