<?php
/**
 * includes/repositories/convocatorias.php
 * Repositorio de convocatorias — Fase 3.
 * Reemplaza la lógica de glob() + json_decode() para convocatorias.
 * Dependencias: includes/db.php, config.php
 */

require_once __DIR__ . '/../db.php';

/**
 * Convocatorias para el home (máx. $limite).
 * Publicadas primero, luego cerradas. Ordenadas por fecha_publicacion DESC.
 *
 * Genera alias de compatibilidad:
 *  - 'activa'  → 1 si estado='publicada', 0 si 'cerrada'
 *  - 'fecha'   → fecha_publicacion
 *  - 'cierre'  → fecha_cierre
 *
 * @param  int $limite
 * @return array
 */
function get_convocatorias_home(int $limite = 3): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT *,
                fecha_publicacion AS fecha,
                fecha_cierre      AS cierre,
                (estado = 'publicada') AS activa
         FROM convocatorias
         WHERE estado IN ('publicada', 'cerrada')
         ORDER BY (estado = 'publicada') DESC, fecha_publicacion DESC
         LIMIT ?"
    );
    $stmt->execute([$limite]);
    return $stmt->fetchAll();
}

/**
 * Todas las convocatorias públicas (publicadas y cerradas).
 * Para la página completa de convocatorias.
 *
 * @return array
 */
function get_convocatorias_publicas(): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->query(
        "SELECT *,
                fecha_publicacion AS fecha,
                fecha_cierre      AS cierre,
                (estado = 'publicada') AS activa
         FROM convocatorias
         WHERE estado IN ('publicada', 'cerrada')
         ORDER BY (estado = 'publicada') DESC, fecha_publicacion DESC"
    );
    return $stmt->fetchAll();
}

/**
 * Busca una convocatoria por slug.
 *
 * @param  string $slug
 * @return array|null
 */
function get_convocatoria_por_slug(string $slug): ?array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT *,
                fecha_publicacion AS fecha,
                fecha_cierre      AS cierre,
                (estado = 'publicada') AS activa
         FROM convocatorias
         WHERE slug = ?
         LIMIT 1"
    );
    $stmt->execute([$slug]);
    $conv = $stmt->fetch();
    return $conv ?: null;
}
