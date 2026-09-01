<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../functions/rangos.php';
require_once __DIR__ . '/../backend/usuarios/UsuarioRepository.php';

requerirAuthPagina();

$id = validarId($_GET['id'] ?? null);
if ($id === null) {
    http_response_code(404);
    die('Perfil no encontrado');
}

$repo = new UsuarioRepository();
$usuario = $repo->buscarPorId($id);

if (!$usuario) {
    http_response_code(404);
    die('Perfil no encontrado');
}

$rango = obtenerRango((int) $usuario['dias']);
$progreso = min(100, (int) round(((int) $usuario['dias'] / 30) * 100));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title><?= h($usuario['nombre']) ?> — Septiembre Sin Fap</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
</head>
<body>

<header class="app-header">
    <div class="brand compact-brand">
        <img src="logo.png" alt="Logo Septiembre Sin Fap" class="brand-logo brand-logo-sm">
        <span>Septiembre <span>Sin Fap</span></span>
    </div>
    <span class="day-pill"><?= $usuario['activo'] ? 'Activo' : 'Fuera del reto' ?></span>
</header>

<section class="hero-racha">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div class="etiqueta-dias"><?= h($usuario['nombre']) ?></div>
            <div class="numero-dias"><?= h((string) $usuario['dias']) ?></div>
            <div class="etiqueta-dias">días sobrevividos</div>
        </div>
        <span class="badge-rango"><?= h($rango['icono']) ?> <?= h($rango['nombre']) ?></span>
    </div>

    <div class="progress mt-3" style="height:8px; background:var(--surface-2);">
        <div class="progress-bar" role="progressbar" style="width: <?= $progreso ?>%; background: var(--ember);"></div>
    </div>
    <div class="text-muted-app small mt-2">
        <?= $progreso ?>% del reto completado · en el reto desde
        <?= h(date('d/m/Y', strtotime($usuario['fecha_registro']))) ?>
    </div>
</section>

<section class="px-3">
    <div class="card-app mb-4">
        <div class="mb-2 fw-semibold" style="font-size:0.9rem;">Calendario de septiembre</div>
        <div id="gridContribuciones" class="grid-contribuciones">
            <!-- se llena por JS -->
        </div>
        <div class="d-flex gap-3 mt-3 text-muted-app" style="font-size:0.75rem;">
            <span>🟩 Sobrevivió</span>
            <span>🟥 Falló</span>
            <span>⬜ Sin reporte</span>
        </div>
    </div>
</section>

<script>window.PERFIL_USUARIO_ID = <?= (int) $usuario['id'] ?>;</script>
<script src="js/perfil.js"></script>
</body>
</html>
