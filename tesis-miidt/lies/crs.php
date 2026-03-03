<?php
// Incluir archivo de conexión a la base de datos
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/modules/TesisModule.php';

//  Se inicializan las variables de búsqueda y filtros
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_director = isset($_GET['director']) ? $_GET['director'] : '';
$filtro_anio = isset($_GET['anio']) ? $_GET['anio'] : '';

$tesisModule = new TesisModule($conn);

//línea de investigación actual
$current_linea = 'CSR';

// Obtener resultados usando el módulo
$resultado = $tesisModule->getTesisByLinea($current_linea, $busqueda, $filtro_estado, $filtro_director, $filtro_anio);
$num_resultados = $resultado ? $resultado->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repositorio de Tesis</title>
    <link rel="stylesheet" href="/../repositorio_MIIDT/assets/css/stile.css">
    <link rel="stylesheet" href="/../repositorio_MIIDT/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/slim-select@2.6.0/dist/slimselect.css" rel="stylesheet">
</head>

<body>
<?php require_once __DIR__ . '/../../partials/header.php'; ?>

<section id="hero-miidt" class="miidt-hero" aria-label="Banner principal">
    <div class="hero-carousel">
        <div class="hero-track">
            <div class="hero-slide"
                 style="
                        background-image: url('/../repositorio_MIIDT/assets/img/grupo-interno.webp');
                        background-size: cover;
                        background-position: center;
                    ">
            </div>
            <div class="hero-slide"
                 style="
                        background-image: url('/../repositorio_MIIDT/assets/img/MIIDT.webp');
                        background-size: cover;
                        background-position: center;
                    ">
            </div>
            <div class="hero-slide"
                 style="
                        background-image: url('/../repositorio_MIIDT/assets/img/grupo-MIIDT.webp');
                        background-size: cover;
                        background-position: center;
                    ">
            </div>
        </div>

        <div class="hero-content">
            <div>
                <h2>CONSTRUCCIÓN SISMO-RESISTENTE</h2>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../../partials/tesis_view_content.php'; ?>
<?php require_once __DIR__ . '/../../partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/slim-select@2.6.0/dist/slimselect.min.js"></script>
<script src="/../repositorio_MIIDT/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/../repositorio_MIIDT/assets/js/modal_portada.js"></script>
<script src="/../repositorio_MIIDT/assets/js/busqueda_ajax.js"></script>
</body>

</html>