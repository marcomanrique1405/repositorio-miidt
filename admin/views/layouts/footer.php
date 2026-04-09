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

</body>
</html>