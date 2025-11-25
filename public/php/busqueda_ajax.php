<?php
include $_SERVER['DOCUMENT_ROOT'] . '/repositorio_MIIDT/repositorio-miidt/config/database.php';

$busqueda = $_GET['busqueda'] ?? '';
$estado = $_GET['estado'] ?? '';
$director = $_GET['director'] ?? '';
$anio = $_GET['anio'] ?? '';
$linea = $_GET['linea'] ?? '';   // 🔵 ESTA ES LA CLAVE

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
        WHERE 1=1";  // Siempre permite agregar más filtros
        

// 🔵 FILTRO DINÁMICO DE LA LÍNEA DE INVESTIGACIÓN
if (!empty($linea)) {
    $sql .= " AND li.nombre = '$linea'";
}

// 🔵 BÚSQUEDA GENERAL
if (!empty($busqueda)) {
    $busqueda = $conn->real_escape_string($busqueda);
    $sql .= " AND (
                t.titulo LIKE '%$busqueda%' 
                OR CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', IFNULL(a.apellido_materno, '')) LIKE '%$busqueda%'
                OR CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno, '')) LIKE '%$busqueda%'
                OR li.nombre LIKE '%$busqueda%'
            )";
}

// 🔵 FILTROS EXTRAS
if (!empty($estado)) $sql .= " AND t.estado = '$estado'";
if (!empty($director)) $sql .= " AND t.id_director = '$director'";
if (!empty($anio)) $sql .= " AND YEAR(t.fecha_registro) = '$anio'";

$sql .= " ORDER BY t.fecha_registro DESC";

$resultado = $conn->query($sql);

echo '<h5 class="text-secondary mb-3 resultado-header">
        Resultado de la búsqueda 
        <span class="text-primary small">' . ($resultado->num_rows ?? 0) . ' Tesis</span>
      </h5>';

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        echo '
        <div class="card mb-3 shadow-sm tesis-card">
            <div class="row g-0">
                <div class="col-md-2 col-md-3 col-lg-2 text-center tesis-card-img-container">
                    <img src="' . htmlspecialchars($row['portada']) . '" class="img-fluid" alt="Portada de Tesis">
                </div>
                <div class="col-12 col-md-9 col-lg-10">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">' . htmlspecialchars($row['titulo']) . '</h5>
                        <p><b>Autor:</b> ' . htmlspecialchars($row['autor_completo']) . '</p>
                        <p><b>Director:</b> ' . htmlspecialchars($row['director_completo']) . '</p>
                        <p><b>Línea:</b> ' . htmlspecialchars($row['linea_investigacion']) . '</p>
                        <p><b>Estado:</b> ' . htmlspecialchars($row['estado']) . '</p>
                        <p><b>Año:</b> ' . htmlspecialchars($row['anio_registro']) . '</p>

                        <a href="' . htmlspecialchars($row['url']) . '" class="btn btn-secondary btn-sm" download>
                            <i class="fas fa-download me-1"></i> Descargar PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>';
    }
} else {
    echo '<div class="alert alert-info">No hay resultados para mostrar.</div>';
}
?>
