<?php
require_once __DIR__ . '/../functions/auth.php';

iniciarSesionSegura();
if (usuarioAutenticado()) {
    header('Location: /dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Septiembre Sin Fap — Ingresar</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
</head>
<body>

<div class="login-wrap">
    <div class="login-card">
        <div class="login-brand" aria-label="Septiembre Sin Fap">
            <img src="logo.png" alt="Logo Septiembre Sin Fap" class="brand-logo brand-logo-lg">
        </div>
        <div class="titulo">Septiembre<br><span>Sin Fap</span></div>
        <p class="text-muted-app mb-4">Un mes. Una racha. Sin excusas.</p>

        <div id="mensajeError" class="mensaje-error mb-3 d-none"></div>

        <form id="formLogin" novalidate>
            <div class="mb-3">
                <label class="form-label text-muted-app small">Usuario</label>
                <input type="text" class="form-control form-control-app" id="username" autocomplete="username" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted-app small">Contraseña</label>
                <input type="password" class="form-control form-control-app" id="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-ember w-100" id="btnSubmit">Entrar al reto</button>
        </form>
    </div>
</div>

<script src="js/login.js"></script>
</body>
</html>
