<?php
declare(strict_types=1);

final class AuthController
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_LOCK_SECONDS = 900; // 15 minutos
    private const SESSION_IDLE_SECONDS = 1800; // 30 minutos de inactividad

    public function login(): void
    {
        /*
            Si ya tiene sesión, primero verificamos si no expiró por inactividad.
            Si sigue activa, lo mandamos al dashboard.
        */
        if (!empty($_SESSION['admin_auth'])) {
            if ($this->sesionExpiradaPorInactividad()) {
                $this->destruirSesion();
            } else {
                $_SESSION['admin_last_activity'] = time();
                header('Location: ' . ADMIN_BASE . '/index.php/dashboard');
                exit;
            }
        }

        $error = '';
        $usernameValue = '';

        if (isset($_GET['expired']) && $_GET['expired'] === '1') {
            $error = 'Tu sesión expiró por inactividad. Inicia sesión nuevamente.';
        }

        /*
            Token CSRF para proteger el formulario de login.
            En login.php debe existir:
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        */
        $csrfToken = $this->generarCsrfToken();

        require ADMIN_ROOT . '/views/auth/login.php';
    }

    public function doLogin(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . ADMIN_BASE . '/index.php/login');
            exit;
        }

        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $csrfToken = (string)($_POST['csrf_token'] ?? '');

        /*
            Token para volver a pintar el formulario si hay error.
        */
        $csrfTokenVista = $this->generarCsrfToken();

        if (!$this->verificarCsrfToken($csrfToken)) {
            $error = 'Solicitud no válida. Intenta nuevamente.';
            $usernameValue = $username;
            $csrfToken = $csrfTokenVista;
            require ADMIN_ROOT . '/views/auth/login.php';
            return;
        }

        if ($this->loginBloqueado()) {
            $error = 'Demasiados intentos fallidos. Intenta nuevamente en unos minutos.';
            $usernameValue = $username;
            $csrfToken = $csrfTokenVista;
            require ADMIN_ROOT . '/views/auth/login.php';
            return;
        }

        if ($username === '' || $password === '') {
            $this->registrarIntentoFallido();
            $error = 'Todos los campos son obligatorios.';
            $usernameValue = $username;
            $csrfToken = $csrfTokenVista;
            require ADMIN_ROOT . '/views/auth/login.php';
            return;
        }

        /*
            Evita payloads demasiado grandes en pruebas de seguridad.
        */
        if (mb_strlen($username) > 80 || mb_strlen($password) > 255) {
            $this->registrarIntentoFallido();
            $error = 'Credenciales incorrectas';
            $usernameValue = '';
            $csrfToken = $csrfTokenVista;
            require ADMIN_ROOT . '/views/auth/login.php';
            return;
        }

        require_once ADMIN_ROOT . '/models/Usuario.php';
        $usuarioModel = new Usuario();

        $usuario = $usuarioModel->findByUsername($username);

        /*
            Mensajes uniformes para no revelar si el usuario existe,
            si está inactivo o si la contraseña falló.
        */
        if (!$usuario) {
            $this->registrarIntentoFallido();
            $error = 'Credenciales incorrectas';
            $usernameValue = $username;
            $csrfToken = $csrfTokenVista;
            require ADMIN_ROOT . '/views/auth/login.php';
            return;
        }

        if (($usuario['estado'] ?? '') !== 'activo') {
            $this->registrarIntentoFallido();
            $error = 'Credenciales incorrectas';
            $usernameValue = $username;
            $csrfToken = $csrfTokenVista;
            require ADMIN_ROOT . '/views/auth/login.php';
            return;
        }

        if (!password_verify($password, (string)$usuario['password_hash'])) {
            $this->registrarIntentoFallido();
            $error = 'Credenciales incorrectas';
            $usernameValue = $username;
            $csrfToken = $csrfTokenVista;
            require ADMIN_ROOT . '/views/auth/login.php';
            return;
        }

        /*
            Login correcto: limpiar intentos fallidos.
        */
        $this->limpiarIntentosLogin();

        session_regenerate_id(true);

        $_SESSION['admin_auth'] = true;
        $_SESSION['admin_id'] = (int)$usuario['id_usuario'];
        $_SESSION['admin_username'] = (string)$usuario['username'];
        $_SESSION['admin_nombre'] = (string)$usuario['nombre_completo'];
        $_SESSION['admin_login_time'] = time();

        /*
            NUEVO:
            Última actividad para cerrar sesión por inactividad.
        */
        $_SESSION['admin_last_activity'] = time();

        /*
            Renovamos CSRF después del login.
        */
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $usuarioModel->updateUltimoLogin((int)$usuario['id_usuario']);

        header('Location: ' . ADMIN_BASE . '/index.php/dashboard');
        exit;
    }

    public function dashboard(): void
    {
        $this->verificarSesionActiva(false);

        require_once ADMIN_ROOT . '/models/Dashboard.php';

        $dashboard = new Dashboard();

        $totalTesis = $dashboard->totalTesis();
        $totalDirectores = $dashboard->totalDirectores();
        $totalFisico = $dashboard->totalFisico();
        $totalDigital = $dashboard->totalDigital();

        require ADMIN_ROOT . '/views/auth/dashboard.php';
    }

    public function stats(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->verificarSesionActiva(true);

        require_once ADMIN_ROOT . '/models/Dashboard.php';

        $dashboard = new Dashboard();

        $data = [
            'tesis' => $dashboard->totalTesis(),
            'directores' => $dashboard->totalDirectores(),
            'fisico' => $dashboard->totalFisico(),
            'digital' => $dashboard->totalDigital()
        ];

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function logout(): void
    {
        $this->destruirSesion();

        header('Location: ' . ADMIN_BASE . '/index.php/login');
        exit;
    }

    public function filtrarTesis(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->verificarSesionActiva(true);

        require_once ADMIN_ROOT . '/models/Tesis.php';

        $rawInput = file_get_contents("php://input");
        $input = json_decode((string)$rawInput, true);

        if (!is_array($input)) {
            $input = [];
        }

        $tesisModel = new Tesis();

        $resultados = $tesisModel->filtrar($input);

        echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
    }

    private function generarCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return (string)$_SESSION['csrf_token'];
    }

    private function verificarCsrfToken(string $token): bool
    {
        if (empty($_SESSION['csrf_token']) || $token === '') {
            return false;
        }

        return hash_equals((string)$_SESSION['csrf_token'], $token);
    }

    private function loginBloqueado(): bool
    {
        $bloqueadoHasta = (int)($_SESSION['login_bloqueado_hasta'] ?? 0);

        if ($bloqueadoHasta <= 0) {
            return false;
        }

        if (time() >= $bloqueadoHasta) {
            $this->limpiarIntentosLogin();
            return false;
        }

        return true;
    }

    private function registrarIntentoFallido(): void
    {
        $intentos = (int)($_SESSION['login_intentos_fallidos'] ?? 0);
        $intentos++;

        $_SESSION['login_intentos_fallidos'] = $intentos;

        if ($intentos >= self::MAX_LOGIN_ATTEMPTS) {
            $_SESSION['login_bloqueado_hasta'] = time() + self::LOGIN_LOCK_SECONDS;
        }
    }

    private function limpiarIntentosLogin(): void
    {
        unset($_SESSION['login_intentos_fallidos']);
        unset($_SESSION['login_bloqueado_hasta']);
    }

    /*
        Verifica sesión en rutas protegidas.
        - Si es página normal: redirige al login.
        - Si es AJAX/JSON: responde JSON con redirect.
    */
    private function verificarSesionActiva(bool $jsonResponse = false): void
    {
        if (empty($_SESSION['admin_auth'])) {
            $this->responderSesionNoValida($jsonResponse, false);
        }

        if ($this->sesionExpiradaPorInactividad()) {
            $this->destruirSesion();
            $this->responderSesionNoValida($jsonResponse, true);
        }

        /*
            Si la sesión sigue activa, actualizamos última actividad.
        */
        $_SESSION['admin_last_activity'] = time();
    }

    private function sesionExpiradaPorInactividad(): bool
    {
        if (empty($_SESSION['admin_auth'])) {
            return false;
        }

        $ultimaActividad = (int)($_SESSION['admin_last_activity'] ?? 0);

        if ($ultimaActividad <= 0) {
            $_SESSION['admin_last_activity'] = time();
            return false;
        }

        return (time() - $ultimaActividad) > self::SESSION_IDLE_SECONDS;
    }

    private function responderSesionNoValida(bool $jsonResponse, bool $expired): void
    {
        if ($jsonResponse) {
            http_response_code($expired ? 440 : 403);

            echo json_encode([
                'ok' => false,
                'error' => $expired ? 'Sesión expirada por inactividad' : 'No autorizado',
                'message' => $expired
                    ? 'Tu sesión expiró por inactividad. Inicia sesión nuevamente.'
                    : 'No autorizado',
                'redirect' => ADMIN_BASE . '/index.php/login' . ($expired ? '?expired=1' : '')
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        header('Location: ' . ADMIN_BASE . '/index.php/login' . ($expired ? '?expired=1' : ''));
        exit;
    }

    private function destruirSesion(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'] ?? ADMIN_BASE,
                'domain' => $params['domain'] ?? '',
                'secure' => (bool)($params['secure'] ?? false),
                'httponly' => (bool)($params['httponly'] ?? true),
                'samesite' => $params['samesite'] ?? 'Strict'
            ]);
        }

        session_destroy();
    }
}