<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/topbar.php'; ?>

<div class="admin-dashboard__page">
    <div class="admin-dashboard__body">

        <!-- ===================== -->
        <!-- SIDEBAR / FILTROS -->
        <!-- ===================== -->
        <aside class="admin-dashboard__sidebar-placeholder">
            <?php require_once __DIR__ . '/../components/filtros-tesis.php'; ?>
        </aside>

        <!-- ===================== -->
        <!-- CONTENIDO PRINCIPAL -->
        <!-- ===================== -->
        <main class="admin-dashboard__main">

            <!-- ===================== -->
            <!-- TARJETAS DE ESTADÍSTICAS -->
            <!-- ===================== -->
            <?php require_once __DIR__ . '/../components/stats.php'; ?>

            <!-- ===================== -->
            <!-- CONTENEDOR DE VISTAS -->
            <!-- ===================== -->
            <div id="contenedorPrincipal">

                <!-- ===================== -->
                <!-- VISTA LISTA DE TESIS -->
                <!-- ===================== -->
                <div id="vistaLista">
                    <?php require_once __DIR__ . '/../components/tesis-panel.php'; ?>
                </div>

                <!-- ===================== -->
                <!-- VISTA AGREGAR TESIS -->
                <!-- ===================== -->
                <div id="vistaAgregar" class="admin-hidden">
                    <?php require_once __DIR__ . '/../partials/agregar-tesis.php'; ?>
                </div>

                <!-- ===================== -->
                <!-- VISTA EDITAR TESIS -->
                <!-- ===================== -->
                <div id="vistaEditar" class="admin-hidden">
                    <?php require_once __DIR__ . '/../partials/editar-tesis.php'; ?>
                </div>

            </div>

            <div class="admin-dashboard__content-placeholder"></div>

        </main>

    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>