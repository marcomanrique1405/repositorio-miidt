<?php
include $_SERVER['DOCUMENT_ROOT'] . '/repositorio_MIIDT/repositorio-miidt/config/database.php';

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_director = isset($_GET['director']) ? $_GET['director'] : '';
$filtro_anio = isset($_GET['anio']) ? $_GET['anio'] : '';
$linea = isset($_GET['linea']) ? $_GET['linea'] : 'CSR'; // ✅ Nuevo parámetro

// Lista de líneas válidas
$lineas_validas = ['CSR', 'Geomática', 'TICs'];
$linea = isset($_GET['linea']) ? $_GET['linea'] : 'CSR';

// Validar que la línea sea válida
if (!in_array($linea, $lineas_validas)) {
    $linea = 'CSR'; // Valor por defecto si no es válida
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

// Aplicar filtros (tu código actual)
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

// ✅ Solo devolver las tarjetas de resultados (sin el wrapper)
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        // Tu código de generación de cards (el que tienes en el while actual)
        echo '<div class="card mb-3 shadow-sm tesis-card">...</div>';
    }
} else {
    echo '<div class="alert alert-info">No hay resultados para mostrar.</div>';
}
?>