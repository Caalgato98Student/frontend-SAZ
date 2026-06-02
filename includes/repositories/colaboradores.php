<?php
/**
 * Colaboradores activos con sus redes sociales.
 * Cada elemento tiene: id, nombre, profesion, imagen, redes[].
 * redes[] = [['nombre' => 'LinkedIn', 'url' => 'https://...'], ...]
 */

require_once __DIR__ . '/../db.php';

function get_colaboradores_activos(): array
{
    $pdo = get_pdo();

    $colaboradores = $pdo->query(
        "SELECT id, nombre, profesion, imagen
         FROM colaboradores
         WHERE activo = 1
         ORDER BY orden, nombre"
    )->fetchAll();

    if (empty($colaboradores)) return [];

    $ids          = array_column($colaboradores, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmtRedes = $pdo->prepare(
        "SELECT colaborador_id, nombre, url
         FROM colaborador_redes
         WHERE colaborador_id IN ($placeholders)
         ORDER BY colaborador_id, orden"
    );
    $stmtRedes->execute($ids);

    $redesMap = [];
    foreach ($stmtRedes->fetchAll() as $red) {
        $redesMap[$red['colaborador_id']][] = [
            'nombre' => $red['nombre'],
            'url'    => $red['url'],
        ];
    }

    foreach ($colaboradores as &$col) {
        $col['redes'] = $redesMap[$col['id']] ?? [];
    }

    return $colaboradores;
}

/**
 * Todas las redes de un colaborador, para el formulario de edición del panel.
 */
function get_redes_colaborador(int $colaboradorId): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT id, nombre, url, orden FROM colaborador_redes
         WHERE colaborador_id = ? ORDER BY orden"
    );
    $stmt->execute([$colaboradorId]);
    return $stmt->fetchAll();
}