<?php
/**
 * includes/repositories/astrofotografia.php
 * Repositorio de astrofotografía.
 * Dependencias: includes/db.php, config.php
 */

require_once __DIR__ . '/../db.php';

/**
 * Reconstruye los arrays `hardware` y `parametros` a partir de columnas individuales.
 * Necesario para que el HTML del lightbox siga funcionando sin cambios.
 */
function enriquecer_astrofoto(array $foto): array
{
    $foto['hardware'] = array_filter([
        'telescopio' => $foto['telescopio'] ?? null,
        'montura'    => $foto['montura']    ?? null,
        'camara'     => $foto['camara']     ?? null,
    ]);
    $foto['parametros'] = array_filter([
        'integracion' => $foto['integracion'] ?? null,
        'iso_gain'    => $foto['iso_gain']    ?? null,
        'filtros'     => $foto['filtros']     ?? null,
    ]);
    return $foto;
}

/** SELECT base con JOIN a astrofoto_categorias. */
function _astro_select(): string
{
    return "SELECT a.*, a.slug AS id,
                   c.slug  AS categoria,
                   c.nombre AS categoria_nombre,
                   c.icono  AS categoria_icono,
                   c.color  AS categoria_color
            FROM astrofotografia a
            JOIN astrofoto_categorias c ON c.id = a.categoria_id";
}

/**
 * Fotos para la portada. Prioriza destacadas, luego las más recientes.
 */
function get_astrofotos_home(int $limite = 3): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        _astro_select() . "
        WHERE a.visible = 1
        ORDER BY a.destacada DESC, a.fecha DESC
        LIMIT ?"
    );
    $stmt->execute([$limite]);
    return array_map('enriquecer_astrofoto', $stmt->fetchAll());
}

/**
 * Fotos visibles de una categoría por slug ("sol", "luna", "profundo").
 */
function get_astrofotos_por_categoria(string $slug): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        _astro_select() . "
        WHERE a.visible = 1 AND c.slug = ?
        ORDER BY a.fecha DESC"
    );
    $stmt->execute([$slug]);
    return array_map('enriquecer_astrofoto', $stmt->fetchAll());
}

/**
 * Todas las fotos visibles sin filtro de categoría (vista "Todas").
 */
function get_todas_astrofotos(): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        _astro_select() . "
        WHERE a.visible = 1
        ORDER BY a.destacada DESC, a.fecha DESC"
    );
    $stmt->execute();
    return array_map('enriquecer_astrofoto', $stmt->fetchAll());
}

/**
 * Busca una astrofoto por slug.
 */
function get_astrofoto_por_slug(string $slug): ?array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        _astro_select() . "
        WHERE a.slug = ? LIMIT 1"
    );
    $stmt->execute([$slug]);
    $foto = $stmt->fetch();
    return $foto ? enriquecer_astrofoto($foto) : null;
}

/**
 * Todas las categorías de astrofotografía, para el select del panel admin.
 */
function get_astrofoto_categorias(): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->query("SELECT * FROM astrofoto_categorias ORDER BY nombre");
    return $stmt->fetchAll();
}
