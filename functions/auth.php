<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

function iniciarSesionSegura(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

/**
 * Intenta autenticar. Devuelve el array del usuario si es correcto, o null.
 */
function intentarLogin(string $username, string $password): ?array
{
    $pdo = getConexion();
    $stmt = $pdo->prepare('SELECT id, nombre, username, password, activo, dias FROM usuarios WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($password, $usuario['password'])) {
        return null;
    }

    unset($usuario['password']);
    return $usuario;
}

function crearSesionUsuario(array $usuario): void
{
    iniciarSesionSegura();
    session_regenerate_id(true);
    $_SESSION['usuario_id']       = $usuario['id'];
    $_SESSION['usuario_nombre']   = $usuario['nombre'];
    $_SESSION['usuario_username'] = $usuario['username'];
}

function usuarioAutenticado(): bool
{
    iniciarSesionSegura();
    return !empty($_SESSION['usuario_id']);
}

function usuarioIdActual(): ?int
{
    iniciarSesionSegura();
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Para páginas HTML: redirige al login si no hay sesión.
 */
function requerirAuthPagina(): void
{
    iniciarSesionSegura();
    if (empty($_SESSION['usuario_id'])) {
        header('Location: /login.php');
        exit;
    }
}

/**
 * Para endpoints JSON: responde 401 si no hay sesión.
 */
function requerirAuthApi(): int
{
    iniciarSesionSegura();
    if (empty($_SESSION['usuario_id'])) {
        jsonError('No autenticado', 401);
    }
    return $_SESSION['usuario_id'];
}

function cerrarSesion(): void
{
    iniciarSesionSegura();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
