<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/topbar.php'; ?>

<div class="admin-dashboard__page">
    <div class="admin-dashboard__body">
        <aside class="admin-dashboard__sidebar-placeholder">
            <?php require_once __DIR__ . '/../components/filtros-tesis.php'; ?>
        </aside>

        <main class="admin-dashboard__main">
            <?php require_once __DIR__ . '/../components/stats.php'; ?>

            <div id="contenedorPrincipal">

                <div id="vistaLista">
                    <?php require_once __DIR__ . '/../components/tesis-panel.php'; ?>
                </div>

                <div id="vistaAgregar" class="admin-hidden">
                    <?php require_once __DIR__ . '/../partials/agregar-tesis.php'; ?>
                </div>

            </div>

            <div class="admin-dashboard__content-placeholder"></div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>