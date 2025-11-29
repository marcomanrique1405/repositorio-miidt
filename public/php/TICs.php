<?php
// Incluir archivo de conexión a la base de datos
include $_SERVER['DOCUMENT_ROOT'] . '/repositorio_MIIDT/repositorio-miidt/config/database.php';

// Inicializar variables de búsqueda y filtros
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_director = isset($_GET['director']) ? $_GET['director'] : '';
$filtro_anio = isset($_GET['anio']) ? $_GET['anio'] : '';

// Variable para el número de resultados (se calculará después de la consulta)
$num_resultados = 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca MIIDT - Repositorio de Tesis</title>
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/stile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include '../../partials/header.php'; ?>

    <section id="hero-miidt" class="miidt-hero" aria-label="Banner principal">
        <div class="hero-carousel">
            <div class="hero-track">
                <div class="hero-slide"
                    style="
                        background-image: url('/repositorio_MIIDT/repositorio-miidt/public/assets/img/grupo-interno.jpg');
                        background-size: cover;
                        background-position: center;
                    ">
                </div>
                <div class="hero-slide"
                    style="
                        background-image: url('/repositorio_MIIDT/repositorio-miidt/public/assets/img/MIIDT.jpg');
                        background-size: cover;
                        background-position: center;
                    ">
                </div>
                <div class="hero-slide"
                    style="
                        background-image: url('/repositorio_MIIDT/repositorio-miidt/public/assets/img/grupo-MIIDT.jpg');
                        background-size: cover;
                        background-position: center;
                    ">
                </div>
            </div>

            <div class="hero-content">
                <div>
                    <h2>TICs</h2>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-4">
        <form method="GET" action="" class="position-relative search-form ">
            <input type="hidden" id="linea-investigacion" value="TICs">
            <input
                type="text"
                id="input-busqueda"
                name="busqueda"
                class="form-control ps-5"
                placeholder="Buscar por título, autor, director o línea de investigación..."
                value="<?php echo htmlspecialchars($busqueda); ?>">
            <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y text-secondary ms-3"></i>
        </form>
    </div>

    <section class="container my-5">
        <div class="row">

            <div class="col-12 col-xl-9 mb-4 order-xl-1 order-2">

                <?php
                // 🔹 CONSULTA SQL ACTUALIZADA SEGÚN EL MODELO
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
                        WHERE li.nombre = 'TICs'";

                // 🔹 Filtros dinámicos
                if (!empty($busqueda)) {
                    $busqueda_escapada = $conn->real_escape_string($busqueda);
                    $sql .= " AND (
                                t.titulo LIKE '%$busqueda_escapada%' 
                                OR CONCAT(a.nombre, ' ', a.apellido_paterno, ' ', IFNULL(a.apellido_materno, '')) LIKE '%$busqueda_escapada%' 
                                OR CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno, '')) LIKE '%$busqueda_escapada%'
                                OR li.nombre LIKE '%$busqueda_escapada%'
                            )";
                }

                
                if (!empty($filtro_estado)) {
                    $filtro_estado_escapado = $conn->real_escape_string($filtro_estado);
                    $sql .= " AND (t.estado = '$filtro_estado_escapado' 
                        OR t.estado = 'Digital y Fisico')";
                }

                if (!empty($filtro_director)) {
                    $filtro_director_escapado = $conn->real_escape_string($filtro_director);
                    $sql .= " AND t.id_director = '$filtro_director_escapado'";
                }

                if (!empty($filtro_anio)) {
                    $filtro_anio_escapado = $conn->real_escape_string($filtro_anio);
                    $sql .= " AND YEAR(t.fecha_registro) = '$filtro_anio_escapado'";
                }

                $sql .= " ORDER BY t.fecha_registro DESC";

                $resultado = $conn->query($sql);
                $num_resultados = $resultado ? $resultado->num_rows : 0;
                ?>

                <div class="card shadow-sm contenedor-resultados p-3">

                    <h5 class="text-secondary mb-3 resultado-header">
                        Resultado de la búsqueda
                        <span class="text-primary small"><?php echo $num_resultados; ?> Tesis</span>
                    </h5>

                    <div id="resultados-lista">
                    <?php
                    if ($resultado && $num_resultados > 0) {
                        while ($row = $resultado->fetch_assoc()) {

                            $portada = $row['portada'];
                            // Extraer solo el nombre del archivo
                            $nombre_archivo = basename($portada);

                            // Obtener nombre sin extensión y la extensión por separado
                            $info = pathinfo($nombre_archivo);
                            $nombre_sin_ext = $info['filename'];
                            $extension_original = $info['extension'];

                            // Buscar en diferentes formatos (jpg, png, jpeg)
                            $extensiones_posibles = ['jpg', 'jpeg', 'png', 'webp', $extension_original];
                            $imagen_popup = $portada; // Por defecto usar portada

                            foreach ($extensiones_posibles as $ext) {
                                $ruta_popup = '/repositorio_MIIDT/repositorio-miidt/public/assets/img/popups/linea_CSR/' . $nombre_sin_ext . '.' . $ext;
                                $ruta_completa = $_SERVER['DOCUMENT_ROOT'] . $ruta_popup;

                                if (file_exists($ruta_completa)) {
                                    $imagen_popup = $ruta_popup;
                                    break;
                                }
                            }

                            echo '
                            <div class="card mb-3 shadow-sm tesis-card">
                                <div class="row g-0">
                                    <div class="col-md-2 col-md-3 col-lg-2 text-center tesis-card-img-container">
                                        <img src="' . htmlspecialchars($row['portada']) . '" class="img-fluid " alt="Portada de Tesis">
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
                                               <!-- Botón para ver portada en pop-up -->
                                                <button class="btn btn-danger btn-sm ver-portada-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalPortada" 
                                                    data-imagen-popup="' . htmlspecialchars($imagen_popup) . '"
                                                    data-titulo="' . htmlspecialchars($row['titulo']) . '">
                                                    <i class="fas fa-image me-1"></i> Visualizar Portada
                                                </button>

                                                <!-- Botón para descargar PDF -->
' . (!empty($row['url']) ? '
<a href="descargar_tesis.php?url=' . urlencode($row['url']) . '" 
class="btn btn-secondary btn-sm" 
rel="noopener noreferrer">
<i class="fas fa-download me-1"></i> Descargar PDF
</a>' : '
                                                <button class="btn btn-secondary btn-sm" disabled>
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
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-3 mb-4 order-xl-2 order-1 d-flex justify-content-center">
                <div class="card shadow-sm p-3 filtro-panel">
                    <h6><i class="fas fa-filter me-2"></i> Filtros de búsqueda</h6>
                    <form method="GET" action="">
                        <input type="hidden" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>">

                        <select class="form-select my-2" id="filtro-estado" name="estado" onchange="this.form.submit()">
                            <option value="">Estado</option>
                            <option value="Digital" <?php if ($filtro_estado == 'Digital') echo 'selected'; ?>>Digital</option>
                            <option value="Fisico" <?php if ($filtro_estado == 'Fisico') echo 'selected'; ?>>Fisico</option>
                        </select>

                        <select class="form-select my-2" id="filtro-director" name="director" onchange="this.form.submit()">
                            <option value="">Director de tesis</option>
                            <?php
                            $sql_directores = "
                            SELECT DISTINCT d.id_director,
                            CONCAT(d.nombre, ' ', d.apellido_paterno, ' ', IFNULL(d.apellido_materno, '')) AS director_completo
                            FROM director d
                            INNER JOIN tesis t ON d.id_director = t.id_director
                            INNER JOIN autor a ON t.matricula = a.matricula
                            INNER JOIN linea_investigacion li ON a.id_linea = li.id_linea
                            WHERE li.nombre = 'TICs'
                            ORDER BY director_completo ASC";
                            $result_directores = $conn->query($sql_directores);
                            if ($result_directores && $result_directores->num_rows > 0) {
                                while ($director = $result_directores->fetch_assoc()) {
                                    $selected = ($filtro_director == $director['id_director']) ? 'selected' : '';
                                    echo '<option value="' . $director['id_director'] . '" ' . $selected . '>' . htmlspecialchars($director['director_completo']) . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay directores disponibles</option>';
                            }
                            ?>
                        </select>

                        <select class="form-select my-2" id="filtro-anio" name="anio" onchange="this.form.submit()">
                            <option value="">Año de publicación</option>
                            <?php
                            // 🔹 Consulta para mostrar solo los años de tesis de la línea "TICs"
                            $sql_anios = "
                            SELECT DISTINCT YEAR(t.fecha_registro) AS anio
                            FROM tesis t
                            INNER JOIN autor a ON t.matricula = a.matricula
                            INNER JOIN linea_investigacion li ON a.id_linea = li.id_linea
                            WHERE li.nombre = 'TICs'
                            AND t.fecha_registro IS NOT NULL
                            ORDER BY anio DESC";

                            $result_anios = $conn->query($sql_anios);

                            if ($result_anios && $result_anios->num_rows > 0) {
                                while ($anio = $result_anios->fetch_assoc()) {
                                    $selected = ($filtro_anio == $anio['anio']) ? 'selected' : '';
                                    echo '<option value="' . $anio['anio'] . '" ' . $selected . '>' . htmlspecialchars($anio['anio']) . '</option>';
                                }
                            } else {
                                echo '<option disabled>No hay años disponibles</option>';
                            }
                            ?>
                        </select>

                        <div class="filtros-activos-caja mt-4 mb-4">
                            <span class="filtros-activos-label">Filtros activos:</span>
                            <span id="filtros-activos-texto" class="filtros-activos-valor fw-bold">
                                <?php
                                $filtros_activos = [];
                                if (!empty($filtro_estado)) $filtros_activos[] = "Estado: $filtro_estado";
                                if (!empty($filtro_director)) $filtros_activos[] = "Director";
                                if (!empty($filtro_anio)) $filtros_activos[] = "Año: $filtro_anio";

                                echo !empty($filtros_activos) ? implode(', ', $filtros_activos) : 'Ninguno';
                                ?>
                            </span>
                        </div>

                        <div class="mt-2 text-center">
                            <button type="button" id="btn-limpiar-filtros" class="btn btn-danger btn-limpiar">Limpiar todos los filtros</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para ver portada -->
    <div class="modal fade" id="modalPortada" tabindex="-1" aria-labelledby="modalPortadaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPortadaLabel">Portada Oficial De La Tesis</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagenPortada" src="" alt="Portada de la tesis" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
    

    <script src="<?php echo $basePath; ?>../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/modal_portada.js"></script>   
    <script src="../assets/js/busqueda_ajax.js"></script>
</body>

</html>