<?php

/** SELECT base con LEFT JOIN a cargos para obtener el nombre del cargo. */
function _miembro_cargo_join(): string
{
    return "LEFT JOIN cargos c ON c.id = m.cargo_id";
}

function get_miembros_activos(): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->query(
        "SELECT m.*, m.slug AS id, c.nombre AS cargo
         FROM miembros m " . _miembro_cargo_join() . "
         WHERE m.activo = 1 ORDER BY m.orden, m.nombre"
    );
    return $stmt->fetchAll();
}

function get_miembro_por_slug(string $slug): ?array
{
    $pdo  = get_pdo();

    $stmt = $pdo->prepare(
        "SELECT m.*, c.nombre AS cargo
         FROM miembros m " . _miembro_cargo_join() . "
         WHERE m.slug = ? LIMIT 1"
    );
    $stmt->execute([$slug]);
    $m = $stmt->fetch();
    if (!$m) return null;

    $idNumerico = $m['id'];

    $stmtF = $pdo->prepare(
        "SELECT descripcion FROM miembro_formacion WHERE miembro_id = ? ORDER BY orden, id"
    );
    $stmtF->execute([$idNumerico]);
    $formacion = $stmtF->fetchAll(PDO::FETCH_COLUMN);

    $stmtD = $pdo->prepare(
        "SELECT descripcion FROM miembro_divulgacion WHERE miembro_id = ? ORDER BY orden, id"
    );
    $stmtD->execute([$idNumerico]);
    $divulgacion = $stmtD->fetchAll(PDO::FETCH_COLUMN);

    // Alias slug AS id para compatibilidad con el HTML que arma el link ?id=slug
    $m['id'] = $m['slug'];

    $m['perfil_detallado'] = [
        'formacion'     => $formacion,
        'divulgacion'   => $divulgacion,
        'generalidades' => $m['generalidades'] ?? '',
    ];

    return $m;
}

function get_miembros_directorio(): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->query(
        "SELECT m.nombre, c.nombre AS cargo, m.especialidad, m.correo
         FROM miembros m " . _miembro_cargo_join() . "
         WHERE m.activo = 1 ORDER BY m.orden, m.nombre"
    );
    return $stmt->fetchAll();
}

function get_mesa_directiva(): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->query(
        "SELECT m.nombre, c.nombre AS cargo, m.especialidad, m.imagen
         FROM miembros m " . _miembro_cargo_join() . "
         WHERE m.activo = 1 AND m.en_mesa_directiva = 1 ORDER BY m.orden"
    );
    return $stmt->fetchAll();
}

/**
 * Todos los cargos disponibles, para el select del panel admin.
 */
function get_cargos(): array
{
    return get_pdo()->query("SELECT * FROM cargos ORDER BY nombre")->fetchAll();
}
