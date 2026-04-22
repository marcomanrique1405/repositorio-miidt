<?php
declare(strict_types=1);

session_start();

define('ADMIN_BASE', '/repositorio_MIIDT/admin');
define('ADMIN_ROOT', __DIR__);

require ADMIN_ROOT . '/controllers/AuthController.php';

function debugConsole(string $label, $value = null): void {
    $output = $value !== null ? $label . ': ' . json_encode($value) : $label;
}

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

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$controller = new AuthController();

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
        http_response_code(405);
        echo '405 - Method Not Allowed';
        break;

    case '/filtrar-tesis':
        if ($method === 'POST') {
            $controller->filtrarTesis();
            exit;
        }
        http_response_code(405);
        echo '405 - Method Not Allowed';
        break;

    default:
        http_response_code(404);
        echo '404 - Not Found';
        break;
}