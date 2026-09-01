<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../backend/usuarios/UsuarioRepository.php';

requerirMetodo('GET');
requerirAuthApi();

$orden = $_GET['orden'] ?? 'dias';

$repo = new UsuarioRepository();
$usuarios = array_map([$repo, 'conRango'], $repo->listarActivos((string) $orden));

jsonResponse(['ok' => true, 'usuarios' => $usuarios]);
