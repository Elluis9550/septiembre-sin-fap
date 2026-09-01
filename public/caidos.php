<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/helpers.php';

requerirAuthPagina();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Salón de los caídos — Septiembre Sin Fap</title>
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
    <a href="dashboard.php" class="text-muted-app" style="text-decoration:none;">← Volver</a>
</header>

<main class="caidos-shell">
    <div class="card-app mb-3">
        <div class="display" style="font-size:1.1rem; color:var(--text);">Los que ya no siguen</div>
    </div>
    <div id="listaCaidos"></div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/caidos.js"></script>
</body>
</html>
