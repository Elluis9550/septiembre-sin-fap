<?php
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/auth.php';

cerrarSesion();
jsonResponse(['ok' => true]);
