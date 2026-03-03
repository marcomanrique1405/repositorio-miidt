<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/modules/TesisModule.php';

$busqueda = $_GET['busqueda'] ?? '';
$estado   = $_GET['estado'] ?? '';
$director = $_GET['director'] ?? '';
$anio     = $_GET['anio'] ?? '';
$linea    = $_GET['linea'] ?? 'CSR';

$busqueda = trim($_GET['busqueda'] ?? '');

if ($busqueda !== '') {
    // Si hay una búsqueda, se mantienen los filtros
    $estado   = '';
    $director = '';
    $anio     = '';
}

$tesisModule = new TesisModule($conn);

$resultado = $tesisModule->getTesisByLinea(
    $linea,
    $busqueda,
    $estado,
    $director,
    $anio
);

$html = '';
$count = 0;

if ($resultado && $resultado->num_rows > 0) {

    $count = $resultado->num_rows;

    while ($row = $resultado->fetch_assoc()) {
        //portada
        $portada = $row['portada'];
        $nombre_archivo = basename($portada);
        $info = pathinfo($nombre_archivo);
        $nombre_sin_ext = $info['filename'];

        $extensiones = ['jpg','jpeg','png','webp'];
        $portada_encontrada = $portada;
        $directorio = dirname($portada);

        foreach ($extensiones as $ext) {
            $ruta = $directorio . '/' . $nombre_sin_ext . '.' . $ext;
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $ruta)) {
                $portada_encontrada = $ruta;
                break;
            }
        }
        //imgaen popup
        $autor = strtolower(strtr($row['autor_completo'], [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n',
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N'
        ]));

        $autor_slug = str_replace(' ', '-', $autor);

        switch ($row['linea_investigacion']) {
            case 'Geomática':
            case 'Geomàtica':
                $carpeta = 'linea_Geomatica';
                break;
            case 'TICs':
                $carpeta = 'linea_TICs';
                break;
            default:
                $carpeta = 'linea_CSR';
        }

        $imagen_popup = $portada_encontrada;
        foreach ($extensiones as $ext) {
            $ruta_popup = "/assets/img/popups/$carpeta/$autor_slug.$ext";
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $ruta_popup)) {
                $imagen_popup = $ruta_popup;
                break;
            }
        }

        $html .= '
        <div class="card mb-3 shadow-sm tesis-card">
            <div class="row g-0">

                <div class="col-md-2 col-lg-2 text-center tesis-card-img-container">
                    <img src="' . htmlspecialchars($portada_encontrada) . '" 
                         class="img-fluid" 
                         alt="Portada de Tesis">
                </div>

                <div class="col-md-10 col-lg-10">
                    <div class="card-body">

                        <h5 class="card-title fw-bold">
                            ' . htmlspecialchars($row['titulo']) . '
                        </h5>

                        <p class="card-text mb-1"><b>Autor:</b> ' . htmlspecialchars($row['autor_completo']) . '</p>
                        <p class="card-text mb-1"><b>Director:</b> ' . htmlspecialchars($row['director_completo']) . '</p>
                        <p class="card-text mb-1"><b>Línea de investigación:</b> ' . htmlspecialchars($row['linea_investigacion']) . '</p>
                        <p class="card-text mb-1"><b>Estado:</b> ' . htmlspecialchars($row['estado']) . '</p>
                        <p class="card-text mb-1"><b>Año de registro:</b> ' . htmlspecialchars($row['anio_registro']) . '</p>

                        <div class="mt-3 d-flex flex-column flex-md-row justify-content-between gap-2">
                            <button class="btn btn-danger btn-sm ver-portada-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#modalPortada"
                                data-imagen-popup="' . htmlspecialchars($imagen_popup) . '"
                                data-titulo="' . htmlspecialchars($row['titulo']) . '">
                                <i class="fas fa-image me-1"></i> Visualizar Portada
                            </button>';

        if (!empty($row['url'])) {
            $html .= '
                            <a href="descargar_tesis.php?url=' . urlencode($row['url']) . '"
                               class="btn btn-secondary btn-sm"
                               target="_blank" rel="noopener">
                                <i class="fas fa-download me-1"></i> Vista previa PDF
                            </a>';
        } else {
            $html .= '
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="fas fa-download me-1"></i> No disponible en PDF
                            </button>';
        }

        $html .= '
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

} else {
    $html = '<div class="alert alert-info">No hay resultados para mostrar.</div>';
}

echo json_encode([
    'html'  => $html,
    'count' => $count
], JSON_UNESCAPED_UNICODE);

exit;