<?php
require_once __DIR__ . '/../functions/auth.php';

iniciarSesionSegura();
header('Location: ' . (usuarioAutenticado() ? '/dashboard.php' : '/login.php'));
exit;
