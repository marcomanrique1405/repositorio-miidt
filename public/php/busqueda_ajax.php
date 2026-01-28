<?php
include '../../config/database.php';
include '../../src/modules/TesisModule.php'; // ✅ NUEVO: Incluir el módulo

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_director = isset($_GET['director']) ? $_GET['director'] : '';
$filtro_anio = isset($_GET['anio']) ? $_GET['anio'] : '';

// ✅ VALIDACIÓN DE LÍNEA
$lineas_validas = ['CSR', 'Geomática', 'Geomàtica', 'TICs'];
$linea = isset($_GET['linea']) ? $_GET['linea'] : 'CSR';

// Normalizar Geomàtica (acento grave) a Geomática (acento agudo) para consistencia con BD
if ($linea === 'Geomàtica') {
    $linea = 'Geomática';
}

if (!in_array($linea, $lineas_validas)) {
    $linea = 'CSR';
}

// ✅ NUEVO: Usar TesisModule en lugar de SQL directo
$tesisModule = new TesisModule($conn);
$resultado = $tesisModule->getTesisByLinea($linea, $busqueda, $filtro_estado, $filtro_director, $filtro_anio);
$num_resultados = $resultado ? $resultado->num_rows : 0;

// Iniciar captura de salida HTML
ob_start();

if ($resultado && $num_resultados > 0) {
    while ($row = $resultado->fetch_assoc()) {

        $portada = $row['portada'];
        $nombre_archivo = basename($portada);
        $info = pathinfo($nombre_archivo);
        $nombre_sin_ext = $info['filename'];
        $extension_original = isset($info['extension']) ? $info['extension'] : '';

        // ✅ BUSCAR PORTADA PRINCIPAL (en la ruta original)
        $extensiones_posibles = ['jpg', 'jpeg', 'png', 'webp'];
        if (!empty($extension_original) && !in_array(strtolower($extension_original), $extensiones_posibles)) {
            $extensiones_posibles[] = $extension_original;
        }

        $portada_encontrada = $portada; // Por defecto, usar la portada de la BD
        $directorio_portada = dirname($portada);

        // Verificar si existe la portada con diferentes extensiones
        foreach ($extensiones_posibles as $ext) {
            $ruta_portada = $directorio_portada . '/' . $nombre_sin_ext . '.' . $ext;
            $ruta_completa_portada = $_SERVER['DOCUMENT_ROOT'] . $ruta_portada;

            if (file_exists($ruta_completa_portada)) {
                $portada_encontrada = $ruta_portada;
                break;
            }
        }

        // ========================================
        // GENERAR NOMBRE COMPLETO DEL AUTOR (POPUP)
        // ========================================

        $autor = $row['autor_completo'];

        // Normalizar nombres: minusculas, sin acentos, sin Ñ
        $mapa = [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'ñ' => 'n',
            'Ñ' => 'N'
        ];

        $autor_limpio = strtr($autor, $mapa);
        $autor_limpio = strtolower($autor_limpio);

        // Convertir espacios → guiones
        $nombre_completo_popup = str_replace(' ', '-', $autor_limpio);

        // ========================================
        // SELECCIONAR CARPETA SEGÚN LÍNEA
        // ========================================
        switch ($row['linea_investigacion']) {
            case 'CSR':
                $carpeta_popup = 'linea_CSR';
                break;
            case 'Geomática':
            case 'Geomàtica':
                $carpeta_popup = 'linea_Geomatica';
                break;
            case 'TICs':
                $carpeta_popup = 'linea_TICs';
                break;
            default:
                $carpeta_popup = 'linea_CSR';
        }

        // ========================================
        // BUSCAR ARCHIVO EXACTO
        // ========================================
        $extensiones = ['png', 'jpg', 'jpeg', 'webp'];
        $imagen_popup = $portada_encontrada;

        foreach ($extensiones as $ext) {

            // Ruta relativa real del proyecto
            $ruta_rel = "public/assets/img/popups/$carpeta_popup/{$nombre_completo_popup}.$ext";

            // Ruta absoluta en el disco (para verificar)
            $ruta_abs = __DIR__ . "/../../$ruta_rel";

            error_log("Probando ruta: $ruta_abs");


            if (file_exists($ruta_abs)) {

                // Ruta que el navegador SI reconoce
                $imagen_popup = "/repositorio_MIIDT/repositorio-miidt/$ruta_rel";
                error_log("✅ Imagen popup encontrada: $imagen_popup");
                break;
            }
        }

        // ✅ Si no se encontró imagen popup, usar la portada como fallback
if ($imagen_popup === null) {
    $imagen_popup = $portada_encontrada;
    error_log("⚠️ No se encontró popup, usando portada: $imagen_popup");
}


        echo '<div class="card mb-3 shadow-sm tesis-card">
                <div class="row g-0">
                    <div class="col-md-2 col-md-3 col-lg-2 text-center tesis-card-img-container">
                        <img src="' . htmlspecialchars($portada_encontrada) . '" class="img-fluid" alt="Portada de Tesis">
                    </div>
                    <div class="col-12 col-md-9 col-lg-10">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">' . htmlspecialchars($row['titulo']) . '</h5>
                            <p class="card-text mb-1"><b>Autor:</b> ' . htmlspecialchars($row['autor_completo']) . '</p>
                            <p class="card-text mb-1"><b>Director:</b> ' . htmlspecialchars($row['director_completo']) . '</p>
                            <p class="card-text mb-1"><b>Línea de investigación:</b> ' . htmlspecialchars($row['linea_investigacion']) . '</p>
                            <p class="card-text mb-1"><b>Estado:</b> ' . htmlspecialchars($row['estado']) . '</p>
                            <p class="card-text mb-1"><b>Año de registro:</b> ' . htmlspecialchars($row['anio_registro']) . '</p>
                            
                            <div class="mt-3 d-flex flex-column flex-md-row justify-content-start justify-content-md-between gap-2">
                                <button class="btn btn-danger btn-sm ver-portada-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalPortada" 
                                    data-imagen-popup="' . htmlspecialchars($imagen_popup) . '"
                                    data-titulo="' . htmlspecialchars($row['titulo']) . '">
                                    <i class="fas fa-image me-1"></i> Visualizar Portada
                                </button>

                                ' . (!empty($row['url']) ?
                                   '<a href="descargar_tesis.php?url=' . urlencode($row['url']) . '" 
                                    class="btn btn-secondary btn-sm"
                                    target="_blank"
                                    rel="noopener noreferrer">
                                    <i class="fas fa-download me-1"></i> Vista previa PDF
                                    </a>' :
                                   '<button class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-download me-1"></i> No disponible en PDF
                                </button>') . '
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
    }
} else {
    echo '<div class="alert alert-info">No hay resultados para mostrar.</div>';
}

// Capturar el HTML generado
$html_output = ob_get_clean();

// Devolver respuesta JSON
header('Content-Type: application/json');
echo json_encode([
    'html' => $html_output,
    'count' => $num_resultados
]);
