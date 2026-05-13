<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Tesis.php';

final class TesisController
{
    public function buscar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $query = $_GET['q'] ?? '';

        $model = new Tesis();
        $tesis = $model->buscar($query);

        echo json_encode($tesis, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function buscarDirectores(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['admin_auth'])) {
            http_response_code(403);
            echo json_encode([
                'ok' => false,
                'message' => 'No autorizado'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $query = trim((string)($_GET['q'] ?? ''));

        $model = new Tesis();
        $directores = $model->buscarDirectores($query);

        echo json_encode($directores, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function guardar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['admin_auth'])) {
            http_response_code(403);
            echo json_encode([
                'ok' => false,
                'message' => 'No autorizado'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            $titulo = trim((string)($_POST['titulo'] ?? ''));
            $url = trim((string)($_POST['url'] ?? ''));
            $fecha = trim((string)($_POST['fecha_registro'] ?? ''));
            $estado = trim((string)($_POST['estado'] ?? ''));

            $idDirector = (int)($_POST['id_director'] ?? 0);
            $idLinea = (int)($_POST['id_linea'] ?? 0);

            $matricula = trim((string)($_POST['autor_matricula'] ?? ''));
            $nombre = trim((string)($_POST['autor_nombre'] ?? ''));
            $apellidoPaterno = trim((string)($_POST['autor_apellido_paterno'] ?? ''));
            $apellidoMaterno = trim((string)($_POST['autor_apellido_materno'] ?? ''));
            $correo = trim((string)($_POST['autor_correo'] ?? ''));
            $sexo = trim((string)($_POST['autor_sexo'] ?? ''));

            if ($titulo === '') {
                throw new RuntimeException('El título de tesis es obligatorio.');
            }

            if ($matricula === '' || $nombre === '' || $apellidoPaterno === '' || $sexo === '') {
                throw new RuntimeException('Completa los datos del autor.');
            }

            if (!in_array($sexo, ['M', 'F'], true)) {
                throw new RuntimeException('Selecciona el sexo del autor.');
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
                VALIDACIÓN IMPORTANTE:
                El director debe pertenecer a la misma línea de investigación
                seleccionada para la tesis.
            */
            $model = new Tesis();

            if (!$model->directorPerteneceALinea($idDirector, $idLinea)) {
                throw new RuntimeException('El director seleccionado no pertenece a la línea de investigación elegida. Cambia la línea o selecciona otro director.');
            }

            $fechaMysql = $this->convertirFechaMysql($fecha);
            $lineaInfo = $this->obtenerLineaInfo($idLinea);
            $slugAutor = $this->crearSlugAutor($nombre, $apellidoPaterno, $apellidoMaterno);

            $portadaDb = null;

            /*
                Pasta física:
                Se guarda físicamente en:
                repositorio_MIIDT/uploads/portadas/Linea_TICs/nombre-autor.webp

                Y en BD se guarda:
                /uploads/portadas/Linea_TICs/nombre-autor.webp
            */
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

            /*
                Portada institucional:
                Se guarda físicamente en:
                repositorio_MIIDT/assets/img/popups/linea_TICs/nombre-autor.png

                No se guarda en BD.
            */
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
            http_response_code(422);

            echo json_encode([
                'ok' => false,
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit;
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
}