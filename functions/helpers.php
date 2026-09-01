<?php

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError(string $mensaje, int $status = 400): void
{
    jsonResponse(['ok' => false, 'error' => $mensaje], $status);
}

/**
 * Sanitiza texto simple para salida en HTML (previene XSS).
 */
function h(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Valida que un valor sea un entero positivo (para IDs).
 */
function validarId($valor): ?int
{
    $id = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : $id;
}

/**
 * Lee y decodifica el cuerpo JSON de la petición actual.
 */
function leerJsonBody(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function metodoEsPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function requerirMetodo(string $metodo): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $metodo) {
        jsonError('Método no permitido', 405);
    }
}
