<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../backend/reportes/ReporteRepository.php';

requerirMetodo('GET');
requerirAuthApi();

$id = validarId($_GET['id'] ?? null);
if ($id === null) {
    jsonError('ID inválido', 422);
}

$repo = new ReporteRepository();
$mapa = $repo->obtenerPorRangoFechas($id, RETO_FECHA_INICIO, RETO_FECHA_FIN);

// Construir la cuadrícula completa del reto, día por día,
// marcando "pendiente" para fechas futuras o sin reporte.
$inicio = new DateTime(RETO_FECHA_INICIO);
$fin    = new DateTime(RETO_FECHA_FIN);
$hoy    = hoyApp();

$dias = [];
for ($fecha = clone $inicio; $fecha <= $fin; $fecha->modify('+1 day')) {
    $f = $fecha->format('Y-m-d');
    if (isset($mapa[$f])) {
        $estado = $mapa[$f]; // sobrevivio | falle
    } elseif ($f > $hoy) {
        $estado = 'futuro';
    } else {
        $estado = 'sin_reporte';
    }
    $dias[] = ['fecha' => $f, 'estado' => $estado];
}

jsonResponse(['ok' => true, 'dias' => $dias]);
