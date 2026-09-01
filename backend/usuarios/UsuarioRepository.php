<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../functions/rangos.php';

class UsuarioRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getConexion();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nombre, username, activo, dias, fecha_registro FROM usuarios WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    public function listarActivos(string $orden = 'dias'): array
    {
        $ordenPermitido = in_array($orden, ['dias', 'nombre'], true) ? $orden : 'dias';
        $direccion = $ordenPermitido === 'dias' ? 'DESC' : 'ASC';

        $stmt = $this->pdo->query(
            "SELECT id, nombre, username, dias, fecha_registro
             FROM usuarios
             WHERE activo = TRUE
             ORDER BY {$ordenPermitido} {$direccion}, nombre ASC"
        );
        return $stmt->fetchAll();
    }

    public function estaActivo(int $usuarioId): bool
    {
        $stmt = $this->pdo->prepare('SELECT activo FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $usuarioId]);
        $row = $stmt->fetch();
        return $row ? (bool) $row['activo'] : false;
    }

    /**
     * Enriquecer un registro de usuario con su rango calculado al vuelo.
     */
    public function conRango(array $usuario): array
    {
        $usuario['rango'] = obtenerRango((int) $usuario['dias']);
        return $usuario;
    }
}
