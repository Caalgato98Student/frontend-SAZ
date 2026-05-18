<?php

/**
 * Convocatorias para la portada.
 * Publicadas primero, luego cerradas. Ordenadas por fecha.
 */
function get_convocatorias_home(int $limite = 3): array
{
    $pdo = get_pdo();
    $sql = "SELECT *,
                   (estado = 'publicada') AS activa,
                   fecha_publicacion AS fecha,
                   fecha_cierre AS cierre
            FROM convocatorias
            WHERE estado IN ('publicada', 'cerrada')
            ORDER BY (estado = 'publicada') DESC, fecha_publicacion DESC
            LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Todas las convocatorias visibles (publicadas + cerradas).
 * Para la página completa de convocatorias.
 */
function get_convocatorias_publicas(): array
{
    $pdo = get_pdo();
    $sql = "SELECT *,
                   (estado = 'publicada') AS activa,
                   fecha_publicacion AS fecha,
                   fecha_cierre AS cierre
            FROM convocatorias
            WHERE estado IN ('publicada', 'cerrada')
            ORDER BY (estado = 'publicada') DESC, fecha_publicacion DESC";
    return $pdo->query($sql)->fetchAll();
}

function get_convocatoria_por_slug(string $slug): ?array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT *, fecha_publicacion AS fecha, fecha_cierre AS cierre
         FROM convocatorias WHERE slug = ? LIMIT 1"
    );
    $stmt->execute([$slug]);
    $conv = $stmt->fetch();
    return $conv ?: null;
}
