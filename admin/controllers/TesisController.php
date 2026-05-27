<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Tesis.php';

final class TesisController
{
    private const SESSION_IDLE_SECONDS = 1800; // 30 minutos de inactividad

    public function buscar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->verificarSesionActiva(true);

        $query = trim((string)($_GET['q'] ?? ''));

        if (mb_strlen($query) > 150) {
            $query = mb_substr($query, 0, 150);
        }

        $model = new Tesis();
        $tesis = $model->buscar($query);

        echo json_encode($tesis, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function buscarDirectores(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->verificarSesionActiva(true);

        $query = trim((string)($_GET['q'] ?? ''));

        if (mb_strlen($query) > 150) {
            $query = mb_substr($query, 0, 150);
        }

        $model = new Tesis();
        $directores = $model->buscarDirectores($query);

        echo json_encode($directores, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function obtener(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->verificarSesionActiva(true);

        try {
            $idTesis = $this->obtenerEnteroGet('id');

            if ($idTesis <= 0) {
                throw new RuntimeException('No se recibió una tesis válida.');
            }

            $model = new Tesis();
            $tesis = $model->obtenerPorId($idTesis);

            if (!$tesis) {
                throw new RuntimeException('No se encontró la tesis solicitada.');
            }

            echo json_encode([
                'ok' => true,
                'tesis' => $tesis
            ], JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Throwable $e) {
            $this->responderExcepcion($e, 422);
        }
    }

    public function guardar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->verificarSesionActiva(true);

        if (!$this->csrfValidoDesdeRequest()) {
            $this->responderJson(false, 'Solicitud no válida. Recarga la página e intenta nuevamente.', 403);
        }

        try {
            $titulo = $this->obtenerTextoPost('titulo', 250);
            $url = $this->obtenerTextoPost('url', 600);
            $fecha = $this->obtenerTextoPost('fecha_registro', 20);
            $estado = $this->obtenerTextoPost('estado', 30);

            $idDirector = $this->obtenerEnteroPost('id_director');
            $idLinea = $this->obtenerEnteroPost('id_linea');

            $matricula = $this->obtenerTextoPost('autor_matricula', 50);
            $nombre = $this->obtenerTextoPost('autor_nombre', 100);
            $apellidoPaterno = $this->obtenerTextoPost('autor_apellido_paterno', 100);
            $apellidoMaterno = $this->obtenerTextoPost('autor_apellido_materno', 100);
            $correo = $this->obtenerTextoPost('autor_correo', 150);
            $sexo = $this->obtenerTextoPost('autor_sexo', 1);

            if ($titulo === '') {
                throw new RuntimeException('El título de tesis es obligatorio.');
            }

            if ($matricula === '' || $nombre === '' || $apellidoPaterno === '' || $sexo === '') {
                throw new RuntimeException('Completa los datos del autor.');
            }

            if (!in_array($sexo, ['M', 'F'], true)) {
                throw new RuntimeException('Selecciona el sexo del autor.');
            }

            if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('El correo institucional no tiene un formato válido.');
            }

            if ($idLinea <= 0) {
                throw new RuntimeException('Selecciona una línea de investigación.');
            }

            if ($idDirector <= 0) {
                throw new RuntimeException('Selecciona un director de la lista.');
            }

            if ($fecha === '') {
                throw new RuntimeException('Selecciona la fecha de la tesis.');
            }

            if (!in_array($estado, ['Digital', 'Fisico', 'Digital y Fisico'], true)) {
                throw new RuntimeException('Selecciona el estado de la tesis.');
            }

            /*
                REGLA DEL LINK:
                - Fisico: NO requiere link.
                - Digital: SÍ requiere link.
                - Digital y Fisico: SÍ requiere link.
            */
            $requiereLinkDigital = in_array($estado, ['Digital', 'Digital y Fisico'], true);

            if ($requiereLinkDigital && $url === '') {
                throw new RuntimeException('Agrega el link del archivo digital de la tesis.');
            }

            if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
                throw new RuntimeException('El link del archivo digital de la tesis no tiene un formato válido.');
            }

            $model = new Tesis();

            $fechaMysql = $this->convertirFechaMysql($fecha);
            $lineaInfo = $this->obtenerLineaInfo($idLinea);
            $slugAutor = $this->crearSlugAutor($nombre, $apellidoPaterno, $apellidoMaterno);

            $portadaDb = null;

            if (
                isset($_FILES['pasta_fisica']) &&
                ($_FILES['pasta_fisica']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
            ) {
                $portadaDb = $this->guardarImagenConvertida(
                    $_FILES['pasta_fisica'],
                    dirname(ADMIN_ROOT) . '/uploads/portadas/' . $lineaInfo['carpeta_portadas'],
                    '/uploads/portadas/' . $lineaInfo['carpeta_portadas'] . '/' . $slugAutor . '.webp',
                    $slugAutor . '.webp',
                    'webp'
                );
            }

            if (
                isset($_FILES['portada_institucional']) &&
                ($_FILES['portada_institucional']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
            ) {
                $this->guardarImagenConvertida(
                    $_FILES['portada_institucional'],
                    dirname(ADMIN_ROOT) . '/assets/img/popups/' . $lineaInfo['carpeta_popup'],
                    '/assets/img/popups/' . $lineaInfo['carpeta_popup'] . '/' . $slugAutor . '.png',
                    $slugAutor . '.png',
                    'png'
                );
            }

            $idTesis = $model->guardarAlta([
                'autor' => [
                    'matricula' => $matricula,
                    'id_linea' => $idLinea,
                    'nombre' => $nombre,
                    'apellido_paterno' => $apellidoPaterno,
                    'apellido_materno' => $apellidoMaterno !== '' ? $apellidoMaterno : null,
                    'correo_institucional' => $correo !== '' ? $correo : null,
                    'sexo' => $sexo
                ],
                'tesis' => [
                    'matricula' => $matricula,
                    'id_director' => $idDirector,
                    'titulo' => $titulo,
                    'url' => $url !== '' ? $url : null,
                    'portada' => $portadaDb,
                    'fecha_registro' => $fechaMysql,
                    'estado' => $estado
                ]
            ]);

            echo json_encode([
                'ok' => true,
                'message' => 'Tesis agregada correctamente.',
                'id_tesis' => $idTesis
            ], JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Throwable $e) {
            $this->responderExcepcion($e, 422);
        }
    }

    public function actualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->verificarSesionActiva(true);

        if (!$this->csrfValidoDesdeRequest()) {
            $this->responderJson(false, 'Solicitud no válida. Recarga la página e intenta nuevamente.', 403);
        }

        try {
            $idTesis = $this->obtenerEnteroPost('id_tesis');

            $titulo = $this->obtenerTextoPost('titulo', 250);
            $url = $this->obtenerTextoPost('url', 600);
            $fecha = $this->obtenerTextoPost('fecha_registro', 20);
            $estado = $this->obtenerTextoPost('estado', 30);

            $idDirector = $this->obtenerEnteroPost('id_director');
            $idLinea = $this->obtenerEnteroPost('id_linea');

            $matricula = $this->obtenerTextoPost('autor_matricula', 50);
            $nombre = $this->obtenerTextoPost('autor_nombre', 100);
            $apellidoPaterno = $this->obtenerTextoPost('autor_apellido_paterno', 100);
            $apellidoMaterno = $this->obtenerTextoPost('autor_apellido_materno', 100);
            $correo = $this->obtenerTextoPost('autor_correo', 150);
            $sexo = $this->obtenerTextoPost('autor_sexo', 1);

            if ($idTesis <= 0) {
                throw new RuntimeException('No se recibió el identificador de la tesis.');
            }

            if ($titulo === '') {
                throw new RuntimeException('El título de tesis es obligatorio.');
            }

            if ($matricula === '' || $nombre === '' || $apellidoPaterno === '' || $sexo === '') {
                throw new RuntimeException('Completa los datos del autor.');
            }

            if (!in_array($sexo, ['M', 'F'], true)) {
                throw new RuntimeException('Selecciona el sexo del autor.');
            }

            if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('El correo institucional no tiene un formato válido.');
            }

            if ($idLinea <= 0) {
                throw new RuntimeException('Selecciona una línea de investigación.');
            }

            if ($idDirector <= 0) {
                throw new RuntimeException('Selecciona un director de la lista.');
            }

            if ($fecha === '') {
                throw new RuntimeException('Selecciona la fecha de la tesis.');
            }

            if (!in_array($estado, ['Digital', 'Fisico', 'Digital y Fisico'], true)) {
                throw new RuntimeException('Selecciona el estado de la tesis.');
            }

            /*
                REGLA DEL LINK:
                - Fisico: NO requiere link.
                - Digital: SÍ requiere link.
                - Digital y Fisico: SÍ requiere link.
            */
            $requiereLinkDigital = in_array($estado, ['Digital', 'Digital y Fisico'], true);

            if ($requiereLinkDigital && $url === '') {
                throw new RuntimeException('Agrega el link del archivo digital de la tesis.');
            }

            if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
                throw new RuntimeException('El link del archivo digital de la tesis no tiene un formato válido.');
            }

            $model = new Tesis();

            $tesisActual = $model->obtenerPorId($idTesis);

            if (!$tesisActual) {
                throw new RuntimeException('No se encontró la tesis que deseas actualizar.');
            }

            $fechaMysql = $this->convertirFechaMysql($fecha);
            $lineaInfo = $this->obtenerLineaInfo($idLinea);
            $slugAutor = $this->crearSlugAutor($nombre, $apellidoPaterno, $apellidoMaterno);

            /*
                NUEVO SIN ROMPER:
                Calculamos el nombre anterior de las imágenes para poder copiar/renombrar
                cuando cambia el nombre del autor o cambia la línea.
            */
            $idLineaAnterior = (int)($tesisActual['id_linea'] ?? $idLinea);
            $lineaInfoAnterior = $this->obtenerLineaInfo($idLineaAnterior);

            $slugAutorAnterior = $this->crearSlugAutor(
                (string)($tesisActual['autor_nombre'] ?? $nombre),
                (string)($tesisActual['autor_apellido_paterno'] ?? $apellidoPaterno),
                (string)($tesisActual['autor_apellido_materno'] ?? $apellidoMaterno)
            );

            $rootProyecto = dirname(ADMIN_ROOT);

            /*
                Pasta física:
                - Si subes nueva pasta física, se guarda la nueva.
                - Si no subes y cambió el nombre/linea, copia la imagen anterior al nuevo nombre.
                - Si no encuentra la anterior, conserva lo que ya estaba en BD.
            */
            $portadaDb = null;

            if (
                isset($_FILES['pasta_fisica']) &&
                ($_FILES['pasta_fisica']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
            ) {
                $portadaDb = $this->guardarImagenConvertida(
                    $_FILES['pasta_fisica'],
                    $rootProyecto . '/uploads/portadas/' . $lineaInfo['carpeta_portadas'],
                    '/uploads/portadas/' . $lineaInfo['carpeta_portadas'] . '/' . $slugAutor . '.webp',
                    $slugAutor . '.webp',
                    'webp'
                );
            } else {
                $portadaActual = (string)($tesisActual['portada'] ?? '');
                $portadaNueva = '/uploads/portadas/' . $lineaInfo['carpeta_portadas'] . '/' . $slugAutor . '.webp';

                if ($portadaActual !== '' && $portadaActual !== $portadaNueva) {
                    $rutaPastaAnterior = $this->rutaFisicaDesdePublica($portadaActual);
                    $rutaPastaNueva = $this->rutaFisicaDesdePublica($portadaNueva);

                    if ($this->copiarArchivoSiExiste($rutaPastaAnterior, $rutaPastaNueva)) {
                        $portadaDb = $portadaNueva;
                    }
                }
            }

            /*
                Portada institucional:
                - Si subes nueva, se guarda con el nuevo nombre.
                - Si no subes, copia la portada institucional anterior al nuevo nombre.
                Esto arregla el problema de que después no aparezca en el popup.
            */
            if (
                isset($_FILES['portada_institucional']) &&
                ($_FILES['portada_institucional']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE
            ) {
                $this->guardarImagenConvertida(
                    $_FILES['portada_institucional'],
                    $rootProyecto . '/assets/img/popups/' . $lineaInfo['carpeta_popup'],
                    '/assets/img/popups/' . $lineaInfo['carpeta_popup'] . '/' . $slugAutor . '.png',
                    $slugAutor . '.png',
                    'png'
                );
            } else {
                $rutaInstitucionalAnterior = $rootProyecto
                    . '/assets/img/popups/'
                    . $lineaInfoAnterior['carpeta_popup']
                    . '/'
                    . $slugAutorAnterior
                    . '.png';

                $rutaInstitucionalNueva = $rootProyecto
                    . '/assets/img/popups/'
                    . $lineaInfo['carpeta_popup']
                    . '/'
                    . $slugAutor
                    . '.png';

                $this->copiarArchivoSiExiste($rutaInstitucionalAnterior, $rutaInstitucionalNueva);
            }

            $model->actualizarTesis([
                'autor' => [
                    'matricula' => $matricula,
                    'id_linea' => $idLinea,
                    'nombre' => $nombre,
                    'apellido_paterno' => $apellidoPaterno,
                    'apellido_materno' => $apellidoMaterno !== '' ? $apellidoMaterno : null,
                    'correo_institucional' => $correo !== '' ? $correo : null,
                    'sexo' => $sexo
                ],
                'tesis' => [
                    'id_tesis' => $idTesis,
                    'matricula' => $matricula,
                    'id_director' => $idDirector,
                    'titulo' => $titulo,
                    'url' => $url !== '' ? $url : null,
                    'portada' => $portadaDb,
                    'fecha_registro' => $fechaMysql,
                    'estado' => $estado
                ]
            ]);

            echo json_encode([
                'ok' => true,
                'message' => 'Tesis actualizada correctamente.',
                'id_tesis' => $idTesis
            ], JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Throwable $e) {
            $this->responderExcepcion($e, 422);
        }
    }

    public function eliminar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $this->verificarSesionActiva(true);

        if (!$this->csrfValidoDesdeRequest()) {
            $this->responderJson(false, 'Solicitud no válida. Recarga la página e intenta nuevamente.', 403);
        }

        try {
            $payload = $this->obtenerJsonPayload();

            $idTesis = (int)(
                $_POST['id_tesis']
                ?? $_POST['id']
                ?? $payload['id_tesis']
                ?? $payload['id']
                ?? 0
            );

            if ($idTesis <= 0) {
                throw new RuntimeException('No se recibió el identificador de la tesis.');
            }

            $model = new Tesis();

            $tesisActual = $model->obtenerPorId($idTesis);

            if (!$tesisActual) {
                throw new RuntimeException('No se encontró la tesis que deseas eliminar.');
            }

            $model->eliminar($idTesis);

            echo json_encode([
                'ok' => true,
                'message' => 'Tesis eliminada correctamente.',
                'id_tesis' => $idTesis
            ], JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Throwable $e) {
            $this->responderExcepcion($e, 422);
        }
    }

    private function convertirFechaMysql(string $fecha): string
    {
        $dt = DateTime::createFromFormat('d/m/Y', $fecha);

        if (!$dt) {
            throw new RuntimeException('La fecha no tiene un formato válido.');
        }

        return $dt->format('Y-m-d 00:00:00');
    }

    private function obtenerLineaInfo(int $idLinea): array
    {
        switch ($idLinea) {
            case 1:
                return [
                    'nombre' => 'CSR',
                    'carpeta_portadas' => 'Linea_CSR',
                    'carpeta_popup' => 'linea_CSR'
                ];

            case 2:
                return [
                    'nombre' => 'TICs',
                    'carpeta_portadas' => 'Linea_TICs',
                    'carpeta_popup' => 'linea_TICs'
                ];

            case 3:
                return [
                    'nombre' => 'Geomatica',
                    'carpeta_portadas' => 'Linea_Geomatica',
                    'carpeta_popup' => 'linea_Geomatica'
                ];

            default:
                throw new RuntimeException('Línea de investigación no válida.');
        }
    }

    private function crearSlugAutor(string $nombre, string $apellidoPaterno, string $apellidoMaterno): string
    {
        $texto = trim($nombre . ' ' . $apellidoPaterno . ' ' . $apellidoMaterno);

        $texto = mb_strtolower($texto, 'UTF-8');

        $reemplazos = [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'à' => 'a',
            'è' => 'e',
            'ì' => 'i',
            'ò' => 'o',
            'ù' => 'u',
            'ä' => 'a',
            'ë' => 'e',
            'ï' => 'i',
            'ö' => 'o',
            'ü' => 'u',
            'ñ' => 'n'
        ];

        $texto = strtr($texto, $reemplazos);
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
        $texto = trim((string)$texto, '-');

        if ($texto === '') {
            throw new RuntimeException('No se pudo generar el nombre de la imagen.');
        }

        return $texto;
    }

    private function guardarImagenConvertida(
        array $file,
        string $carpetaFisica,
        string $rutaPublica,
        string $nombreArchivo,
        string $formatoFinal
    ): string {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Error al subir la imagen.');
        }

        if (($file['size'] ?? 0) > 8 * 1024 * 1024) {
            throw new RuntimeException('La imagen no debe pesar más de 8 MB.');
        }

        $tmp = (string)($file['tmp_name'] ?? '');

        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new RuntimeException('No se recibió una imagen válida.');
        }

        $info = @getimagesize($tmp);

        if (!$info || empty($info['mime'])) {
            throw new RuntimeException('El archivo subido no es una imagen válida.');
        }

        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $imagen = imagecreatefromjpeg($tmp);
                break;

            case 'image/png':
                $imagen = imagecreatefrompng($tmp);
                break;

            case 'image/webp':
                $imagen = imagecreatefromwebp($tmp);
                break;

            case 'image/gif':
                $imagen = imagecreatefromgif($tmp);
                break;

            default:
                throw new RuntimeException('Formato de imagen no permitido.');
        }

        if (!$imagen) {
            throw new RuntimeException('No se pudo procesar la imagen.');
        }

        if (!is_dir($carpetaFisica)) {
            if (!mkdir($carpetaFisica, 0775, true) && !is_dir($carpetaFisica)) {
                imagedestroy($imagen);
                throw new RuntimeException('No se pudo crear la carpeta para guardar la imagen.');
            }
        }

        $rutaFisica = rtrim($carpetaFisica, '/\\') . DIRECTORY_SEPARATOR . $nombreArchivo;

        if ($formatoFinal === 'webp') {
            imagepalettetotruecolor($imagen);

            if (!imagewebp($imagen, $rutaFisica, 85)) {
                imagedestroy($imagen);
                throw new RuntimeException('No se pudo guardar la imagen WebP.');
            }
        } elseif ($formatoFinal === 'png') {
            imagepalettetotruecolor($imagen);
            imagealphablending($imagen, false);
            imagesavealpha($imagen, true);

            if (!imagepng($imagen, $rutaFisica, 6)) {
                imagedestroy($imagen);
                throw new RuntimeException('No se pudo guardar la imagen PNG.');
            }
        } else {
            imagedestroy($imagen);
            throw new RuntimeException('Formato final no permitido.');
        }

        imagedestroy($imagen);

        return $rutaPublica;
    }

    private function rutaFisicaDesdePublica(string $rutaPublica): string
    {
        $rutaPublica = trim($rutaPublica);

        if ($rutaPublica === '') {
            return '';
        }

        if (str_starts_with($rutaPublica, '/repositorio_MIIDT/')) {
            $rutaPublica = substr($rutaPublica, strlen('/repositorio_MIIDT'));
        }

        if (!str_starts_with($rutaPublica, '/')) {
            $rutaPublica = '/' . $rutaPublica;
        }

        return dirname(ADMIN_ROOT) . $rutaPublica;
    }

    private function copiarArchivoSiExiste(string $rutaAnterior, string $rutaNueva): bool
    {
        if ($rutaAnterior === '' || $rutaNueva === '') {
            return false;
        }

        if ($rutaAnterior === $rutaNueva) {
            return true;
        }

        if (!file_exists($rutaAnterior)) {
            return false;
        }

        $carpetaNueva = dirname($rutaNueva);

        if (!is_dir($carpetaNueva)) {
            if (!mkdir($carpetaNueva, 0775, true) && !is_dir($carpetaNueva)) {
                return false;
            }
        }

        if (file_exists($rutaNueva)) {
            return true;
        }

        return copy($rutaAnterior, $rutaNueva);
    }

    private function verificarSesionActiva(bool $jsonResponse = true): void
    {
        if (empty($_SESSION['admin_auth'])) {
            $this->responderSesionNoValida($jsonResponse, false);
        }

        if ($this->sesionExpiradaPorInactividad()) {
            $this->destruirSesion();
            $this->responderSesionNoValida($jsonResponse, true);
        }

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

    private function estaAutenticado(): bool
    {
        return !empty($_SESSION['admin_auth']);
    }

    private function obtenerTextoPost(string $key, int $maxLength): string
    {
        $valor = trim((string)($_POST[$key] ?? ''));

        if ($maxLength > 0 && mb_strlen($valor) > $maxLength) {
            $valor = mb_substr($valor, 0, $maxLength);
        }

        return $valor;
    }

    private function obtenerEnteroPost(string $key): int
    {
        return (int)($_POST[$key] ?? 0);
    }

    private function obtenerEnteroGet(string $key): int
    {
        return (int)($_GET[$key] ?? 0);
    }

    private function obtenerJsonPayload(): array
    {
        $contentType = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));

        if (strpos($contentType, 'application/json') === false) {
            return [];
        }

        $json = file_get_contents('php://input');
        $decoded = json_decode((string)$json, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function csrfValidoDesdeRequest(): bool
    {
        $payload = $this->obtenerJsonPayload();

        $token = (string)(
            $_POST['csrf_token']
            ?? $payload['csrf_token']
            ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')
        );

        if ($token === '') {
            return false;
        }

        if (function_exists('verificarCsrfToken')) {
            return verificarCsrfToken($token);
        }

        if (empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals((string)$_SESSION['csrf_token'], $token);
    }

    private function responderJson(bool $ok, string $message, int $statusCode = 200): void
    {
        http_response_code($statusCode);

        echo json_encode([
            'ok' => $ok,
            'message' => $message
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    private function responderExcepcion(Throwable $e, int $statusCode = 422): void
    {
        error_log('TesisController error: ' . $e->getMessage());

        http_response_code($statusCode);

        echo json_encode([
            'ok' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }
}