<?php
/**
 * Cargador mínimo de variables de entorno desde un archivo .env
 * sin depender de librerías externas (mantenemos PHP puro).
 */

function loadEnv(string $path): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }

    if (!file_exists($path)) {
        // En producción real las variables pueden venir ya inyectadas
        // por el hosting, así que no es un error fatal que falte el archivo.
        $loaded = true;
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Quitar comillas envolventes si existen
        $value = trim($value, "\"'");

        if (getenv($key) === false) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }

    $loaded = true;
}

loadEnv(__DIR__ . '/../.env');

function env(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value !== false ? $value : $default;
}
