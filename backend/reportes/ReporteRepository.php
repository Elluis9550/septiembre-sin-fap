<?php
require_once __DIR__ . '/../../config/database.php';

class ReporteRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? getConexion();
    }

    public function yaReportoHoy(int $usuarioId, string $fecha): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM reportes WHERE usuario_id = :usuario_id AND fecha = :fecha'
        );
        $stmt->execute(['usuario_id' => $usuarioId, 'fecha' => $fecha]);
        return (bool) $stmt->fetchColumn();
    }

    public function insertar(int $usuarioId, string $fecha, string $resultado): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO reportes (usuario_id, fecha, resultado) VALUES (:usuario_id, :fecha, :resultado)'
        );
        $stmt->execute([
            'usuario_id' => $usuarioId,
            'fecha'      => $fecha,
            'resultado'  => $resultado,
        ]);
    }

    /**
     * Devuelve los reportes de un usuario dentro de un rango de fechas,
     * indexados por fecha, para construir la cuadrícula tipo GitHub.
     */
    public function obtenerPorRangoFechas(int $usuarioId, string $desde, string $hasta): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT fecha, resultado FROM reportes
             WHERE usuario_id = :usuario_id AND fecha BETWEEN :desde AND :hasta
             ORDER BY fecha ASC'
        );
        $stmt->execute(['usuario_id' => $usuarioId, 'desde' => $desde, 'hasta' => $hasta]);

        $resultado = [];
        foreach ($stmt->fetchAll() as $fila) {
            $resultado[$fila['fecha']] = $fila['resultado'];
        }
        return $resultado;
    }

    /**
     * IDs de usuarios activos que NO tienen reporte en la fecha dada.
     * Usado por el cron de cierre de día.
     */
    public function usuariosActivosSinReporte(string $fecha): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.id FROM usuarios u
             WHERE u.activo = TRUE
               AND NOT EXISTS (
                   SELECT 1 FROM reportes r
                   WHERE r.usuario_id = u.id AND r.fecha = :fecha
               )'
        );
        $stmt->execute(['fecha' => $fecha]);
        return array_column($stmt->fetchAll(), 'id');
    }
}
