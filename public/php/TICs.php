<?php
// Incluir archivo de conexión a la base de datos
include '../../config/database.php';
// Incluir el módulo de Tesis
include '../../src/modules/TesisModule.php';

// Inicializar variables de búsqueda y filtros
$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_director = isset($_GET['director']) ? $_GET['director'] : '';
$filtro_anio = isset($_GET['anio']) ? $_GET['anio'] : '';

// Instanciar el módulo
$tesisModule = new TesisModule($conn);

// Definir línea de investigación actual
$current_linea = 'TICs';

// Obtener resultados usando el módulo
$resultado = $tesisModule->getTesisByLinea($current_linea, $busqueda, $filtro_estado, $filtro_director, $filtro_anio);
$num_resultados = $resultado ? $resultado->num_rows : 0;
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
                        background-image: url('../assets/img/grupo-interno.jpg');
                        background-size: cover;
                        background-position: center;
                    ">
                </div>
                <div class="hero-slide"
                    style="
                        background-image: url('../assets/img/MIIDT.jpg');
                        background-size: cover;
                        background-position: center;
                    ">
                </div>
                <div class="hero-slide"
                    style="
                        background-image: url('../assets/img/grupo-MIIDT.jpg');
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

    <?php include '../../partials/tesis_view_content.php'; ?>
    
    <!-- FOOTHER -->
  <?php include '../../partials/footer.php'; ?>
    

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/modal_portada.js"></script>   
    <script src="../assets/js/busqueda_ajax.js"></script>
</body>

</html>