<?php
/**
 * includes/repositories/actividades.php
 * Repositorio de actividades — lee de la base de datos.
 *
 * Funciones:
 *   get_actividad_por_slug($slug) — actividad completa con ítems e imágenes
 *   get_actividades_activas()     — todos los tipos activos (para menú)
 */

require_once __DIR__ . '/../db.php';

/**
 * Actividad con ítems e imágenes del carrusel.
 * Retorna estructura compatible con el template:
 *   [
 *     'titulo'      => 'Charlas',
 *     'icono'       => 'bi bi-chat-left-text',
 *     'descripcion' => '...',
 *     'items'       => [['titulo' => '...', 'descripcion' => '...'], ...],
 *     'imagenes'    => ['assets/img/actividades/charlas/foto1.jpg', ...],
 *   ]
 */
function get_actividad_por_slug(string $slug): ?array
{
    $pdo = get_pdo();

    // Actividad base
    $stmt = $pdo->prepare("SELECT * FROM actividades WHERE slug = ? AND activo = 1 LIMIT 1");
    $stmt->execute([$slug]);
    $actividad = $stmt->fetch();
    if (!$actividad) return null;

    // Ítems descriptivos
    $stmtI = $pdo->prepare(
        "SELECT titulo, descripcion FROM actividad_items
         WHERE actividad_id = ? ORDER BY orden, id"
    );
    $stmtI->execute([$actividad['id']]);
    $actividad['items'] = $stmtI->fetchAll();

    // Imágenes del carrusel (solo la ruta, igual que el scandir anterior)
    $stmtImg = $pdo->prepare(
        "SELECT ruta FROM actividad_imagenes
         WHERE actividad_id = ? ORDER BY orden, id"
    );
    $stmtImg->execute([$actividad['id']]);
    $actividad['imagenes'] = $stmtImg->fetchAll(PDO::FETCH_COLUMN);

    return $actividad;
}

/**
 * Todos los tipos de actividad activos, para el menú de navegación.
 */
function get_actividades_activas(): array
{
    $pdo = get_pdo();
    $stmt = $pdo->query(
        "SELECT id, slug, titulo, icono FROM actividades WHERE activo = 1 ORDER BY orden, titulo"
    );
    return $stmt->fetchAll();
}
