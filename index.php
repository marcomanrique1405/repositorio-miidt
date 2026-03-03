<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/stile.css">
    <title>Inicio</title>
</head>
<body>
<?php include 'partials/header.php'; ?>
<section id="hero-miidt" class="miidt-hero miidt-hero-ajustado" aria-label="Banner principal">
    <div class="hero-carousel">
        <div class="hero-track">
            <div class="hero-slide"
                 style="
                    background-image: url('assets/img/grupo-interno.webp');
                    background-size: cover;
                    background-position: center;
                ">
            </div>
            <div class="hero-slide"
                 style="
                    background-image: url('assets/img/grupo-MIIDT.webp');
                    background-size: cover;
                    background-position: center;
                ">
            </div>
            <div class="hero-slide"
                 style="
                    background-image: url('assets/img/grupo-interno.webp');
                    background-size: cover;
                    background-position: center;
                ">
            </div>
        </div>

        <div class="hero-content">
            <div>
                <h2>Repositorio MIIDT</h2>
            </div>
        </div>
    </div>
</section>

<div class="texto-index">
    <p>La Maestría en Ingeniería para la Innovación y Desarrollo Tecnológico es un
        programa educativo que a través de su planta académica ofrece la oportunidad de que sus estudiantes
        puedan realizar estudios aplicando conocimientos que fortalezcan las necesidades en la primera instancia
        del entorno social que guarda el Estado y de ahí que puedan tener incidencia nacional e internacional;
        por lo anterior este repositorio, es uno más de los espacios disponibles a todo aquel que busca
        continuar o, en su mejor caso, conocer los trabajos que como Maestría estamos trabajando desde una
        perspectiva profesionalizante con impacto en investigación.</p>
</div>
<div class="cartas-chidas">
    <!-- Carta 1 -->
    <div class="flip-card" data-card="CSR">
        <div class="flip-card-inner">
            <div class="flip-card-front">
                <img src="assets/img/imagenes_tarjetas/sismo.webp" alt="Construcción Sismo-Resistente">
                <h3>Construcción Sismo-Resistente</h3>
            </div>
            <div class="flip-card-back">
                <h3>CSR</h3>
                <p>Estudio de la sismo-resistencia y mitigación del riesgo sísmico en edificaciones para implementar soluciones funcionales y operativas. Incluye el desarrollo de análisis, diseño, ejecución y control de construcciones sismo-resistentes y económicas.</p>
            </div>
        </div>
    </div>

    <!-- Carta 2 -->
    <div class="flip-card" data-card="Geomatica">
        <div class="flip-card-inner">
            <div class="flip-card-front">
                <img src="assets/img/imagenes_tarjetas/GEO.webp" alt="Geomática">
                <h3>Geomática</h3>
            </div>
            <div class="flip-card-back">
                <h3>Geomática</h3>
                <p>Estudio de las principales tecnologías aplicadas al modelado espacial y al manejo de información geográfica, para implementar soluciones funcionales y operativas. Incluye el análisis, diseño, ejecución y transferencia de las geotecnologías.</p>
            </div>
        </div>
    </div>

    <!-- Carta 3 -->
    <div class="flip-card" data-card="TICs">
        <div class="flip-card-inner">
            <div class="flip-card-front">
                <img src="assets/img/imagenes_tarjetas/TICS.webp" alt="Tecnologías de la Información y Comunicación">
                <h3>Tecnologías de la Información y Comunicación</h3>
            </div>
            <div class="flip-card-back">
                <h3>TICs</h3>
                <p>Estudio y aplicación de la programación de aplicaciones informáticas, base de datos, y tecnologías computacionales de vanguardia, para el diseño de soluciones funcionales y operativas, y la operación, administración y transferencia de las TIC.</p>
            </div>
        </div>
    </div>
</div>

<div class="carrusel-doble">
    <div class="carrusel-track">

        <div class="carrusel-slide">
            <img src="assets/img/imagene_inicio_index/1.webp" alt="imagen 1">
            <img src="assets/img/imagene_inicio_index/2.webp" alt="imagen 2">
        </div>

        <div class="carrusel-slide">
            <img src="assets/img/imagene_inicio_index/3.webp" alt="imagen 3">
            <img src="assets/img/imagene_inicio_index/4.webp" alt="imagen 4">
        </div>

        <div class="carrusel-slide">
            <img src="assets/img/imagene_inicio_index/5.webp" alt="imagen 5">
            <img src="assets/img/imagene_inicio_index/6.webp" alt="imagen 6">
        </div>

        <div class="carrusel-slide">
            <img src="assets/img/imagene_inicio_index/1.webp" alt="imagen 1">
            <img src="assets/img/imagene_inicio_index/2.webp" alt="imagen 2">
        </div>

    </div>
</div>

<div class="texto-recursos">
    <p>
        Visita nuestros recursos digitales de la UAGro.<br>
        Da click <a href="https://www.posgrado1.uagro.mx/index.php" target="_blank">aquí</a>.
    </p>
</div>





<!-- FOOTHER -->
<?php include 'partials/footer.php'; ?>

<!-- JS de Bootstrap: también SIN slash inicial -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/cartas.js"></script>
</body>
</html>
