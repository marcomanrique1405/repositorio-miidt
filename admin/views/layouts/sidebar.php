<?php
$base = defined('ADMIN_BASE')
    ? ADMIN_BASE
    : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$base = ($base === '/' ? '' : $base);
?>

<aside class="admin-sidebar">

    <div class="admin-sidebar__brand">
        <img 
            src="<?= htmlspecialchars($base) ?>/assets/img/logo.webp" 
            alt="Logo"
            class="admin-sidebar__logo"
        >
        <span class="admin-sidebar__title">MIIDT</span>
    </div>

    <nav class="admin-sidebar__menu">

        <a href="<?= htmlspecialchars($base) ?>/index.php/dashboard" class="admin-sidebar__link active">
            Dashboard
        </a>

        <a href="#" class="admin-sidebar__link">
            Tesis
        </a>

        <a href="#" class="admin-sidebar__link">
            Directores
        </a>

        <a href="#" class="admin-sidebar__link">
            Configuración
        </a>

    </nav>

</aside>

