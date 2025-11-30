<?php
// descargar_tesis.php

// 🔹 CONFIGURACIÓN DE RED
$ips_permitidas = [
    '192.168.0.0/24',
    '127.0.0.1',
    '::1',
    // '10.15.64.0/18', // Producción
];

// 🔹 Función para verificar IP
function ip_en_rango($ip, $rango) {
    if (strpos($rango, '/') === false) {
        return $ip === $rango;
    }
    
    list($subnet, $mask) = explode('/', $rango);
    $ip_long = ip2long($ip);
    $subnet_long = ip2long($subnet);
    
    if ($ip_long === false || $subnet_long === false) {
        return false;
    }
    
    $mask_long = -1 << (32 - (int)$mask);
    $subnet_long &= $mask_long;
    
    return ($ip_long & $mask_long) == $subnet_long;
}

$ip_usuario = $_SERVER['REMOTE_ADDR'];
$acceso_permitido = false;

foreach ($ips_permitidas as $rango) {
    if (ip_en_rango($ip_usuario, $rango)) {
        $acceso_permitido = true;
        break;
    }
}

// 🔹 Si no tiene acceso
if (!$acceso_permitido) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acceso Restringido</title>
        <link rel="stylesheet" href="/repositorio_MIIDT/repositorio-miidt/node_modules/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-lg border-danger">
                        <div class="card-body text-center p-5">
                            <i class="fas fa-wifi text-danger mb-3" style="font-size: 4rem;"></i>
                            <h2 class="text-danger mb-3">Acceso Restringido</h2>
                            <p class="lead mb-3">Las tesis solo pueden descargarse desde la <strong>red WiFi de la institución</strong>.</p>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Conéctate a la red institucional para descargar.
                            </div>
                            <p class="text-muted small mt-4">
                                <i class="fas fa-network-wired"></i> Tu IP: <code><?php echo htmlspecialchars($ip_usuario); ?></code>
                            </p>
                            <a href="javascript:history.back()" class="btn btn-secondary mt-3">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 🔹 PROCESAR DESCARGA
if (!isset($_GET['url']) || empty($_GET['url'])) {
    http_response_code(400);
    die("Error: No se especificó URL");
}

$url_archivo = $_GET['url'];

// 🔹 DETECTAR SI ES GOOGLE DRIVE
if (strpos($url_archivo, 'drive.google.com') !== false) {
    // Es un enlace de Google Drive
    
    // Extraer el ID del archivo
    preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url_archivo, $matches);
    
    if (isset($matches[1])) {
        $file_id = $matches[1];
        // Convertir a URL de descarga directa
         $download_url = "https://drive.google.com/file/d/" . $file_id . "/view";  
        
        // Redirigir a Google Drive
        header("Location: " . $download_url);
        exit;
    } else {
        http_response_code(400);
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Error</title>
            <link rel="stylesheet" href="/repositorio_MIIDT/repositorio-miidt/node_modules/bootstrap/dist/css/bootstrap.min.css">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <div class="alert alert-danger text-center">
                    <h3><i class="fas fa-exclamation-triangle"></i> URL Inválida</h3>
                    <p>No se pudo procesar el enlace de Google Drive.</p>
                    <a href="javascript:history.back()" class="btn btn-secondary">Volver</a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// 🔹 SI ES UN ARCHIVO LOCAL
$ruta_completa = $_SERVER['DOCUMENT_ROOT'] . $url_archivo;

if (!file_exists($ruta_completa)) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Archivo No Encontrado</title>
        <link rel="stylesheet" href="/repositorio_MIIDT/repositorio-miidt/node_modules/bootstrap/dist/css/bootstrap.min.css">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="alert alert-warning text-center">
                <h3><i class="fas fa-file-excel"></i> Archivo No Encontrado</h3>
                <p>El archivo solicitado no existe.</p>
                <a href="javascript:history.back()" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Verificar extensión
$extension = strtolower(pathinfo($ruta_completa, PATHINFO_EXTENSION));
if ($extension !== 'pdf') {
    http_response_code(400);
    die("Error: Solo se permiten archivos PDF");
}

// Descargar archivo local
if (ob_get_level()) ob_end_clean();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($ruta_completa) . '"');
header('Content-Length: ' . filesize($ruta_completa));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

readfile($ruta_completa);
exit;
?>