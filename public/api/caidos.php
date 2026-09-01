<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/rangos.php';
require_once __DIR__ . '/../../backend/caidas/CaidaRepository.php';

requerirMetodo('GET');
requerirAuthApi();

$repo = new CaidaRepository();
$caidas = $repo->historial();

$caidasConRango = array_map(function (array $caida): array {
    $rango = rangoPorNombre((string) ($caida['rango'] ?? 'Caído'));
    return [
        'id' => (int) $caida['id'],
        'usuario_id' => (int) $caida['usuario_id'],
        'nombre' => (string) $caida['nombre'],
        'dias' => (int) $caida['dias'],
        'fecha' => (string) $caida['fecha'],
        'rango' => [
            'nombre' => $rango['nombre'],
            'icono' => $rango['icono'],
            'color' => $rango['color'],
        ],
    ];
}, $caidas);

jsonResponse(['ok' => true, 'caidas' => $caidasConRango]);
