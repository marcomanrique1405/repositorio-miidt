<?php
// Archivo de prueba específico para verificar las rutas de popups

echo "<h2>Prueba de Rutas para Imágenes Popup</h2>";

// Simular el proceso que hace el código
$nombre_archivo_portada = "Ana-Liborio.png"; // Ejemplo de la imagen que vi en tu screenshot
$info = pathinfo($nombre_archivo_portada);
$nombre_sin_ext = $info['filename'];

echo "<p><strong>Nombre de archivo de portada:</strong> $nombre_archivo_portada</p>";
echo "<p><strong>Nombre sin extensión:</strong> $nombre_sin_ext</p>";

echo "<hr>";

// Probar diferentes extensiones
$extensiones_posibles = ['jpg', 'jpeg', 'png', 'webp'];
$carpeta_popup = 'linea_Geomatica';

echo "<h3>Buscando imagen popup en carpeta: $carpeta_popup</h3>";

foreach ($extensiones_posibles as $ext) {
    // Ruta relativa para el HTML (desde public/php/)
    $ruta_popup_html = '../assets/img/popups/' . $carpeta_popup . '/' . $nombre_sin_ext . '.' . $ext;
    
    // Ruta absoluta del servidor para verificar si existe
    $ruta_completa = dirname(__DIR__) . '/public/assets/img/popups/' . $carpeta_popup . '/' . $nombre_sin_ext . '.' . $ext;
    
    $existe = file_exists($ruta_completa);
    
    echo "<div style='margin: 10px 0; padding: 10px; background: " . ($existe ? '#d4edda' : '#f8d7da') . "; border-radius: 5px;'>";
    echo "<p><strong>Extensión:</strong> .$ext</p>";
    echo "<p><strong>Ruta HTML:</strong> $ruta_popup_html</p>";
    echo "<p><strong>Ruta completa servidor:</strong> $ruta_completa</p>";
    echo "<p><strong>¿Existe?</strong> " . ($existe ? '✅ SÍ' : '❌ NO') . "</p>";
    echo "</div>";
    
    if ($existe) {
        echo "<p style='color: green; font-weight: bold;'>✓ ¡Imagen encontrada! Esta es la que debería mostrarse en el popup.</p>";
        break;
    }
}

echo "<hr>";
echo "<h3>Archivos reales en la carpeta linea_Geomatica:</h3>";

$ruta_carpeta = dirname(__DIR__) . '/public/assets/img/popups/linea_Geomatica/';
if (is_dir($ruta_carpeta)) {
    $archivos = scandir($ruta_carpeta);
    echo "<ul>";
    foreach ($archivos as $archivo) {
        if ($archivo != '.' && $archivo != '..') {
            $info_archivo = pathinfo($archivo);
            $nombre_base = $info_archivo['filename'];
            $extension = isset($info_archivo['extension']) ? $info_archivo['extension'] : '';
            
            $coincide = ($nombre_base == $nombre_sin_ext);
            
            echo "<li style='color: " . ($coincide ? 'green' : 'black') . "; font-weight: " . ($coincide ? 'bold' : 'normal') . ";'>";
            echo "$archivo";
            if ($coincide) {
                echo " ← ✅ COINCIDE CON LA BÚSQUEDA";
            }
            echo "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ La carpeta no existe: $ruta_carpeta</p>";
}

echo "<hr>";
echo "<h3>Información del servidor:</h3>";
echo "<p><strong>__DIR__ (este archivo):</strong> " . __DIR__ . "</p>";
echo "<p><strong>dirname(__DIR__):</strong> " . dirname(__DIR__) . "</p>";
echo "<p><strong>Ruta esperada de popups:</strong> " . dirname(__DIR__) . "/public/assets/img/popups/</p>";
?>
