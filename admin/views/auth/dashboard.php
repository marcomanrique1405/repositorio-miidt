<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/topbar.php'; ?>

<div class="admin-dashboard__page">
    <div class="admin-dashboard__body">
        <aside class="admin-dashboard__sidebar-placeholder"></aside>

        <main class="admin-dashboard__main">
            <?php require_once __DIR__ . '/../components/stats.php'; ?>
            <?php require_once __DIR__ . '/../components/tesis-panel.php'; ?>

            <div class="admin-dashboard__content-placeholder"></div>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>