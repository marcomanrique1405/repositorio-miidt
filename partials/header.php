<?php
// Ruta base (ajústala si cambias la carpeta raíz del proyecto)
$basePath = '/REPOSITORIO-MIIDT/Biblioteca-MIIDT/public/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca MIIDT</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>../node_modules/bootstrap/dist/css/bootstrap.min.css">

    <!-- Tu hoja de estilos -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/stile.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid px-4">

            <!-- Logo -->
            <img src="<?php echo $basePath; ?>assets/img/logo.png" alt="logo" class="logo-img">

            <!-- Botón responsive -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menú -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="<?php echo $basePath; ?>index.php" class="nav-link active">Inicio</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" id="navbarDropdownLies" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Líes
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownLies">
                            <li><a href="<?php echo $basePath; ?>php/CSR_modulo_tesis.php" class="dropdown-item">CSR</a></li>
                            <li><a href="<?php echo $basePath; ?>php/Geomática.php" class="dropdown-item">Geomática</a></li>
                            <li><a href="<?php echo $basePath; ?>php/TICs.php" class="dropdown-item">TIC's</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?php echo $basePath; ?>../node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $basePath; ?>assets/js/dropdown-hover.js"></script>
</body>
</html>
