<?php
require_once __DIR__ . '/env.php';

/**
 * Configuración centralizada del reto.
 * Todo el manejo de fecha/hora del sistema pasa por aquí.
 */

date_default_timezone_set(env('APP_TIMEZONE', 'America/Bogota'));

const RETO_FECHA_INICIO  = '2026-09-01';
const RETO_FECHA_FIN     = '2026-09-30';
const RETO_HORA_REPORTE  = '20:00:00'; // hora local a partir de la cual se habilita el reporte

/**
 * Devuelve un DateTime "ahora" en la zona horaria configurada del proyecto.
 * Todo el backend debe usar esta función en vez de confiar en el reloj
 * del cliente o en date_default_timezone_get() implícito.
 */
function ahoraApp(): DateTime
{
    return new DateTime('now', new DateTimeZone(env('APP_TIMEZONE', 'America/Bogota')));
}

function hoyApp(): string
{
    return ahoraApp()->format('Y-m-d');
}

/**
 * Indica si, en este momento, el reporte diario está habilitado
 * (a partir de RETO_HORA_REPORTE, hora del proyecto).
 */
function reporteHabilitado(): bool
{
    $ahora = ahoraApp();
    $limite = new DateTime($ahora->format('Y-m-d') . ' ' . RETO_HORA_REPORTE, $ahora->getTimezone());
    return $ahora >= $limite;
}

/**
 * Segundos que faltan para que se habilite el reporte de hoy.
 * Devuelve 0 si ya está habilitado.
 */
function segundosParaReporte(): int
{
    $ahora = ahoraApp();
    $limite = new DateTime($ahora->format('Y-m-d') . ' ' . RETO_HORA_REPORTE, $ahora->getTimezone());
    if ($ahora >= $limite) {
        return 0;
    }
    return $limite->getTimestamp() - $ahora->getTimestamp();
}

function diaActualDelReto(): int
{
    $inicio = new DateTime(RETO_FECHA_INICIO);
    $hoy    = new DateTime(hoyApp());
    if ($hoy < $inicio) {
        return 0;
    }
    $diff = $inicio->diff($hoy)->days + 1;
    return max(1, $diff);
}

function retoActivo(): bool
{
    $hoy = hoyApp();
    return $hoy >= RETO_FECHA_INICIO && $hoy <= RETO_FECHA_FIN;
}
