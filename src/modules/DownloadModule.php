<?php

class DownloadModule {
    private $ips_permitidas = [
        '10.0.0.0/8',        // Red interna institucional
        '201.148.25.0/24',  // IP pública institucional
    ];
    public function checkAccess($ip_usuario) {
        foreach ($this->ips_permitidas as $rango) {
            if ($this->ip_en_rango($ip_usuario, $rango)) {
                return true;
            }
        }
        return false;
    }

    private function ip_en_rango($ip, $rango) {
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

    public function processDownload($url_archivo) {
        if (empty($url_archivo)) {
            $this->renderError(400, "Error", "No se especificó URL", "fas fa-exclamation-triangle", "danger");
            return;
        }

        if (strpos($url_archivo, 'drive.google.com') !== false) {
            $this->handleDriveLink($url_archivo);
        } else {
            $this->serveLocalFile($url_archivo);
        }
    }

    private function handleDriveLink($url_archivo) {
        preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url_archivo, $matches);

        if (isset($matches[1])) {
            $file_id = $matches[1];
            $download_url = "https://drive.google.com/file/d/" . $file_id . "/view";
            header("Location: " . $download_url);
            exit;
        } else {
            $this->renderError(400, "URL Inválida", "No se pudo procesar el enlace de Google Drive.", "fas fa-exclamation-triangle", "danger");
        }
    }

    private function serveLocalFile($url_archivo) {
        $ruta_completa = $_SERVER['DOCUMENT_ROOT'] . $url_archivo;

        if (!file_exists($ruta_completa)) {
            $this->renderError(404, "Archivo No Encontrado", "El archivo solicitado no existe.", "fas fa-file-excel", "warning");
            return;
        }

        $extension = strtolower(pathinfo($ruta_completa, PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            $this->renderError(400, "Error", "Solo se permiten archivos PDF", "fas fa-file-pdf", "danger");
            return;
        }

        if (ob_get_level()) ob_end_clean();

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($ruta_completa) . '"');
        header('Content-Length: ' . filesize($ruta_completa));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');

        readfile($ruta_completa);
        exit;
    }

    public function renderAccessDenied($ip_usuario) {
        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Acceso Restringido</title>
            <link rel="stylesheet" href="/../repositorio_MIIDT/assets/vendor/bootstrap/css/bootstrap.min.css">
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
                            <a href="/index.php" class="btn btn-secondary mt-3">
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

    private function renderError($code, $title, $message, $icon, $type) {
        http_response_code($code);
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title><?php echo htmlspecialchars($title); ?></title>
            <link rel="stylesheet" href="/../repositorio_MIIDT/assets/vendor/bootstrap/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        </head>
        <body class="bg-light">
        <div class="container mt-5">
            <div class="alert alert-<?php echo $type; ?> text-center">
                <h3><i class="<?php echo $icon; ?>"></i> <?php echo htmlspecialchars($title); ?></h3>
                <p><?php echo htmlspecialchars($message); ?></p>
                <a href="javascript:history.back()" class="btn btn-secondary">Volver</a>
            </div>
        </div>
        </body>
        </html>
        <?php
        exit;
    }
}
?>
