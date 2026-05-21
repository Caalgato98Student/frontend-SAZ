<?php
/**
 * includes/repositories/noticias.php
 * Repositorio de noticias — Fase 3.
 * Reemplaza la lógica de glob() + json_decode() para noticias.
 * Dependencias: includes/db.php, config.php, includes/repositories/configuracion.php
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/configuracion.php';

/**
 * Noticias para la portada (home).
 * Fijadas primero, luego por fecha. Límite desde configuracion.
 *
 * @return array  Lista de noticias como arrays asociativos
 */
function get_noticias_home(): array
{
    $pdo = get_pdo();

    // Obtener límite desde configuración (default: 5)
    $limite = (int) (get_config('noticias_home_limite') ?? 5);

    $sql = "SELECT n.*, n.slug AS id, c.nombre AS categoria
            FROM noticias n
            LEFT JOIN categorias_noticias c ON n.categoria_id = c.id
            WHERE n.estado = 'publicado' AND n.visible_en_principal = 1
            ORDER BY n.fijado DESC, n.fecha DESC
            LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limite]);
    return $stmt->fetchAll();
}

/**
 * Noticias para el archivo (listado completo paginado).
 *
 * @param  int $pagina    Página actual (1-indexed)
 * @param  int $porPagina Cantidad por página
 * @return array ['noticias' => [...], 'total' => int]
 */
function get_noticias_archivo(int $pagina, int $porPagina = 9): array
{
    $pdo    = get_pdo();
    $offset = ($pagina - 1) * $porPagina;

    // Total de noticias publicadas
    $stmtTotal = $pdo->prepare(
        "SELECT COUNT(*) FROM noticias WHERE estado = 'publicado'"
    );
    $stmtTotal->execute();
    $total = (int) $stmtTotal->fetchColumn();

    // Noticias de esta página
    $sql = "SELECT n.*, n.slug AS id, c.nombre AS categoria
            FROM noticias n
            LEFT JOIN categorias_noticias c ON n.categoria_id = c.id
            WHERE n.estado = 'publicado'
            ORDER BY n.fecha DESC
            LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$porPagina, $offset]);
    $noticias = $stmt->fetchAll();

    return ['noticias' => $noticias, 'total' => $total];
}

/**
 * Busca una noticia por slug.
 *
 * @param  string $slug  Identificador URL de la noticia
 * @return array|null    Datos de la noticia o null si no existe
 */
function get_noticia_por_slug(string $slug): ?array
{
    $pdo = get_pdo();
    $sql = "SELECT n.*, n.slug AS id, c.nombre AS categoria
            FROM noticias n
            LEFT JOIN categorias_noticias c ON n.categoria_id = c.id
            WHERE n.slug = ?
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$slug]);
    $noticia = $stmt->fetch();
    return $noticia ?: null;
}
