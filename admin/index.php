<?php
session_start();

$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

if (empty($_SESSION['admin_auth'])) {
    header("Location: $base/views/login.php");
    exit;
}

header("Location: $base/views/dashboard.php");
