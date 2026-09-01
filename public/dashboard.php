<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../backend/usuarios/UsuarioRepository.php';
require_once __DIR__ . '/../functions/rangos.php';

requerirAuthPagina();

$usuarioId = usuarioIdActual();
$repo = new UsuarioRepository();
$yo = $repo->buscarPorId($usuarioId);
$rangoYo = $yo ? obtenerRango((int) $yo['dias']) : obtenerRango(0);
$totalActivos = count($repo->listarActivos());
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Septiembre Sin Fap — Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
</head>
<body>

<header class="app-header">
    <div class="brand">
        <img src="logo.png" alt="Logo Septiembre Sin Fap" class="brand-logo brand-logo-sm">
        <span>Septiembre <span>Sin Fap</span></span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="day-pill">Día <?= h((string) diaActualDelReto()) ?>/30</span>
        <a href="caidos.php" class="text-muted-app" style="text-decoration:none; font-size:0.8rem;">Caídos</a>
        <a href="api/logout.php" class="text-muted-app" style="text-decoration:none; font-size:0.8rem;">Salir</a>
    </div>
</header>

<section class="hero-racha">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <div class="etiqueta-dias">Tu racha, <?= h($yo['nombre'] ?? '') ?></div>
            <div class="numero-dias"><?= h((string) ($yo['dias'] ?? 0)) ?></div>
            <div class="etiqueta-dias">días sobrevividos</div>
        </div>
        <span class="badge-rango"><?= h($rangoYo['icono']) ?> <?= h($rangoYo['nombre']) ?></span>
    </div>
    <div class="mt-3 text-muted-app small">
        <?= h((string) $totalActivos) ?> participantes siguen activos hoy.
        <?php if (!($yo['activo'] ?? true)): ?>
            <div class="mt-2" style="color:#ff9daa;">Caíste del reto. Puedes ver tu perfil e historial.</div>
        <?php endif; ?>
    </div>
</section>

<div class="px-3 mb-2 d-flex justify-content-between align-items-center">
    <span class="text-muted-app small">Participantes activos</span>
    <select id="selectorOrden" class="form-select form-select-sm form-control-app" style="width:auto;">
        <option value="dias">Ordenar por días</option>
        <option value="nombre">Ordenar por nombre</option>
    </select>
</div>

<main class="lista-participantes" id="listaParticipantes">
    <div class="text-muted-app small">Cargando participantes...</div>
</main>

<!-- Modal de reporte diario -->
<div class="modal fade modal-reporte" id="modalReporte" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title display" style="font-size:1.1rem;">Reporte de hoy</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted-app small mb-4">¿Sobreviviste el día de hoy?</p>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="opcion-reporte" data-valor="sobrevivio">
                            <span class="icono">🟢</span>
                            <div>Sobreviví</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="opcion-reporte" data-valor="falle">
                            <span class="icono">🔴</span>
                            <div>Fallé</div>
                        </div>
                    </div>
                </div>
                <div id="errorReporte" class="mensaje-error mt-3 d-none"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-ember w-100" id="btnConfirmarReporte" disabled>Confirmar</button>
            </div>
        </div>
    </div>
</div>

<button class="btn-reportar-flotante" id="btnAbrirReporte" disabled>Cargando estado del reporte...</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>window.USUARIO_ACTUAL_ID = <?= (int) $usuarioId ?>;</script>
<script src="js/dashboard.js"></script>
</body>
</html>
