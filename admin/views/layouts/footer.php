<?php
$base = defined('ADMIN_BASE')
    ? ADMIN_BASE
    : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$base = ($base === '/' ? '' : $base);
?>

</div>

<script>
    const BASE_URL = "<?= htmlspecialchars($base) ?>";
</script>

<script>
/* ==========================================
   SESIÓN EXPIRADA POR INACTIVIDAD
   Detecta respuestas AJAX con redirect
========================================== */

(function () {
    if (!window.fetch) return;

    const fetchOriginal = window.fetch;

    window.fetch = async function () {
        const response = await fetchOriginal.apply(this, arguments);

        try {
            const responseClonada = response.clone();
            const contentType = responseClonada.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                const data = await responseClonada.json();

                if (
                    data &&
                    data.redirect &&
                    (
                        response.status === 440 ||
                        response.status === 403 ||
                        data.error === 'Sesión expirada por inactividad'
                    )
                ) {
                    window.location.href = data.redirect;
                }
            }
        } catch (e) {
            /*
                No hacemos nada.
                Esto evita romper peticiones que no sean JSON.
            */
        }

        return response;
    };
})();
</script>

<!-- <script src="<?= htmlspecialchars($base) ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->
<script src="<?= htmlspecialchars($base) ?>/assets/js/tesis.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/filtros.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/checkbox-unico.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/autor-popup.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/select-fix.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/vistas-dashboard.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/select-anio-espacio.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/menu-mobile.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/upload-imagenes-tesis.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/director-autocomplete.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/guardar-tesis.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/modal-tesis.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/editar-tesis.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/eliminar-tesis.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/calendario-tesis.js"></script>

</body>
</html>