<?php
declare(strict_types=1);

session_start();

/**
 * BASE FIJA
 */
define('ADMIN_BASE', '/repositorio_MIIDT/admin');
define('ADMIN_ROOT', __DIR__);

require ADMIN_ROOT . '/controllers/AuthController.php';

/**
 * 🔥 FUNCIÓN DEBUG (SIEMPRE FUNCIONA)
 */
function debugConsole(string $label, $value = null): void {
    $output = $value !== null ? $label . ': ' . json_encode($value) : $label;
    echo "<script>console.log(" . json_encode($output) . ");</script>";
}

/**
 * Obtener ruta
 */
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

/**
 * Quitar base
 */
if (strncmp($uriPath, ADMIN_BASE, strlen(ADMIN_BASE)) === 0) {
    $uriPath = substr($uriPath, strlen(ADMIN_BASE));
}

/**
 * Normalizar
 */
$path = '/' . ltrim($uriPath, '/');
$path = rtrim($path, '/');
$path = ($path === '' ? '/' : $path);

/**
 * 🔥 SOPORTE PARA index.php/login
 */
if (strpos($path, '/index.php') === 0) {
    $path = substr($path, strlen('/index.php'));
    $path = ($path === '' ? '/' : $path);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/**
 * 🔥 DEBUG GLOBAL (SIEMPRE SE EJECUTA)
 */
debugConsole('REQUEST_URI', $_SERVER['REQUEST_URI'] ?? '');
debugConsole('URI_PATH', $uriPath);
debugConsole('PATH_FINAL', $path);
debugConsole('METHOD', $method);

$controller = new AuthController();

/**
 * ROUTER
 */
switch ($path) {

    case '/':
        debugConsole('ROUTE', '/ → login()');
        $controller->login();
        break;

    case '/login':
        if ($method === 'POST') {
            debugConsole('ROUTE', '/login POST → doLogin()');
            $controller->doLogin();
        } else {
            debugConsole('ROUTE', '/login GET → login()');
            $controller->login();
        }
        break;

    case '/dashboard':
        debugConsole('ROUTE', '/dashboard');
        $controller->dashboard();
        break;

    case '/logout':
        debugConsole('ROUTE', '/logout');
        $controller->logout();
        break;

    default:
        debugConsole('ROUTE', '404 → ' . $path);
        http_response_code(404);
        echo '404 - Not Found';
        break;
}