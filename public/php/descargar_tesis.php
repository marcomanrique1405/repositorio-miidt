<?php
// descargar_tesis.php

// Incluir el módulo de descargas
require_once '../../src/modules/DownloadModule.php';

// Instanciar el módulo
$downloadModule = new DownloadModule();

// Obtener IP del usuario
$ip_usuario = $_SERVER['REMOTE_ADDR'];

// Verificar acceso
if (!$downloadModule->checkAccess($ip_usuario)) {
    $downloadModule->renderAccessDenied($ip_usuario);
}

// Procesar descarga
$url_archivo = isset($_GET['url']) ? $_GET['url'] : '';
$downloadModule->processDownload($url_archivo);
?>