<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../backend/usuarios/UsuarioRepository.php';
require_once __DIR__ . '/../../backend/reportes/ReporteRepository.php';
require_once __DIR__ . '/../../backend/caidas/CaidaRepository.php';
require_once __DIR__ . '/../../functions/rangos.php';

requerirMetodo('POST');
$usuarioId = requerirAuthApi();

// El backend es la autoridad final sobre la hora: nunca confiar en el cliente.
if (!reporteHabilitado()) {
    jsonError('El reporte diario aún no está habilitado', 403);
}

if (!retoActivo()) {
    jsonError('El reto no está activo en este momento', 403);
}

$body = leerJsonBody();
$resultado = $body['resultado'] ?? '';

if (!in_array($resultado, ['sobrevivio', 'falle'], true)) {
    jsonError('Resultado inválido', 422);
}

$usuarioRepo = new UsuarioRepository();
$reporteRepo = new ReporteRepository();
$caidaRepo   = new CaidaRepository();

if (!$usuarioRepo->estaActivo($usuarioId)) {
    jsonError('El usuario no está activo en el reto', 403);
}

$fechaHoy = hoyApp();

if ($reporteRepo->yaReportoHoy($usuarioId, $fechaHoy)) {
    jsonError('Ya enviaste tu reporte de hoy', 409);
}

$pdo = getConexion();

try {
    $pdo->beginTransaction();

    // 1. Registrar el reporte (protegido además por UNIQUE(usuario_id, fecha) en BD)
    $reporteRepo->insertar($usuarioId, $fechaHoy, $resultado);

    // 2. Ejecutar la consecuencia correspondiente dentro de la misma transacción.
    if ($resultado === 'falle') {
        $info = $caidaRepo->registrarCaida($usuarioId, $fechaHoy);
    } else {
        $nuevosDias = $caidaRepo->registrarSupervivencia($usuarioId);
        $info = ['dias' => $nuevosDias, 'rango' => obtenerRango($nuevosDias)];
    }

    $pdo->commit();

    jsonResponse([
        'ok' => true,
        'resultado' => $resultado,
        'dias' => $info['dias'] ?? null,
        'dias_alcanzados' => $info['dias'] ?? null,
        'rango' => $info['rango'],
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Puede ocurrir por la constraint UNIQUE en caso de doble envío simultáneo
    error_log('Error al registrar reporte: ' . $e->getMessage());
    jsonError('No se pudo registrar el reporte. Intenta de nuevo.', 409);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Error inesperado en reportar.php: ' . $e->getMessage());
    jsonError('Error interno al procesar el reporte', 500);
}
