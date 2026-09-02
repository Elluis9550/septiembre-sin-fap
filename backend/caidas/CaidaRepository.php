<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/rangos.php';

class CaidaRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? getConexion();
    }

    /**
     * Ejecuta el flujo completo y atómico de una caída:
     *   - obtiene días actuales del usuario
     *   - calcula el rango alcanzado
     *   - marca usuarios.activo = false
     *   - inserta el registro histórico en `caidas`
     *
     * Todo dentro de una única transacción: si algo falla, se hace
     * ROLLBACK y el usuario permanece exactamente como estaba antes.
     */
    public function registrarCaida(int $usuarioId, string $fecha): array
    {
        $inicioTransaccion = !$this->pdo->inTransaction();
        if ($inicioTransaccion) {
            $this->pdo->beginTransaction();
        }

        try {
            // Bloqueamos la fila del usuario para evitar condiciones de carrera
            $stmt = $this->pdo->prepare(
                'SELECT dias, activo FROM usuarios WHERE id = :id FOR UPDATE'
            );
            $stmt->execute(['id' => $usuarioId]);
            $usuario = $stmt->fetch();

            if (!$usuario) {
                throw new RuntimeException('Usuario no encontrado');
            }
            if (!$usuario['activo']) {
                // Ya estaba inactivo: no hacemos doble caída
                throw new RuntimeException('El usuario ya se encuentra inactivo');
            }

            $dias  = (int) $usuario['dias'];
            $rango = obtenerRango($dias)['nombre'];

            $stmt = $this->pdo->prepare('UPDATE usuarios SET activo = FALSE WHERE id = :id');
            $stmt->execute(['id' => $usuarioId]);

            $stmt = $this->pdo->prepare(
                'INSERT INTO caidas (usuario_id, fecha, dias, rango)
                 VALUES (:usuario_id, :fecha, :dias, :rango)'
            );
            $stmt->execute([
                'usuario_id' => $usuarioId,
                'fecha'      => $fecha,
                'dias'       => $dias,
                'rango'      => $rango,
            ]);

            if ($inicioTransaccion) {
                $this->pdo->commit();
            }

            return ['dias' => $dias, 'rango' => $rango];
        } catch (Throwable $e) {
            if ($inicioTransaccion && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Incrementa el contador de días de un usuario que sobrevivió.
     * Operación simple pero envuelta en transacción por consistencia
     * con el resto del sistema.
     */
    public function registrarSupervivencia(int $usuarioId): int
    {
        $inicioTransaccion = !$this->pdo->inTransaction();
        if ($inicioTransaccion) {
            $this->pdo->beginTransaction();
        }

        try {
            $stmt = $this->pdo->prepare('SELECT dias FROM usuarios WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $usuarioId]);
            $usuario = $stmt->fetch();

            if (!$usuario) {
                throw new RuntimeException('Usuario no encontrado');
            }

            $nuevosDias = (int) $usuario['dias'] + 1;

            $stmt = $this->pdo->prepare('UPDATE usuarios SET dias = :dias WHERE id = :id');
            $stmt->execute(['dias' => $nuevosDias, 'id' => $usuarioId]);

            if ($inicioTransaccion) {
                $this->pdo->commit();
            }
            return $nuevosDias;
        } catch (Throwable $e) {
            if ($inicioTransaccion && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function historial(): array
    {
        $stmt = $this->pdo->query(
            'SELECT c.id, c.usuario_id, u.nombre, c.fecha, c.dias, c.rango
             FROM caidas c
             JOIN usuarios u ON u.id = c.usuario_id
             ORDER BY c.fecha DESC'
        );
        return $stmt->fetchAll();
    }
}
