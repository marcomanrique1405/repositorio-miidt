<?php

$envFile = __DIR__ . '/../.app.env';

if (!file_exists($envFile)) {
    die('Archivo .app.env no encontrado');
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    if (str_starts_with(trim($line), '#')) continue;

    [$key, $value] = explode('=', $line, 2);
    $_ENV[$key] = trim($value);
}

$conn = new mysqli(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD'],
    $_ENV['DB_NAME']
);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");