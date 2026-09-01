<?php
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/auth.php';

cerrarSesion();
header('Location: /login.php');
exit;
