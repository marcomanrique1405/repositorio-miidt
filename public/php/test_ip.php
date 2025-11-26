<?php
// test_descarga.php - Para diagnosticar el problema

echo "<h2>🔍 Diagnóstico de Descarga</h2>";
echo "<hr>";

// 1. Verificar IP
$ip_usuario = $_SERVER['REMOTE_ADDR'];
echo "<h3>1. Tu IP:</h3>";
echo "<p><code>$ip_usuario</code></p>";

// 2. Verificar acceso
$ips_permitidas = ['192.168.0.0/24', '127.0.0.1', '::1'];

function ip_en_rango($ip, $rango) {
    if (strpos($rango, '/') === false) {
        return $ip === $rango;
    }
    list($subnet, $mask) = explode('/', $rango);
    $ip_long = ip2long($ip);
    $subnet_long = ip2long($subnet);
    if ($ip_long === false || $subnet_long === false) return false;
    $mask_long = -1 << (32 - (int)$mask);
    $subnet_long &= $mask_long;
    return ($ip_long & $mask_long) == $subnet_long;
}

$acceso = false;
foreach ($ips_permitidas as $rango) {
    if (ip_en_rango($ip_usuario, $rango)) {
        $acceso = true;
        break;
    }
}

echo "<h3>2. Acceso:</h3>";
if ($acceso) {
    echo "<p style='color: green;'>✅ PERMITIDO</p>";
} else {
    echo "<p style='color: red;'>❌ DENEGADO</p>";
}

// 3. Verificar parámetro URL
echo "<h3>3. Parámetro URL:</h3>";
if (isset($_GET['url'])) {
    echo "<p>URL recibida: <code>" . htmlspecialchars($_GET['url']) . "</code></p>";
    
    $url_archivo = $_GET['url'];
    $ruta_completa = $_SERVER['DOCUMENT_ROOT'] . $url_archivo;
    
    echo "<p>Ruta completa: <code>" . htmlspecialchars($ruta_completa) . "</code></p>";
    
    // 4. Verificar si existe
    echo "<h3>4. Archivo existe:</h3>";
    if (file_exists($ruta_completa)) {
        echo "<p style='color: green;'>✅ SÍ existe</p>";
        echo "<p>Tamaño: " . filesize($ruta_completa) . " bytes</p>";
        echo "<p>Extensión: " . pathinfo($ruta_completa, PATHINFO_EXTENSION) . "</p>";
        
        // 5. Botón de descarga real
        echo "<hr>";
        echo "<h3>5. Probar descarga:</h3>";
        echo "<a href='descargar_tesis.php?url=" . urlencode($url_archivo) . "' class='btn btn-primary'>Descargar Ahora</a>";
    } else {
        echo "<p style='color: red;'>❌ NO existe</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ No se envió ninguna URL</p>";
    echo "<p>Ejemplo de uso: <code>test_descarga.php?url=/ruta/al/archivo.pdf</code></p>";
}

echo "<hr>";
echo "<h3>DOCUMENT_ROOT:</h3>";
echo "<p><code>" . $_SERVER['DOCUMENT_ROOT'] . "</code></p>";
?>
```

## 🎯 Pasos para probar:

1. **Guarda ambos archivos** en la raíz de tu proyecto

2. **Copia la URL completa de alguna tesis** de tu base de datos (columna `url`)

3. **Abre el test:**
```
   http://localhost/repositorio_MIIDT/repositorio-miidt/test_descarga.php?url=/ruta/completa/del/archivo.pdf