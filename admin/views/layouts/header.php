<?php
declare(strict_types=1);

$base = defined('ADMIN_BASE')
    ? ADMIN_BASE
    : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$base = ($base === '/' ? '' : $base);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Dashboard</title>

    <link rel="icon" href="<?= htmlspecialchars($base) ?>/assets/img/favicon.ico" type="image/x-icon">


    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" href="<?= htmlspecialchars($base) ?>/assets/vendor/bootstrap/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="<?= htmlspecialchars($base) ?>/assets/css/auth/style.css">
</head>
<body class="admin-dashboard">

<div class="admin-layout">