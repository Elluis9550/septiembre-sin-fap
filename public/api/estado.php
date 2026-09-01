<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../backend/reportes/ReporteRepository.php';
require_once __DIR__ . '/../../backend/usuarios/UsuarioRepository.php';

requerirMetodo('GET');
$usuarioId = requerirAuthApi();

$reporteRepo = new ReporteRepository();
$usuarioRepo = new UsuarioRepository();

$hoy = hoyApp();
$usuario = $usuarioRepo->buscarPorId($usuarioId);

jsonResponse([
    'ok' => true,
    'reporte_habilitado' => reporteHabilitado(),
    'segundos_para_reporte' => segundosParaReporte(),
    'ya_reporto_hoy' => $usuario && $usuario['activo']
        ? $reporteRepo->yaReportoHoy($usuarioId, $hoy)
        : true,
    'usuario_activo' => $usuario ? (bool) $usuario['activo'] : false,
    'dia_actual_reto' => diaActualDelReto(),
    'reto_activo' => retoActivo(),
]);
