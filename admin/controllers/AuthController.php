<?php
declare(strict_types=1);

final class AuthController
{
    public function login(): void
    {
        if (!empty($_SESSION['admin_auth'])) {
            header('Location: ' . ADMIN_BASE . '/dashboard');
            exit;
        }

        require ADMIN_ROOT . '/views/auth/login.php';
    }

    public function doLogin(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . ADMIN_BASE . '/login');
            exit;
        }

        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            header('Location: ' . ADMIN_BASE . '/login');
            exit;
        }

        // Aquí iría la validación real contra tu BD (modelo)
        // Por ahora queda placeholder seguro: no autentica a nadie.
        $isValid = false;

        if (!$isValid) {
            header('Location: ' . ADMIN_BASE . '/login');
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['admin_auth'] = true;

        header('Location: ' . ADMIN_BASE . '/dashboard');
        exit;
    }

    public function dashboard(): void
    {
        if (empty($_SESSION['admin_auth'])) {
            header('Location: ' . ADMIN_BASE . '/login');
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
                (bool)$params['secure'],
                (bool)$params['httponly']
            );
        }

        session_destroy();

        header('Location: ' . ADMIN_BASE . '/login');
        exit;
    }
}