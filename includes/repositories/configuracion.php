<?php

function get_config(string $clave): ?string
{
    $pdo       = get_pdo();
    $stmt      = $pdo->prepare('SELECT valor FROM configuracion WHERE clave = ?');
    $stmt->execute([$clave]);
    $resultado = $stmt->fetchColumn();
    return $resultado !== false ? $resultado : null;
}

function set_config(string $clave, string $valor): void
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        'INSERT INTO configuracion (clave, valor)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE valor = VALUES(valor)'
    );
    $stmt->execute([$clave, $valor]);
}
