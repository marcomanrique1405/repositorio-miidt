<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banner</title>
    <link rel="stylesheet" href="../../node_modules/bootstrap/dist/css/bootstrap-grid.min.css">
    <link rel="stylesheet" href="../assets/css/stile.css">
</head>

<body>
    <!-- Header -->
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/REPOSITORIO-MIIDT/Biblioteca-MIIDT/partials/header.php'; ?>
    <!-- Banner principal -->
    <section id="hero-miidt" class="miidt-hero" aria-label="Banner principal">
        <div class="hero-carousel">
            <div class="hero-track">
                <!-- Slide 1 -->
                <div class="hero-slide"
                    style="
                        background-image: url('/REPOSITORIO-MIIDT/Biblioteca-MIIDT/public/assets/img/grupo-interno.jpg');
                        background-size: cover;
                        background-position: center;
                    ">
                </div>
                <!-- Slide 2 -->
                <div class="hero-slide"
                    style="
                        background-image: url('/REPOSITORIO-MIIDT/Biblioteca-MIIDT/public/assets/img/MIIDT.jpg');
                        background-size: cover;
                        background-position: center;
                    ">
                </div>
                 <!-- Slide 3 -->
                <div class="hero-slide"
                    style="
                        background-image: url('/REPOSITORIO-MIIDT/Biblioteca-MIIDT/public/assets/img/grupo-MIIDT.jpg');
                        background-size: cover;
                        background-position: center;
                    ">
                </div>
            </div>

            <div class="hero-content">
                <div>
                    <h2>Construcción Sismo-Resistente</h2>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
