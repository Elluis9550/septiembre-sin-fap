<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../backend/usuarios/UsuarioRepository.php';

requerirMetodo('GET');
requerirAuthApi();

$id = validarId($_GET['id'] ?? null);
if ($id === null) {
    jsonError('ID inválido', 422);
}

$repo = new UsuarioRepository();
$usuario = $repo->buscarPorId($id);

if (!$usuario) {
    jsonError('Usuario no encontrado', 404);
}

jsonResponse(['ok' => true, 'usuario' => $repo->conRango($usuario)]);
