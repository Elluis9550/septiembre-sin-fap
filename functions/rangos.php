<?php
/**
 * Tabla de rangos centralizada. Único lugar del sistema donde
 * se define la relación días -> rango. No duplicar esta lista
 * en ninguna otra parte del código.
 *
 * Ordenada de mayor a menor umbral para facilitar la búsqueda.
 */
function tablaRangos(): array
{
return [
    ['dias' => 30, 'nombre' => 'Monge',               'icono' => '🏆', 'color' => '#FFD700'],
    ['dias' => 29, 'nombre' => 'Rey',                 'icono' => '👑', 'color' => '#FFC107'],
    ['dias' => 28, 'nombre' => 'General de Ejército', 'icono' => '🎖️', 'color' => '#FFB300'],
    ['dias' => 27, 'nombre' => 'Coronel',             'icono' => '⭐', 'color' => '#F9A825'],
    ['dias' => 26, 'nombre' => 'Mayor',               'icono' => '🌟', 'color' => '#F57C00'],
    ['dias' => 25, 'nombre' => 'Capitán',             'icono' => '⚔️', 'color' => '#EF6C00'],
    ['dias' => 24, 'nombre' => 'Primer Teniente',     'icono' => '🛡️', 'color' => '#8E44AD'],
    ['dias' => 21, 'nombre' => 'Segundo Teniente',    'icono' => '🗡️', 'color' => '#7E57C2'],
    ['dias' => 16, 'nombre' => 'Aspirante a Oficial','icono' => '🔥', 'color' => '#E64A19'],
    ['dias' => 14, 'nombre' => 'Subteniente',         'icono' => '🎖️', 'color' => '#3949AB'],
    ['dias' => 11, 'nombre' => 'Primer Sargento',    'icono' => '💪', 'color' => '#00897B'],
    ['dias' => 6,  'nombre' => 'Segundo Sargento',    'icono' => '🪖', 'color' => '#43A047'],
    ['dias' => 3,  'nombre' => 'Tercer Sargento',     'icono' => '🔰', 'color' => '#66BB6A'],
    ['dias' => 2,  'nombre' => 'Cabo',               'icono' => '🥉', 'color' => '#78909C'],
    ['dias' => 0,  'nombre' => 'Soldado',             'icono' => '🥚', 'color' => '#9E9E9E'],
];
}

/**
 * Devuelve el rango correspondiente a una cantidad de días.
 * Siempre calculado al vuelo — el rango nunca se persiste en `usuarios`.
 */
function obtenerRango(int $dias): array
{
    foreach (tablaRangos() as $rango) {
        if ($dias >= $rango['dias']) {
            return $rango;
        }
    }
    // Fallback teórico (no debería alcanzarse porque el umbral 0 siempre matchea)
    $rangos = tablaRangos();
    return end($rangos);
}

function nombreRango(int $dias): string
{
    return obtenerRango($dias)['nombre'];
}

function rangoPorNombre(string $nombre): array
{
    $nombre = trim($nombre);
    foreach (tablaRangos() as $rango) {
        if ($rango['nombre'] === $nombre) {
            return $rango;
        }
    }

    return [
        'dias' => 0,
        'nombre' => $nombre !== '' ? $nombre : 'Caído',
        'icono' => '💀',
        'color' => '#ef4a5f',
    ];
}
