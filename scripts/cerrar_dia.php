<?php
/**
 * scripts/cerrar_dia.php
 *
 * Ejecutar vía cron una vez al día, justo después de medianoche
 * (hora del proyecto, ver config/app.php -> APP_TIMEZONE).
 *
 * Ejemplo de crontab (servidor en UTC, proyecto en America/Bogota,
 * medianoche Bogotá ≈ 05:00 UTC):
 *   0 5 * * * php /ruta/al/proyecto/scripts/cerrar_dia.php >> /var/log/cerrar_dia.log 2>&1
 *
 * Reutiliza exactamente la misma lógica transaccional de caída que
 * usa api/reportar.php, para no duplicar la regla de negocio:
 * a todo usuario activo que no reportó el día que acaba de cerrar,
 * se le registra un reporte "falle" y se ejecuta su caída.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../backend/reportes/ReporteRepository.php';
require_once __DIR__ . '/../backend/caidas/CaidaRepository.php';

// El día que se está cerrando es "ayer" respecto al momento en que corre el cron.
$ayer = ahoraApp()->modify('-1 day')->format('Y-m-d');

if ($ayer < RETO_FECHA_INICIO || $ayer > RETO_FECHA_FIN) {
    echo "[$ayer] Fuera del rango del reto, no se hace nada." . PHP_EOL;
    exit(0);
}

$reporteRepo = new ReporteRepository();
$caidaRepo   = new CaidaRepository();

$pendientes = $reporteRepo->usuariosActivosSinReporte($ayer);

echo "[$ayer] Usuarios activos sin reporte: " . count($pendientes) . PHP_EOL;

foreach ($pendientes as $usuarioId) {
    try {
        $reporteRepo->insertar($usuarioId, $ayer, 'falle');
        $info = $caidaRepo->registrarCaida($usuarioId, $ayer);
        echo "  - Usuario #{$usuarioId}: caída automática registrada ({$info['dias']} días, {$info['rango']})." . PHP_EOL;
    } catch (Throwable $e) {
        echo "  - Usuario #{$usuarioId}: ERROR — " . $e->getMessage() . PHP_EOL;
    }
}

echo "Cierre de día completado." . PHP_EOL;
