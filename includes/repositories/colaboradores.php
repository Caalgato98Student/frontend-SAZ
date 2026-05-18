<?php

/**
 * Colaboradores activos ordenados por el campo orden.
 * Retorna estructura compatible con el HTML actual:
 *   cada elemento tiene 'nombre', 'profesion', 'red_nombre', 'red' (alias de url_red), 'imagen'
 */
function get_colaboradores_activos(): array
{
    $pdo = get_pdo();
    $stmt = $pdo->query(
        "SELECT nombre, profesion, red_nombre,
                url_red AS red,
                imagen
         FROM colaboradores
         WHERE activo = 1
         ORDER BY orden, nombre"
    );
    return $stmt->fetchAll();
}
