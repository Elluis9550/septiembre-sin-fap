<?php
require_once __DIR__ . '/../config/database.php';

$usuarios = [
    ['nombre' => 'Devis Colmenares', 'username' => 'deviscolmenares', 'password' => 'Devis2026!'],
    ['nombre' => 'Martin Meza', 'username' => 'martinmeza', 'password' => 'Martin2026!'],
    ['nombre' => 'Rafael Gomez', 'username' => 'rafaelgomez', 'password' => 'Rafael2026!'],
    ['nombre' => 'Luis Sanchez', 'username' => 'luissanchez', 'password' => 'LuisS2026!'],
    ['nombre' => 'Luis Garcia', 'username' => 'luisgarcia', 'password' => 'LuisG2026!'],
    ['nombre' => 'Javier Toro', 'username' => 'javiertoro', 'password' => 'Javier2026!'],
    ['nombre' => 'Amaury Herrera', 'username' => 'amauryherrera', 'password' => 'Amaury2026!'],
    ['nombre' => 'Santiago Pedraza', 'username' => 'santiagopedraza', 'password' => 'Santiago2026!'],
    ['nombre' => 'Jose Posada', 'username' => 'joseposada', 'password' => 'Jose2026!'],
    ['nombre' => 'Sebastian Zurita', 'username' => 'sebastianzurita', 'password' => 'Sebastian2026!'],
    ['nombre' => 'Juan Rojas', 'username' => 'juanrojas', 'password' => 'Juan2026!'],
];

$pdo = getConexion();
$outputFile = __DIR__ . '/credenciales_usuarios.txt';
$lines = [];

foreach ($usuarios as $usuario) {
    $hash = password_hash($usuario['password'], PASSWORD_DEFAULT);

    $sql = <<<'SQL'
        INSERT INTO usuarios (nombre, username, password, activo, dias, fecha_registro)
        VALUES (:nombre, :username, :password, TRUE, 0, NOW())
        ON CONFLICT (username) DO UPDATE
        SET nombre = EXCLUDED.nombre,
            password = EXCLUDED.password,
            activo = TRUE,
            dias = 0
    SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nombre' => $usuario['nombre'],
        'username' => $usuario['username'],
        'password' => $hash,
    ]);

    $lines[] = sprintf("%s | %s | %s", $usuario['nombre'], $usuario['username'], $usuario['password']);
}

file_put_contents($outputFile, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);

fwrite(STDOUT, "Usuarios creados o actualizados correctamente.\n");
fwrite(STDOUT, "Credenciales guardadas en: {$outputFile}\n");
