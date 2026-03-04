<?php
session_start();

// Base automática: /REPOSITORIO_MIIDT/admin
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

if (empty($_SESSION['admin_auth'])) {
    header("Location: $base/views/login.php");
    exit;
}

header("Location: $base/views/dashboard.php");
exit;