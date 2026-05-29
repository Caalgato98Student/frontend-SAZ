<?php
/**
 * includes/repositories/colaboradores.php
 * Repositorio de colaboradores — Fase 3.
 */

require_once __DIR__ . '/../db.php';

function get_colaboradores_activos(): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->query(
        "SELECT nombre, profesion, red_nombre, url_red AS red, imagen, activo, orden
         FROM colaboradores
         WHERE activo = 1
         ORDER BY orden, nombre"
    );
    return $stmt->fetchAll();
}