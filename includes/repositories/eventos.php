<?php

function get_eventos_activos(): array
{
    $pdo = get_pdo();
    $sql = "SELECT * FROM eventos WHERE activo = 1 ORDER BY orden, titulo";
    return $pdo->query($sql)->fetchAll();
}

function get_evento_por_slug(string $slug): ?array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM eventos WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $evento = $stmt->fetch();
    return $evento ?: null;
}

function get_ediciones(int $eventoId): array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT * FROM evento_ediciones WHERE evento_id = ? ORDER BY anio DESC");
    $stmt->execute([$eventoId]);
    return $stmt->fetchAll();
}

function get_imagenes_edicion(int $edicionId): array
{
    $pdo = get_pdo();
    $stmt = $pdo->prepare("SELECT ruta AS archivo, alt_texto AS label, orden FROM evento_edicion_imagenes WHERE edicion_id = ? ORDER BY orden");
    $stmt->execute([$edicionId]);
    return $stmt->fetchAll();
}

function get_evento_completo(string $slug): ?array
{
    $evento = get_evento_por_slug($slug);
    if (!$evento) return null;
    $ediciones = get_ediciones($evento['id']);
    foreach ($ediciones as &$ed) {
        $ed['imagenes'] = get_imagenes_edicion($ed['id']);
        $ed['fecha'] = formatear_fecha_edicion($ed['fecha_inicio'], $ed['fecha_fin']);
    }
    $evento['ediciones'] = $ediciones;
    return $evento;
}

function formatear_fecha_edicion(?string $inicio, ?string $fin): string
{
    if (!$inicio && !$fin) {
        return "Fecha por confirmar";
    }

    $meses = ['01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];

    if ($inicio && !$fin) {
        $ts = strtotime($inicio);
        $dia = date('j', $ts);
        $mes = $meses[date('m', $ts)];
        $anio = date('Y', $ts);
        return "$dia de $mes de $anio";
    }

    $tsInicio = strtotime($inicio);
    $tsFin = strtotime($fin);
    $diaInicio = date('j', $tsInicio);
    $mesInicio = $meses[date('m', $tsInicio)];
    $anioInicio = date('Y', $tsInicio);

    $diaFin = date('j', $tsFin);
    $mesFin = $meses[date('m', $tsFin)];
    $anioFin = date('Y', $tsFin);

    if ($anioInicio !== $anioFin) {
        return "Del $diaInicio de $mesInicio de $anioInicio al $diaFin de $mesFin de $anioFin";
    }
    if ($mesInicio !== $mesFin) {
        return "Del $diaInicio de $mesInicio al $diaFin de $mesFin de $anioFin";
    }
    return "Del $diaInicio al $diaFin de $mesFin de $anioFin";
}
