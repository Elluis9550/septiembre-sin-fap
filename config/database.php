<?php
require_once __DIR__ . '/env.php';

/**
 * Devuelve una conexión PDO a PostgreSQL (Neon), reutilizando
 * la misma instancia durante todo el request.
 */
function getConexion(): PDO
{
    $host     = env('DB_HOST');
    $port     = env('DB_PORT', '5432');
    $dbname   = env('DB_NAME');
    $user     = env('DB_USER');
    $password = env('DB_PASSWORD');
    $sslmode  = env('DB_SSLMODE', 'require');
    $options  = env('DB_OPTIONS');

    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslmode}";
    if ($options !== null && $options !== '') {
        $dsn .= ";options='" . str_replace("'", "''", $options) . "'";
    }

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => true,
            PDO::ATTR_AUTOCOMMIT         => true,
        ]);
    } catch (PDOException $e) {
        error_log('Error de conexión a la base de datos: ' . $e->getMessage());
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Error interno del servidor']);
        exit;
    }

    return $pdo;
}
