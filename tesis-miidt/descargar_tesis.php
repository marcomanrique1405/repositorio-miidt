<?php
require_once __DIR__ . '/../src/modules/DownloadModule.php';

$downloadModule = new DownloadModule();

// Obtener IP del usuario
$ip_usuario = $_SERVER['REMOTE_ADDR'];

// Verificar acceso
if (!$downloadModule->checkAccess($ip_usuario)) {
    $downloadModule->renderAccessDenied($ip_usuario);
}

$url_archivo = isset($_GET['url']) ? $_GET['url'] : '';

$downloadModule->processDownload($url_archivo);
?>