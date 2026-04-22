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

<script src="<?= htmlspecialchars($base) ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/tesis.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/filtros.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/checkbox-unico.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/select-fix.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/vistas-dashboard.js"></script>
<script src="<?= htmlspecialchars($base) ?>/assets/js/select-anio-espacio.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
flatpickr("#fechaTesis", {
    dateFormat: "d/m/Y",
    allowInput: false
});
</script>


</body>
</html>