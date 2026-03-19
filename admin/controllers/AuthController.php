<?php
declare(strict_types=1);

final class AuthController
{
    public function login(): void
    {
        if (!empty($_SESSION['admin_auth'])) {
            header('Location: ' . ADMIN_BASE . '/index.php/dashboard'); // 🔥 FIX
            exit;
        }

        $error = '';
        $usernameValue = '';

        require ADMIN_ROOT . '/views/auth/login.php';
    }

    public function doLogin(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . ADMIN_BASE . '/index.php/login'); // 🔥 FIX
            exit;
        }

        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Todos los campos son obligatorios.';
            $usernameValue = $username;
            require ADMIN_ROOT . '/views/auth/login.php';
            return;
        }

        require_once ADMIN_ROOT . '/models/Usuario.php';
        $usuarioModel = new Usuario();

        $usuario = $usuarioModel->findByUsername($username);

        if (!$usuario) {
            $error = 'Usuario no encontrado.';
            $usernameValue = $username;
            require ADMIN_ROOT . '/views/auth/login.php';
            return;
        }

        if ($usuario['estado'] !== 'activo') {
            $error = 'La cuenta está inactiva.';
            $usernameValue = $username;
            require ADMIN_ROOT . '/views/auth/login.php';
            return;
        }

        if (!password_verify($password, $usuario['password_hash'])) {
            $error = 'Contraseña incorrecta.';
            $usernameValue = $username;
            require ADMIN_ROOT . '/views/auth/login.php';
            return;
        }

        session_regenerate_id(true);

        $_SESSION['admin_auth'] = true;
        $_SESSION['admin_id'] = $usuario['id_usuario'];
        $_SESSION['admin_username'] = $usuario['username'];
        $_SESSION['admin_nombre'] = $usuario['nombre_completo'];

        $usuarioModel->updateUltimoLogin((int)$usuario['id_usuario']);

        header('Location: ' . ADMIN_BASE . '/index.php/dashboard'); // 🔥 FIX
        exit;
    }

    public function dashboard(): void
    {
        if (empty($_SESSION['admin_auth'])) {
            header('Location: ' . ADMIN_BASE . '/index.php/login'); // 🔥 FIX
            exit;
        }

        require ADMIN_ROOT . '/views/auth/dashboard.php';
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();

        header('Location: ' . ADMIN_BASE . '/index.php/login'); // 🔥 FIX
        exit;
    }
}