<?php

function get_miembros_activos(): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->query(
        "SELECT *, slug AS id FROM miembros WHERE activo = 1 ORDER BY orden, nombre"
    );
    return $stmt->fetchAll();
}

function get_miembro_por_slug(string $slug): ?array
{
    $pdo  = get_pdo();

    // Traer miembro; 'id' es el PK numérico, 'slug' es el identificador URL
    $stmt = $pdo->prepare("SELECT * FROM miembros WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $m = $stmt->fetch();
    if (!$m) return null;

    $idNumerico = $m['id'];

    $stmtF = $pdo->prepare(
        "SELECT descripcion FROM miembro_formacion WHERE miembro_id = ? ORDER BY id"
    );
    $stmtF->execute([$idNumerico]);
    $formacion = $stmtF->fetchAll(PDO::FETCH_COLUMN);

    $stmtD = $pdo->prepare(
        "SELECT descripcion FROM miembro_divulgacion WHERE miembro_id = ? ORDER BY id"
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
        "SELECT nombre, cargo, especialidad, correo
         FROM miembros WHERE activo = 1 ORDER BY orden, nombre"
    );
    return $stmt->fetchAll();
}

function get_mesa_directiva(): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->query(
        "SELECT nombre, cargo, especialidad, imagen
         FROM miembros WHERE activo = 1 AND en_mesa_directiva = 1 ORDER BY orden"
    );
    return $stmt->fetchAll();
}
