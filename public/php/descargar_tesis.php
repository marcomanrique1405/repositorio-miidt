<?php
// descargar_tesis.php

// 🔹 Configuración de IPs permitidas para "Red Ing Torre"
$ips_permitidas = [
    '10.15.64.0/18',       // Rango completo de la red (10.15.64.1 - 10.15.127.254)
    '10.15.66.0/24',       // Subred específica donde estás (10.15.66.1 - 10.15.66.254)
];

// También puedes usar solo el rango más amplio:
// $ips_permitidas = ['10.15.64.0/18'];

// 🔹 Función para verificar si la IP está en el rango permitido
function ip_en_rango($ip, $rango) {
    if (strpos($rango, '/') === false) {
        return $ip === $rango;
    }
    
    list($subnet, $mask) = explode('/', $rango);
    $ip_long = ip2long($ip);
    $subnet_long = ip2long($subnet);
    $mask_long = -1 << (32 - (int)$mask);
    $subnet_long &= $mask_long;
    
    return ($ip_long & $mask_long) == $subnet_long;
}

// 🔹 Obtener la IP real del cliente
function obtener_ip_real() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$ip_usuario = obtener_ip_real();
$acceso_permitido = false;

// 🔹 Verificar si la IP está en alguno de los rangos permitidos
foreach ($ips_permitidas as $rango) {
    if (ip_en_rango($ip_usuario, $rango)) {
        $acceso_permitido = true;
        break;
    }
}

// 🔹 Si no tiene acceso, mostrar error
if (!$acceso_permitido) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acceso Denegado - Red Ing Torre</title>
        <link rel="stylesheet" href="/repositorio_MIIDT/repositorio-miidt/node_modules/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-lg border-danger">
                        <div class="card-body text-center p-5">
                            <i class="fas fa-wifi text-danger" style="font-size: 4rem;"></i>
                            <h2 class="text-danger mt-3 mb-3">Acceso Restringido</h2>
                            <p class="lead mb-3">Esta tesis solo puede descargarse desde la <strong>Red Ing Torre</strong> de la institución.</p>
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i> Por favor, conéctate a la red WiFi <strong>"Red Ing Torre"</strong> para descargar este contenido.
                            </div>
                            <p class="text-muted small mt-4">
                                <i class="fas fa-laptop"></i> Tu dirección IP actual: 
                                <code><?php echo htmlspecialchars($ip_usuario); ?></code>
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

// 🔹 Si tiene acceso, procesar la descarga
if (isset($_GET['url']) && !empty($_GET['url'])) {
    $url_archivo = $_GET['url'];
    $ruta_completa = $_SERVER['DOCUMENT_ROOT'] . $url_archivo;
    
    // Verificar que el archivo existe y está dentro del directorio permitido
    $directorio_permitido = $_SERVER['DOCUMENT_ROOT'] . '/repositorio_MIIDT/repositorio-miidt/';
    
    if (strpos(realpath($ruta_completa), realpath($directorio_permitido)) !== 0) {
        http_response_code(403);
        echo '<div class="alert alert-danger">Acceso denegado al archivo.</div>';
        exit;
    }
    
    if (file_exists($ruta_completa)) {
        // Configurar headers para descarga
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($ruta_completa) . '"');
        header('Content-Length: ' . filesize($ruta_completa));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        
        // Limpiar buffer y enviar archivo
        ob_clean();
        flush();
        readfile($ruta_completa);
        exit;
    } else {
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
                    <p>El archivo PDF solicitado no existe o ha sido movido.</p>
                    <a href="javascript:history.back()" class="btn btn-secondary mt-2">Volver</a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} else {
    http_response_code(400);
    echo '<div class="alert alert-warning">URL de archivo no especificada.</div>';
    exit;
}
?>