<?php
/**
 * includes/repositories/eventos.php
 * Repositorio de eventos — Fase 3.
 * Reemplaza la lógica de glob() + json_decode() para eventos.
 * Dependencias: includes/db.php, config.php
 */

require_once __DIR__ . '/../db.php';

/**
 * Devuelve todos los eventos activos ordenados por campo orden.
 *
 * @return array
 */
function get_eventos_activos(): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->query(
        "SELECT *, slug AS id FROM eventos WHERE activo = 1 ORDER BY orden, titulo"
    );
    return $stmt->fetchAll();
}

/**
 * Busca un evento por slug.
 *
 * @param  string $slug
 * @return array|null
 */
function get_evento_por_slug(string $slug): ?array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT *, slug AS id FROM eventos WHERE slug = ? LIMIT 1"
    );
    $stmt->execute([$slug]);
    $evento = $stmt->fetch();
    return $evento ?: null;
}

/**
 * Devuelve las ediciones de un evento ordenadas por año descendente.
 * Genera la cadena de texto de fecha a partir de fecha_inicio / fecha_fin.
 *
 * @param  int $eventoId
 * @return array
 */
function get_ediciones(int $eventoId): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT * FROM evento_ediciones
         WHERE evento_id = ?
         ORDER BY anio DESC"
    );
    $stmt->execute([$eventoId]);
    $ediciones = $stmt->fetchAll();

    foreach ($ediciones as &$ed) {
        // Generar texto de fecha a partir de campos tipados
        $ed['fecha'] = _formatear_fecha_edicion($ed['fecha_inicio'], $ed['fecha_fin']);
        // Cargar imágenes de cada edición
        $ed['imagenes'] = get_imagenes_edicion((int) $ed['id']);
    }
    return $ediciones;
}

/**
 * Devuelve las imágenes de una edición ordenadas por campo orden.
 *
 * @param  int $edicionId
 * @return array
 */
function get_imagenes_edicion(int $edicionId): array
{
    $pdo  = get_pdo();
    $stmt = $pdo->prepare(
        "SELECT ruta AS archivo, alt_texto AS label, orden
         FROM edicion_imagenes
         WHERE edicion_id = ?
         ORDER BY orden"
    );
    $stmt->execute([$edicionId]);
    return $stmt->fetchAll();
}

/**
 * Carga un evento completo con todas sus ediciones e imágenes.
 * Función principal que usa templates/evento.php.
 *
 * @param  string $slug
 * @return array|null
 */
function get_evento_completo(string $slug): ?array
{
    $evento = get_evento_por_slug($slug);
    if (!$evento) return null;

    $evento['ediciones'] = get_ediciones((int) $evento['id_num'] ?? (int) $evento['id']);

    // Resolver el ID numérico real (no el alias slug AS id)
    $pdo  = get_pdo();
    $stmt = $pdo->prepare("SELECT id FROM eventos WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $row  = $stmt->fetch();
    if ($row) {
        $evento['ediciones'] = get_ediciones((int) $row['id']);
    }

    return $evento;
}

/**
 * Genera la cadena de texto de fecha para una edición.
 * Ejemplos: "21 de abril de 2025", "Del 21 al 25 de abril de 2025"
 *
 * @param  string|null $inicio  DATE string "YYYY-MM-DD"
 * @param  string|null $fin     DATE string "YYYY-MM-DD"
 * @return string
 */
function _formatear_fecha_edicion(?string $inicio, ?string $fin): string
{
    if (!$inicio) return '';

    $meses = [
        1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',5=>'mayo',6=>'junio',
        7=>'julio',8=>'agosto',9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre'
    ];

    $di = new DateTime($inicio);
    $mesNombre = $meses[(int)$di->format('n')];
    $anio      = $di->format('Y');
    $diaI      = (int)$di->format('j');

    if (!$fin || $fin === $inicio) {
        return "{$diaI} de {$mesNombre} de {$anio}";
    }

    $df   = new DateTime($fin);
    $diaF = (int)$df->format('j');

    // Mismo mes
    if ($di->format('Y-m') === $df->format('Y-m')) {
        return "Del {$diaI} al {$diaF} de {$mesNombre} de {$anio}";
    }

    $mesNombreF = $meses[(int)$df->format('n')];
    $anioF      = $df->format('Y');
    return "{$diaI} de {$mesNombre} al {$diaF} de {$mesNombreF} de {$anioF}";
}
