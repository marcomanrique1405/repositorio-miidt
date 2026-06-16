<?php
declare(strict_types=1);

/* ==========================================
   CONFIGURACIÓN BASE DEL ADMIN
========================================== */

define('ADMIN_BASE', '/repositorio_MIIDT/admin');
define('ADMIN_ROOT', __DIR__);

/* ==========================================
   HEADERS DE SEGURIDAD
   No rompen tu funcionamiento actual
========================================== */

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

/*
    Evita que páginas privadas del admin queden guardadas en caché.
    Ayuda cuando alguien cierra sesión y luego presiona "atrás".
*/
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

/* ==========================================
   SESIONES MÁS SEGURAS
========================================== */

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');

$esHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (($_SERVER['SERVER_PORT'] ?? '') === '443')
);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => ADMIN_BASE,
    'domain' => '',
    'secure' => $esHttps,
    'httponly' => true,
    'samesite' => 'Strict'
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* ==========================================
   CONTROLADOR PRINCIPAL
========================================== */

require ADMIN_ROOT . '/controllers/AuthController.php';

/* ==========================================
   FUNCIONES AUXILIARES
========================================== */

function debugConsole(string $label, $value = null): void {
    $output = $value !== null ? $label . ': ' . json_encode($value) : $label;
}

/*
    CSRF listo para usar después en formularios POST.
    No rompe nada porque aquí solo generamos/verificamos cuando tú lo llames
    desde controladores o vistas.
*/
function generarCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string)$_SESSION['csrf_token'];
}

function verificarCsrfToken(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    return hash_equals((string)$_SESSION['csrf_token'], (string)$token);
}

function responderMetodoNoPermitido(string $allow): void
{
    http_response_code(405);
    header('Allow: ' . $allow);
    echo '405 - Method Not Allowed';
}

/* ==========================================
   NORMALIZAR RUTA
========================================== */

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

if (strncmp($uriPath, ADMIN_BASE, strlen(ADMIN_BASE)) === 0) {
    $uriPath = substr($uriPath, strlen(ADMIN_BASE));
}

$path = '/' . ltrim($uriPath, '/');
$path = rtrim($path, '/');
$path = ($path === '' ? '/' : $path);

if (strpos($path, '/index.php') === 0) {
    $path = substr($path, strlen('/index.php'));
    $path = ($path === '' ? '/' : $path);
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$controller = new AuthController();

/* ==========================================
   ROUTER ADMIN
========================================== */

switch ($path) {

    case '/':
        $controller->login();
        break;

    case '/login':
        if ($method === 'POST') {
            $controller->doLogin();
        } else {
            $controller->login();
        }
        break;

    case '/dashboard':
        $controller->dashboard();
        break;

    case '/logout':
        $controller->logout();
        break;

    case '/stats':
        $controller = new AuthController();
        $controller->stats();
        break;

    case '/tesis/buscar':
        if ($method === 'GET') {
            require_once ADMIN_ROOT . '/controllers/TesisController.php';
            $controller = new TesisController();
            $controller->buscar();
            exit;
        }

        responderMetodoNoPermitido('GET');
        break;

    case '/tesis/obtener':
        if ($method === 'GET') {
            require_once ADMIN_ROOT . '/controllers/TesisController.php';
            $controller = new TesisController();
            $controller->obtener();
            exit;
        }

        responderMetodoNoPermitido('GET');
        break;

    case '/tesis/actualizar':
        if ($method === 'POST') {
            require_once ADMIN_ROOT . '/controllers/TesisController.php';
            $controller = new TesisController();
            $controller->actualizar();
            exit;
        }

        responderMetodoNoPermitido('POST');
        break;

    case '/directores/buscar':
        if ($method === 'GET') {
            require_once ADMIN_ROOT . '/controllers/TesisController.php';
            $controller = new TesisController();
            $controller->buscarDirectores();
            exit;
        }

        responderMetodoNoPermitido('GET');
        break;

    case '/tesis/guardar':
        if ($method === 'POST') {
            require_once ADMIN_ROOT . '/controllers/TesisController.php';
            $controller = new TesisController();
            $controller->guardar();
            exit;
        }

        responderMetodoNoPermitido('POST');
        break;

    case '/tesis/eliminar':
        if ($method === 'POST') {
            require_once ADMIN_ROOT . '/controllers/TesisController.php';
            $controller = new TesisController();
            $controller->eliminar();
            exit;
        }

        responderMetodoNoPermitido('POST');
        break;

    case '/filtrar-tesis':
        if ($method === 'POST') {
            $controller->filtrarTesis();
            exit;
        }

        responderMetodoNoPermitido('POST');
        break;

    default:
        http_response_code(404);
        echo '404 - Not Found';
        break;
}