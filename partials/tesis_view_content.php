<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/repositorio_miidt');
define('BASE_URL', '/repositorio_miidt/');
?>
<div class="container my-4">
    <form method="GET" action="" class="position-relative search-form ">
        <input type="hidden" id="linea-investigacion" value="<?php echo htmlspecialchars($current_linea); ?>">
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
                            $nombre_archivo = basename($portada);
                            $info = pathinfo($nombre_archivo);
                            $nombre_sin_ext = $info['filename'];
                            $extension_original = isset($info['extension']) ? $info['extension'] : '';

                            // Busca la portada principal con diferentes extensiones
                            $extensiones_posibles = ['jpg', 'jpeg', 'png', 'webp'];
                            if (!empty($extension_original) && !in_array(strtolower($extension_original), $extensiones_posibles)) {
                                $extensiones_posibles[] = $extension_original;
                            }

                            $portada_encontrada = $portada; // Por defecto, usar la portada de la BD
                            $directorio_portada = dirname($portada);

                            // Verificar si existe la portada con diferentes extensiones
                            foreach ($extensiones_posibles as $ext) {
                                $ruta_portada = $directorio_portada . '/' . $nombre_sin_ext . '.' . $ext;
                                $ruta_completa_portada = PROJECT_ROOT . $ruta_portada;

                                if (file_exists($ruta_completa_portada)) {
                                    $portada_encontrada = $ruta_portada;
                                    break;
                                }
                            }
                            // Generar nombre para imagen del pop-up basado en el autor
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

                            // seleccionar carpeta según línea de investigación
                            switch ($row['linea_investigacion']) {
                                case 'CSR':
                                    $carpeta_popup = 'linea_CSR';
                                    break;
                                case 'Geomatica':
                                case 'Geomatica':
                                    $carpeta_popup = 'linea_Geomatica';
                                    break;
                                case 'TICs':
                                    $carpeta_popup = 'linea_TICs';
                                    break;
                                default:
                                    $carpeta_popup = 'linea_CSR';
                            }
                            // Buscar imagen para el pop-up con diferentes extensiones
                            $baseUrl = '/';
                            $extensiones = ['png', 'jpg', 'jpeg', 'webp'];
                            $imagen_popup = $portada_encontrada;

                            foreach ($extensiones as $ext) {

                                // Ruta relativa que el navegador puede usar (para mostrar en el pop-up)
                                $ruta_rel = "assets/img/popups/$carpeta_popup/{$nombre_completo_popup}.$ext";

                                // Ruta absoluta en el disco (para verificar)
                                $ruta_abs = PROJECT_ROOT . '/' . $ruta_rel;

                                error_log("Probando ruta: $ruta_abs");


                                if (file_exists($ruta_abs)) {

                                    // Ruta que el navegador SI reconoce
                                    $imagen_popup = BASE_URL . $ruta_rel;
                                    error_log("✅ Imagen popup encontrada: $imagen_popup");
                                    break;
                                }
                            }

                            // si no encontro la imagen para el pop-up, usa la portada 
                            if ($imagen_popup === null) {
                                $imagen_popup = $portada_encontrada;
                                error_log("⚠️ No se encontró popup, usando portada: $imagen_popup");
                            }


                            echo '
                            <div class="card mb-3 shadow-sm tesis-card">
                                <div class="row g-0">
                                    <div class="col-md-2 col-md-3 col-lg-2 text-center tesis-card-img-container">
                                        <img src="' . BASE_URL . htmlspecialchars(ltrim($portada_encontrada, '/')) . '" class="img-fluid" alt="Portada de Tesis">
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
                                                <a href="/tesis-miidt/descargar_tesis.php?url=' . urlencode($row['url']) . '" 
                                                class="btn btn-secondary btn-sm" 
                                                target="_blank"
                                                rel="noopener noreferrer">
                                                <i class="fas fa-download me-1"></i> Vista previa PDF
                                                </a>' : '
                                                <button class="btn btn-secondary btn-sm" disabled>
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
                    ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-3 mb-4 order-xl-2 order-1 d-flex justify-content-center">
            <div class="card shadow-sm p-3 filtro-panel">
                <h6><i class="fas fa-filter me-2"></i> Filtros de búsqueda</h6>
                <form method="GET" action="">
                    <input type="hidden" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>">

                    <select class="form-select my-2 slim" id="filtro-estado" name="estado">
                        <option value="">Estado</option>
                        <option value="Digital" <?php if ($filtro_estado == 'Digital') echo 'selected'; ?>>Digital</option>
                        <option value="Fisico" <?php if ($filtro_estado == 'Fisico') echo 'selected'; ?>>Fisico</option>
                    </select>

                    <select class="form-select my-2 slim" id="filtro-director" name="director">
                        <option value="">Director de tesis</option>
                        <?php
                        $result_directores = $tesisModule->getDirectoresByLinea($current_linea);
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

                    <select class="form-select my-2 slim" id="filtro-anio" name="anio">
                        <option value="">Año de publicación</option>
                        <?php
                        $result_anios = $tesisModule->getAniosByLinea($current_linea);

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
                        <button type="button" id="btn-limpiar-filtros" class="btn btn-danger btn-limpiar">Limpiar filtros</button>
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