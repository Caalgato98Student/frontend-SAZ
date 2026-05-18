<?php
/**
 * includes/repositories/observaciones.php
 * Repositorio para tipos de sesión de observación (diurna, nocturna, solar).
 * Reemplaza el HTML estático de las páginas de observaciones.
 *
 * Dependencias: includes/db.php, config.php
 */

/**
 * Tipo de observación con sus tarjetas de contenido.
 * Retorna estructura para las páginas de observaciones:
 *   [
 *     'titulo'           => 'Observación Nocturna',
 *     'icono'            => 'bi bi-moon-stars-fill',
 *     'descripcion_intro'=> '...',
 *     'recomendaciones'  => '<ul>...</ul>',   // HTML de TinyMCE, puede ser null
 *     'items'            => [
 *       ['titulo' => 'Planetas', 'icono' => 'bi bi-globe2 me-2 text-warning', 'descripcion' => '...'],
 *       ...
 *     ],
 *   ]
 */
function get_observacion_por_slug(string $slug): ?array
{
    $pdo = get_pdo();

    // Observación base
    $stmt = $pdo->prepare("SELECT * FROM observaciones WHERE slug = ? AND activo = 1 LIMIT 1");
    $stmt->execute([$slug]);
    $observacion = $stmt->fetch();
    if (!$observacion) return null;

    // Ítems con su ícono propio
    $stmtI = $pdo->prepare(
        "SELECT titulo, icono, descripcion FROM observacion_items
         WHERE observacion_id = ? ORDER BY orden, id"
    );
    $stmtI->execute([$observacion['id']]);
    $observacion['items'] = $stmtI->fetchAll();

    return $observacion;
}

/**
 * Todos los tipos de observación activos, para el menú de navegación.
 */
function get_observaciones_activas(): array
{
    $pdo = get_pdo();
    $stmt = $pdo->query(
        "SELECT id, slug, titulo, icono FROM observaciones WHERE activo = 1 ORDER BY orden, titulo"
    );
    return $stmt->fetchAll();
}
