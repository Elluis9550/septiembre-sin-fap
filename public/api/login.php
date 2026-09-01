<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/auth.php';

requerirMetodo('POST');

$body = leerJsonBody();
$username = trim((string) ($body['username'] ?? ''));
$password = (string) ($body['password'] ?? '');

if ($username === '' || $password === '') {
    jsonError('Usuario y contraseña son obligatorios', 422);
}

$usuario = intentarLogin($username, $password);

if (!$usuario) {
    // Mensaje genérico: no revelamos si el error fue el usuario o la contraseña
    jsonError('Usuario o contraseña incorrectos', 401);
}

crearSesionUsuario($usuario);

jsonResponse([
    'ok' => true,
    'usuario' => [
        'id'     => $usuario['id'],
        'nombre' => $usuario['nombre'],
    ],
]);
