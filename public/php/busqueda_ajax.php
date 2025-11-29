<?php
include $_SERVER['DOCUMENT_ROOT'] . '/repositorio_MIIDT/repositorio-miidt/config/database.php';

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_director = isset($_GET['director']) ? $_GET['director'] : '';
$filtro_anio = isset($_GET['anio']) ? $_GET['anio'] : '';

// ✅ VALIDACIÓN DE LÍNEA
$lineas_validas = ['CSR', 'Geomàtica', 'TICs'];
$linea = isset($_GET['linea']) ? $_GET['linea'] : 'CSR';

if (!in_array($linea, $lineas_validas)) {
    $linea = 'CSR';
}

$sql = "SELECT 
            t.id_tesis, 
            t.titulo, 
            t.url, 
            t.portada, 
            YEAR(t.fecha_registro) AS anio_registro,
            t.estado,
            CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', IFNULL(a.apellido_materno, '')) AS autor_completo,
            CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno, '')) AS director_completo,
            li.nombre AS linea_investigacion
        FROM tesis t
        LEFT JOIN autor a ON t.matricula = a.matricula
        LEFT JOIN director d ON t.id_director = d.id_director
        LEFT JOIN linea_investigacion li ON a.id_linea = li.id_linea
        WHERE li.nombre = '" . $conn->real_escape_string($linea) . "'";

if (!empty($busqueda)) {
    $busqueda_escapada = $conn->real_escape_string($busqueda);
    $sql .= " AND (t.titulo LIKE '%$busqueda_escapada%' 
                OR CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', IFNULL(a.apellido_materno, '')) LIKE '%$busqueda_escapada%' 
                OR CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno, '')) LIKE '%$busqueda_escapada%')";
}

if (!empty($filtro_estado)) {
    $sql .= " AND t.estado = '" . $conn->real_escape_string($filtro_estado) . "'";
}

if (!empty($filtro_director)) {
    $sql .= " AND t.id_director = '" . $conn->real_escape_string($filtro_director) . "'";
}

if (!empty($filtro_anio)) {
    $sql .= " AND YEAR(t.fecha_registro) = '" . $conn->real_escape_string($filtro_anio) . "'";
}

$sql .= " ORDER BY t.fecha_registro DESC";

$resultado = $conn->query($sql);

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        
        $portada = $row['portada'];
        $nombre_archivo = basename($portada);
        $info = pathinfo($nombre_archivo);
        $nombre_sin_ext = $info['filename'];
        $extension_original = $info['extension'];

        $extensiones_posibles = ['jpg', 'jpeg', 'png', 'webp', $extension_original];
        $imagen_popup = $portada;

        foreach ($extensiones_posibles as $ext) {
            $ruta_popup = '/repositorio_MIIDT/repositorio-miidt/public/assets/img/popups/linea_CSR/' . $nombre_sin_ext . '.' . $ext;
            $ruta_completa = $_SERVER['DOCUMENT_ROOT'] . $ruta_popup;

            if (file_exists($ruta_completa)) {
                $imagen_popup = $ruta_popup;
                break;
            }
        }

        echo '<div class="card mb-3 shadow-sm tesis-card">
                <div class="row g-0">
                    <div class="col-md-2 col-md-3 col-lg-2 text-center tesis-card-img-container">
                        <img src="' . htmlspecialchars($row['portada']) . '" class="img-fluid" alt="Portada de Tesis">
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
                                    rel="noopener noreferrer">
                                    <i class="fas fa-download me-1"></i> Descargar PDF
                                </a>' : 
                                '<button class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-download me-1"></i> No disponible
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
?>