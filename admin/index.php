<?php
declare(strict_types=1);

session_start();

$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base = ($base === '/' ? '' : $base);

define('ADMIN_BASE', $base);
define('ADMIN_ROOT', __DIR__);

require ADMIN_ROOT . '/controllers/AuthController.php';

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

if (ADMIN_BASE !== '' && strncmp($uriPath, ADMIN_BASE, strlen(ADMIN_BASE)) === 0) {
    $uriPath = substr($uriPath, strlen(ADMIN_BASE));
}

$path = '/' . ltrim((string)$uriPath, '/');
$path = rtrim($path, '/');
$path = ($path === '' ? '/' : $path);

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

    default:
        http_response_code(404);
        echo '404 - Not Found';
        break;
}