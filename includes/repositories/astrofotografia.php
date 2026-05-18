<?php
/**
 * includes/repositories/astrofotografia.php
 * Repositorio de astrofotografía — Fase 3.
 * Reemplaza la categorización por prefijo de archivo (sol-*.json, luna-*.json, profundo-*.json)
 * con queries filtradas por la columna `categoria` ENUM de la DB.
 * Dependencias: includes/db.php, config.php
 */

require_once __DIR__ . '/../db.php';

/**
 * Reconstruye los arrays `hardware` y `parametros` a partir de columnas individuales de la DB.
 * Necesario para que el HTML del lightbox siga funcionando sin cambios.
 *
 * @param  array $foto  Fila tal como viene de PDO fetchAll/fetch
 * @return array        Foto enriquecida con keys 'hardware' y 'parametros'
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

/**
 * Fotos para la portada. Las más recientes visibles.
 * Si hay fotos destacadas, prioriza esas.
 *
 * @param  int $limite  Número máximo de fotos a retornar
 * @return array
 */
function get_astrofotos_home(int $limite = 3): array
{
    $pdo  = get_pdo();
    $sql  = "SELECT *, slug AS id
             FROM astrofotografia
             WHERE visible = 1
             ORDER BY destacada DESC, fecha DESC
             LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limite]);
    return array_map('enriquecer_astrofoto', $stmt->fetchAll());
}

/**
 * Fotos visibles de una categoría específica.
 *
 * @param  string $categoria  'sol', 'luna' o 'espacio_profundo' (valor ENUM de la DB)
 * @return array
 */
function get_astrofotos_por_categoria(string $categoria): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT *, slug AS id
         FROM astrofotografia
         WHERE visible = 1 AND categoria = ?
         ORDER BY fecha DESC"
    );
    $stmt->execute([$categoria]);
    return array_map('enriquecer_astrofoto', $stmt->fetchAll());
}

/**
 * Todas las fotos visibles sin filtro de categoría.
 * Usada en la galería cuando se muestra la vista "Todas".
 *
 * @return array
 */
function get_todas_astrofotos(): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT *, slug AS id
         FROM astrofotografia
         WHERE visible = 1
         ORDER BY destacada DESC, fecha DESC"
    );
    $stmt->execute();
    return array_map('enriquecer_astrofoto', $stmt->fetchAll());
}

/**
 * Busca una astrofoto por slug.
 *
 * @param  string $slug
 * @return array|null
 */
function get_astrofoto_por_slug(string $slug): ?array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT *, slug AS id FROM astrofotografia WHERE slug = ? LIMIT 1"
    );
    $stmt->execute([$slug]);
    $foto = $stmt->fetch();
    return $foto ? enriquecer_astrofoto($foto) : null;
}
