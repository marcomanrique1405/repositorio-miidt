<?php
$ip_usuario = $_SERVER['REMOTE_ADDR'];
echo "<h2>Información de Red</h2>";
echo "Tu IP: <strong>" . $ip_usuario . "</strong><br>";

$rango = '10.15.64.0/18';
list($subnet, $mask) = explode('/', $rango);
$ip_long = ip2long($ip_usuario);
$subnet_long = ip2long($subnet);
$mask_long = -1 << (32 - (int)$mask);

if (($ip_long & $mask_long) == ($subnet_long & $mask_long)) {
    echo "<div style='color: green;'>✓ Acceso PERMITIDO desde Red Ing Torre</div>";
} else {
    echo "<div style='color: red;'>✗ Acceso DENEGADO - No estás en Red Ing Torre</div>";
}
?>