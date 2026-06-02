<?php
/**
 * scripts/migrate_json_to_db.php
 *
 * Script único de seed/migración. Puebla la base de datos con:
 *   - Actividades + ítems (hardcodeados aquí)
 *   - Imágenes de actividades (escaneadas de assets/img/actividades/)
 *   - Observaciones + ítems (hardcodeados aquí)
 *   - Categorías de noticias, noticias, eventos, convocatorias,
 *     astrofotografía y miembros (leídos desde content/)
 *   - Usuario admin de desarrollo
 *
 * REQUISITOS PREVIOS:
 *   1. XAMPP con MySQL corriendo.
 *   2. Haber importado scripts/schema.sql en la base "saz_cms".
 *   3. Tener config.php configurado.
 *
 * USO:
 *   php scripts/migrate_json_to_db.php
 *
 * IDEMPOTENTE: usa INSERT IGNORE / slugExiste(). Se puede ejecutar
 * varias veces sin duplicar datos.
 */

$rootDir = dirname(__DIR__);
require_once $rootDir . '/config.php';
require_once $rootDir . '/includes/db.php';

$pdo = get_pdo();

$stats = [
    'actividades'    => 0,
    'act_items'      => 0,
    'act_imagenes'   => 0,
    'observaciones'  => 0,
    'obs_items'      => 0,
    'categorias'     => 0,
    'noticias'       => 0,
    'eventos'        => 0,
    'ediciones'      => 0,
    'edicion_imgs'   => 0,
    'convocatorias'  => 0,
    'astrofotografia'=> 0,
    'miembros'       => 0,
    'formacion'      => 0,
    'divulgacion'    => 0,
    'colaboradores'  => 0,
    'col_redes'      => 0,
    'admin'          => 0,
    'saltados'       => 0,
];

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  SAZ CMS — Seed / Migración                        ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

// ── Helpers ────────────────────────────────────────────────────

function leerJsonDir(string $dir): array
{
    $items = [];
    if (!is_dir($dir)) {
        echo "  ⚠ Directorio no encontrado: $dir\n";
        return $items;
    }
    foreach (glob($dir . '*.json') as $archivo) {
        $datos = json_decode(file_get_contents($archivo), true);
        if ($datos) {
            if (empty($datos['id'])) {
                $datos['id'] = basename($archivo, '.json');
            }
            $datos['_filename'] = basename($archivo, '.json');
            $items[] = $datos;
        } else {
            echo "  ⚠ JSON inválido: $archivo\n";
        }
    }
    return $items;
}

function slugExiste(PDO $pdo, string $tabla, string $slug): bool
{
    $stmt = $pdo->prepare("SELECT 1 FROM `$tabla` WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    return (bool) $stmt->fetchColumn();
}

function parsearFecha(string $fechaRaw): array
{
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaRaw)) {
        return [$fechaRaw, null];
    }
    return [null, null];
}

function detectarCategoriaSlug(string $filename): string
{
    if (str_starts_with($filename, 'sol'))      return 'sol';
    if (str_starts_with($filename, 'luna'))     return 'luna';
    if (str_starts_with($filename, 'profundo')) return 'profundo';
    return 'profundo';
}


// ════════════════════════════════════════════════════════════════
// 1. ACTIVIDADES + ÍTEMS
// ════════════════════════════════════════════════════════════════
echo "1. Actividades\n";

$actividades = [
    [
        'slug'        => 'charlas',
        'titulo'      => 'Charlas',
        'icono'       => 'bi bi-chat-left-text',
        'descripcion' => 'Sesiones informales de conversacion e intercambio de conocimientos entre miembros de la SAZ y el publico interesado.',
        'orden'       => 1,
        'items' => [
            ['Charlas de cafe astronomico', 'Reuniones informales donde se discuten noticias recientes de astronomia en un ambiente relajado.', 1],
            ['Noches de preguntas', 'Sesiones abiertas donde el publico puede hacer preguntas a los miembros sobre cualquier tema astronomico.', 2],
            ['Charlas para escuelas', 'Presentaciones adaptadas para estudiantes de primaria, secundaria y bachillerato.', 3],
            ['Charlas con invitados', 'Sesiones especiales con astronomos y cientificos invitados de otras instituciones.', 4],
        ],
    ],
    [
        'slug'        => 'conferencias',
        'titulo'      => 'Conferencias',
        'icono'       => 'bi bi-easel',
        'descripcion' => 'Sesiones magistrales impartidas por especialistas en astronomia, astrofisica y ciencias afines. Abiertas al publico general.',
        'orden'       => 2,
        'items' => [
            ['Conferencias de divulgacion', 'Charlas accesibles para publico no especializado sobre temas de actualidad en astronomia y ciencias del espacio.', 1],
            ['Conferencias especializadas', 'Presentaciones tecnicas dirigidas a estudiantes y profesionales del area con temas de investigacion avanzada.', 2],
            ['Ciclos tematicos', 'Series de conferencias organizadas alrededor de un tema central, como cosmologia, exoplanetas o historia de la astronomia.', 3],
            ['Conferencias virtuales', 'Transmisiones en linea que permiten la participacion de publico a nivel nacional e internacional.', 4],
        ],
    ],
    [
        'slug'        => 'cursos',
        'titulo'      => 'Cursos',
        'icono'       => 'bi bi-book',
        'descripcion' => 'Programas de formacion estructurados con duracion de varias semanas, dirigidos a diferentes niveles de conocimiento.',
        'orden'       => 3,
        'items' => [
            ['Astronomia basica', 'Curso introductorio de 8 semanas que cubre el sistema solar, estrellas, galaxias y cosmologia.', 1],
            ['Mecanica celeste', 'Estudio del movimiento de cuerpos celestes. Requiere conocimientos basicos de fisica y matematicas.', 2],
            ['Astrofotografia intermedia', 'Tecnicas avanzadas de captura y procesamiento de imagen astronomica con equipo dedicado.', 3],
            ['Historia de la astronomia', 'Recorrido historico desde las civilizaciones antiguas hasta los descubrimientos mas recientes.', 4],
        ],
    ],
    [
        'slug'        => 'diplomados',
        'titulo'      => 'Diplomados',
        'icono'       => 'bi bi-mortarboard',
        'descripcion' => 'Programas academicos de mayor profundidad y duracion, con reconocimiento institucional, dirigidos a profesionales y docentes.',
        'orden'       => 4,
        'items' => [
            ['Diplomado en astronomia general', 'Programa de 6 meses que abarca fundamentos de astrofisica, instrumentacion y divulgacion cientifica.', 1],
            ['Diplomado en didactica de la astronomia', 'Orientado a docentes. Estrategias y recursos para ensenar astronomia en el aula.', 2],
            ['Certificacion en observacion astronomica', 'Programa intensivo con evaluacion practica en manejo de telescopios y tecnicas observacionales.', 3],
            ['Proximos diplomados', 'La SAZ esta desarrollando nuevos programas en colaboracion con universidades de la region. Consulta esta pagina para actualizaciones.', 4],
        ],
    ],
    [
        'slug'        => 'talleres',
        'titulo'      => 'Talleres',
        'icono'       => 'bi bi-tools',
        'descripcion' => 'Actividades practicas orientadas a desarrollar habilidades en observacion, instrumentacion y procesamiento de datos astronomicos.',
        'orden'       => 5,
        'items' => [
            ['Construccion de telescopios', 'Taller practico para construir un telescopio refractor basico con materiales accesibles.', 1],
            ['Fotografia nocturna', 'Introduccion a la captura de imagenes del cielo con camaras DSLR y smartphones.', 2],
            ['Procesamiento de imagenes', 'Uso de software libre para apilar y procesar fotografias astronomicas.', 3],
            ['Orientacion celeste', 'Aprende a localizar constelaciones, planetas y objetos de cielo profundo con y sin telescopio.', 4],
        ],
    ],
];

$stmtActIns = $pdo->prepare(
    "INSERT IGNORE INTO actividades (slug, titulo, icono, descripcion, activo, orden)
     VALUES (?, ?, ?, ?, 1, ?)"
);
$stmtActItem = $pdo->prepare(
    "INSERT INTO actividad_items (actividad_id, titulo, descripcion, orden)
     VALUES (?, ?, ?, ?)"
);

foreach ($actividades as $act) {
    $stmtActIns->execute([$act['slug'], $act['titulo'], $act['icono'], $act['descripcion'], $act['orden']]);
    if ($stmtActIns->rowCount() > 0) {
        $actId = (int) $pdo->lastInsertId();
        $stats['actividades']++;
        echo "  ✅ {$act['titulo']} (id=$actId)\n";
        foreach ($act['items'] as [$titulo, $desc, $orden]) {
            $stmtActItem->execute([$actId, $titulo, $desc, $orden]);
            $stats['act_items']++;
        }
    } else {
        echo "  ⏭ Ya existe: {$act['slug']}\n";
        $stats['saltados']++;
    }
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// 2. IMÁGENES DE ACTIVIDADES (escanea filesystem)
// ════════════════════════════════════════════════════════════════
echo "2. Imágenes de actividades\n";

$actividadesDB = $pdo->query(
    "SELECT id, slug, titulo FROM actividades WHERE activo = 1 ORDER BY orden"
)->fetchAll();

$allowedExt  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$stmtImgChk  = $pdo->prepare("SELECT id FROM actividad_imagenes WHERE actividad_id = ? AND ruta = ? LIMIT 1");
$stmtImgIns  = $pdo->prepare(
    "INSERT INTO actividad_imagenes (actividad_id, ruta, alt_texto, orden)
     VALUES (?, ?, ?, ?)"
);

foreach ($actividadesDB as $act) {
    $imgDir = $rootDir . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR
            . 'img' . DIRECTORY_SEPARATOR . 'actividades' . DIRECTORY_SEPARATOR . $act['slug'];

    if (!is_dir($imgDir)) {
        echo "  ⏭ Sin directorio: assets/img/actividades/{$act['slug']}/\n";
        continue;
    }

    $archivos = array_filter(scandir($imgDir), fn($f) => in_array(
        strtolower(pathinfo($f, PATHINFO_EXTENSION)), $allowedExt
    ));
    sort($archivos);

    if (empty($archivos)) {
        echo "  ⏭ Sin imágenes: {$act['slug']}\n";
        continue;
    }

    echo "  📁 {$act['titulo']}\n";
    $orden = 1;
    foreach ($archivos as $file) {
        $ruta = 'assets/img/actividades/' . $act['slug'] . '/' . $file;
        $stmtImgChk->execute([$act['id'], $ruta]);
        if ($stmtImgChk->fetchColumn()) {
            $stats['saltados']++;
        } else {
            $stmtImgIns->execute([
                $act['id'],
                $ruta,
                $act['titulo'] . ' — Sociedad Astronómica de Zacatecas',
                $orden,
            ]);
            $stats['act_imagenes']++;
            echo "     + $file\n";
        }
        $orden++;
    }
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// 3. OBSERVACIONES + ÍTEMS
// ════════════════════════════════════════════════════════════════
echo "3. Observaciones\n";

$observacionesData = [
    [
        'slug'             => 'diurna',
        'titulo'           => 'Observacion Diurna',
        'icono'            => 'bi bi-brightness-high text-info',
        'descripcion_intro'=> 'Sesiones educativas para publico general donde se exploran fenomenos astronomicos visibles durante el dia.',
        'recomendaciones'  => null,
        'orden'            => 1,
        'items' => [
            ['La Luna de dia',      'bi bi-moon-stars me-2',   'Observacion de la Luna cuando es visible durante el dia. Se explican las fases lunares y la mecanica orbital.', 1],
            ['Planetas visibles',   'bi bi-globe me-2',        'En condiciones favorables, Venus puede observarse a plena luz del dia. Se explica por que y como localizarlo.', 2],
            ['Gnomonica',           'bi bi-compass me-2',      'Uso de relojes de sol y proyeccion de sombras para comprender el movimiento aparente del Sol y las estaciones del ano.', 3],
            ['Meteorologia basica', 'bi bi-cloud-sun me-2',    'Introduccion a la atmosfera terrestre: como afecta la observacion astronomica y como interpretar condiciones del cielo.', 4],
        ],
    ],
    [
        'slug'             => 'nocturna',
        'titulo'           => 'Observacion Nocturna',
        'icono'            => 'bi bi-moon-stars-fill text-primary',
        'descripcion_intro'=> 'Sesiones de observacion de planetas, nebulosas, cumulos estelares y galaxias con telescopios de diferentes tipos y aperturas.',
        'recomendaciones'  => '<ul><li>Llegar 15 minutos antes para adaptacion visual a la oscuridad.</li><li>Llevar ropa abrigadora; las noches zacatecanas pueden ser frias.</li><li>Evitar el uso de luces blancas; se recomienda linterna roja.</li><li>No se requiere telescopio propio; la SAZ proporciona equipo.</li></ul>',
        'orden'            => 2,
        'items' => [
            ['Planetas',         'bi bi-globe2 me-2 text-warning',   'Observacion de los planetas visibles: Jupiter con sus lunas galileanas, Saturno y sus anillos, Marte y Venus.', 1],
            ['Cielo profundo',   'bi bi-cloud-haze2 me-2 text-info', 'Nebulosas de emision y reflexion, cumulos abiertos y globulares, galaxias cercanas como Andromeda y el Triangulo.', 2],
            ['Estrellas dobles', 'bi bi-stars me-2 text-warning',    'Sistemas estelares multiples con contrastes de color y brillo. Ideales para telescopios de cualquier apertura.', 3],
        ],
    ],
    [
        'slug'             => 'solar',
        'titulo'           => 'Observacion Solar',
        'icono'            => 'bi bi-sun text-warning',
        'descripcion_intro'=> 'Jornadas de observacion segura del Sol con filtros solares certificados y telescopios especializados.',
        'recomendaciones'  => null,
        'orden'            => 3,
        'items' => [
            ['Seguridad',    'bi bi-shield-check me-2 text-success', 'Todas las observaciones solares se realizan con filtros certificados ISO 12312-2. Nunca se debe observar el Sol directamente sin proteccion adecuada.', 1],
            ['Equipo',       'bi bi-telescope me-2 text-primary',    'Utilizamos telescopios solares con filtro H-alpha que permiten observar protuberancias, manchas solares y filamentos en la cromosfera.', 2],
            ['Programacion', 'bi bi-calendar-event me-2 text-info',  'Las sesiones se realizan generalmente los sabados por la manana en plazas publicas de Zacatecas. Consulta el calendario de eventos para fechas especificas.', 3],
            ['Publico',      'bi bi-people me-2 text-primary',       'Actividad abierta a todas las edades. No se requiere experiencia previa ni inscripcion. Solo acude al punto de observacion en la fecha indicada.', 4],
        ],
    ],
];

$stmtObsIns = $pdo->prepare(
    "INSERT IGNORE INTO observaciones (slug, titulo, icono, descripcion_intro, recomendaciones, activo, orden)
     VALUES (?, ?, ?, ?, ?, 1, ?)"
);
$stmtObsItem = $pdo->prepare(
    "INSERT INTO observacion_items (observacion_id, titulo, icono, descripcion, orden)
     VALUES (?, ?, ?, ?, ?)"
);

foreach ($observacionesData as $obs) {
    $stmtObsIns->execute([
        $obs['slug'], $obs['titulo'], $obs['icono'],
        $obs['descripcion_intro'], $obs['recomendaciones'], $obs['orden'],
    ]);
    if ($stmtObsIns->rowCount() > 0) {
        $obsId = (int) $pdo->lastInsertId();
        $stats['observaciones']++;
        echo "  ✅ {$obs['titulo']}\n";
        foreach ($obs['items'] as [$titulo, $icono, $desc, $orden]) {
            $stmtObsItem->execute([$obsId, $titulo, $icono, $desc, $orden]);
            $stats['obs_items']++;
        }
    } else {
        echo "  ⏭ Ya existe: {$obs['slug']}\n";
        $stats['saltados']++;
    }
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// 4. CATEGORÍAS DE NOTICIAS
// ════════════════════════════════════════════════════════════════
echo "4. Categorías de noticias\n";

$noticias = leerJsonDir($rootDir . '/content/noticias/');
$categoriasUnicas = [];
foreach ($noticias as $n) {
    if (!empty($n['categoria'])) {
        $categoriasUnicas[trim($n['categoria'])] = true;
    }
}

$stmtCatIns = $pdo->prepare("INSERT IGNORE INTO categorias_noticias (nombre, slug) VALUES (?, ?)");
foreach (array_keys($categoriasUnicas) as $cat) {
    $slug = trim(preg_replace('/[^a-z0-9]+/', '-', mb_strtolower($cat)), '-');
    $stmtCatIns->execute([ucfirst($cat), $slug]);
    if ($stmtCatIns->rowCount() > 0) {
        echo "  ✅ $cat\n";
        $stats['categorias']++;
    } else {
        echo "  ⏭ Ya existe: $cat\n";
    }
}

$catMap = [];
foreach ($pdo->query("SELECT id, slug FROM categorias_noticias") as $row) {
    $catMap[$row['slug']] = $row['id'];
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// 5. NOTICIAS
// ════════════════════════════════════════════════════════════════
echo "5. Noticias\n";

$stmtNotIns = $pdo->prepare(
    "INSERT INTO noticias
     (slug, titulo, resumen, contenido, imagen, autor, categoria_id, fecha, estado, visible_en_principal, fijado)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'publicado', 1, 0)"
);

foreach ($noticias as $n) {
    $slug = $n['id'] ?? $n['_filename'];
    if (slugExiste($pdo, 'noticias', $slug)) {
        echo "  ⏭ Ya existe: $slug\n";
        $stats['saltados']++;
        continue;
    }
    $catId = null;
    if (!empty($n['categoria'])) {
        $catSlug = trim(preg_replace('/[^a-z0-9]+/', '-', mb_strtolower(trim($n['categoria']))), '-');
        $catId   = $catMap[$catSlug] ?? null;
    }
    $stmtNotIns->execute([
        $slug,
        $n['titulo']   ?? '',
        $n['resumen']  ?? null,
        $n['contenido'] ?? null,
        $n['imagen']   ?? null,
        $n['autor']    ?? null,
        $catId,
        $n['fecha']    ?? date('Y-m-d'),
    ]);
    echo "  ✅ {$n['titulo']}\n";
    $stats['noticias']++;
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// 6. EVENTOS + EDICIONES + IMÁGENES DE EDICIÓN
// ════════════════════════════════════════════════════════════════
echo "6. Eventos\n";

$eventos   = leerJsonDir($rootDir . '/content/eventos/');
$stmtEvIns = $pdo->prepare(
    "INSERT INTO eventos (slug, titulo, descripcion, imagen_principal, activo, orden)
     VALUES (?, ?, ?, ?, 1, 0)"
);
$stmtEdIns = $pdo->prepare(
    "INSERT INTO evento_ediciones (evento_id, anio, fecha_inicio, fecha_fin, lugar, resumen, imagen, pdf)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmtEdImg = $pdo->prepare(
    "INSERT INTO evento_edicion_imagenes (edicion_id, ruta, alt_texto, orden) VALUES (?, ?, ?, ?)"
);

foreach ($eventos as $ev) {
    $slug = $ev['slug'] ?? $ev['_filename'];
    if (slugExiste($pdo, 'eventos', $slug)) {
        echo "  ⏭ Ya existe: $slug\n";
        $stats['saltados']++;
        continue;
    }
    $stmtEvIns->execute([$slug, $ev['titulo'] ?? '', $ev['descripcion'] ?? null, $ev['imagen_principal'] ?? null]);
    $eventoId = (int) $pdo->lastInsertId();
    echo "  ✅ {$ev['titulo']}\n";
    $stats['eventos']++;

    foreach ($ev['ediciones'] ?? [] as $ed) {
        [$fechaInicio, $fechaFin] = parsearFecha($ed['fecha'] ?? '');
        $stmtEdIns->execute([
            $eventoId, $ed['anio'] ?? date('Y'),
            $fechaInicio, $fechaFin,
            $ed['lugar'] ?? null, $ed['resumen'] ?? null,
            $ed['imagen'] ?? null, $ed['pdf'] ?? null,
        ]);
        $edicionId = (int) $pdo->lastInsertId();
        echo "     ✅ Edición {$ed['anio']}\n";
        $stats['ediciones']++;

        foreach ($ed['imagenes'] ?? $ed['_imagenes'] ?? [] as $idx => $img) {
            $stmtEdImg->execute([$edicionId, $img['archivo'] ?? '', $img['label'] ?? null, $idx]);
            $stats['edicion_imgs']++;
        }
    }
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// 7. CONVOCATORIAS
// ════════════════════════════════════════════════════════════════
echo "7. Convocatorias\n";

$convocatorias = leerJsonDir($rootDir . '/content/convocatorias/');
$stmtConvIns   = $pdo->prepare(
    "INSERT INTO convocatorias (slug, titulo, resumen, contenido, pdf, fecha_publicacion, fecha_cierre, estado)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

foreach ($convocatorias as $c) {
    $slug = $c['id'] ?? $c['_filename'];
    if (slugExiste($pdo, 'convocatorias', $slug)) {
        echo "  ⏭ Ya existe: $slug\n";
        $stats['saltados']++;
        continue;
    }
    $stmtConvIns->execute([
        $slug, $c['titulo'] ?? '',
        $c['resumen'] ?? null, $c['contenido'] ?? null,
        $c['pdf'] ?? null,
        $c['fecha'] ?? date('Y-m-d'), $c['cierre'] ?? null,
        !empty($c['activa']) ? 'publicada' : 'cerrada',
    ]);
    echo "  ✅ {$c['titulo']}\n";
    $stats['convocatorias']++;
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// 8. ASTROFOTOGRAFÍA (usa categoria_id en lugar de ENUM)
// ════════════════════════════════════════════════════════════════
echo "8. Astrofotografía\n";

// Mapa slug → id para astrofoto_categorias
$catAstroMap = [];
foreach ($pdo->query("SELECT id, slug FROM astrofoto_categorias") as $row) {
    $catAstroMap[$row['slug']] = (int) $row['id'];
}
if (empty($catAstroMap)) {
    echo "  ⚠ astrofoto_categorias vacía — asegúrate de haber importado schema.sql\n\n";
} else {
    $astrofotos  = leerJsonDir($rootDir . '/content/astrofotografia/');
    $stmtAstroIns = $pdo->prepare(
        "INSERT INTO astrofotografia
         (slug, titulo, fotografo, lugar, fecha, descripcion, imagen, categoria_id,
          telescopio, montura, camara, integracion, iso_gain, filtros, post_procesamiento,
          visible, destacada)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)"
    );

    foreach ($astrofotos as $a) {
        $slug = $a['id'] ?? $a['_filename'];
        if (slugExiste($pdo, 'astrofotografia', $slug)) {
            echo "  ⏭ Ya existe: $slug\n";
            $stats['saltados']++;
            continue;
        }
        $catSlug    = detectarCategoriaSlug($slug);
        $categoriaId = $catAstroMap[$catSlug] ?? null;
        $hw          = $a['hardware']   ?? [];
        $params      = $a['parametros'] ?? [];
        $fotografo   = $a['fotografo']  ?? $a['colaborador'] ?? 'SAZ';

        $stmtAstroIns->execute([
            $slug, $a['titulo'] ?? null, $fotografo,
            $a['lugar'] ?? null, $a['fecha'] ?? date('Y-m-d'),
            $a['descripcion'] ?? null, $a['imagen'] ?? null, $categoriaId,
            $hw['telescopio'] ?? null, $hw['montura'] ?? null, $hw['camara'] ?? null,
            $params['integracion'] ?? null, $params['iso_gain'] ?? null,
            $params['filtros'] ?? null, $a['post_procesamiento'] ?? null,
        ]);
        echo "  ✅ {$a['titulo']} [$catSlug]\n";
        $stats['astrofotografia']++;
    }
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// 9. MIEMBROS + FORMACIÓN + DIVULGACIÓN
// ════════════════════════════════════════════════════════════════
echo "9. Miembros\n";

$miembros    = leerJsonDir($rootDir . '/content/miembros/');
$stmtMIns    = $pdo->prepare(
    "INSERT INTO miembros
     (slug, nombre, especialidad, correo, ubicacion, distincion, imagen, cv, generalidades, activo, orden)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)"
);
$stmtFormIns = $pdo->prepare("INSERT INTO miembro_formacion (miembro_id, descripcion) VALUES (?, ?)");
$stmtDivIns  = $pdo->prepare("INSERT INTO miembro_divulgacion (miembro_id, descripcion) VALUES (?, ?)");

foreach ($miembros as $m) {
    $slug = $m['id'] ?? $m['_filename'];
    if ($slug === 'ejemplo' || ($m['nombre'] ?? '') === 'Pendiente') {
        echo "  ⏭ Saltando placeholder: $slug\n";
        $stats['saltados']++;
        continue;
    }
    if (slugExiste($pdo, 'miembros', $slug)) {
        echo "  ⏭ Ya existe: $slug\n";
        $stats['saltados']++;
        continue;
    }
    $perfil = $m['perfil_detallado'] ?? [];
    $stmtMIns->execute([
        $slug, $m['nombre'] ?? '', $m['especialidad'] ?? null,
        $m['correo'] ?? null, $m['ubicacion'] ?? null,
        $m['distincion'] ?? null, $m['imagen'] ?? null,
        $m['cv'] ?? null, $perfil['generalidades'] ?? null,
    ]);
    $miembroId = (int) $pdo->lastInsertId();
    echo "  ✅ {$m['nombre']}\n";
    $stats['miembros']++;

    foreach ($perfil['formacion'] ?? [] as $f) {
        $stmtFormIns->execute([$miembroId, $f]);
        $stats['formacion']++;
    }
    foreach ($perfil['divulgacion'] ?? [] as $d) {
        $stmtDivIns->execute([$miembroId, $d]);
        $stats['divulgacion']++;
    }
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// 10. MESA DIRECTIVA — Asignar cargo_id a miembros con cargo
// ════════════════════════════════════════════════════════════════
echo "10. Mesa directiva\n";

// Mapa nombre → id de la tabla cargos
$cargoMap = [];
foreach ($pdo->query("SELECT id, nombre FROM cargos") as $row) {
    $cargoMap[$row['nombre']] = (int) $row['id'];
}

// Solo miembros que tienen JSON en content/miembros/ y forman parte de la mesa
$mesaData = [
    'jesus-ivan-santamaria-najar' => ['Presidente',         1],
    'alejandro-gonzalez-sanchez'  => ['Consejo Consultivo', 1],
];

// Asignar cargo_id y en_mesa_directiva
$stmtCargo = $pdo->prepare(
    "UPDATE miembros SET cargo_id = ?, en_mesa_directiva = ? WHERE slug = ?"
);
foreach ($mesaData as $slug => [$cargoNombre, $enMesa]) {
    $cargoId = $cargoMap[$cargoNombre] ?? null;
    $stmtCargo->execute([$cargoId, $enMesa, $slug]);
    if ($stmtCargo->rowCount() > 0) {
        echo "  🔄 $slug → $cargoNombre\n";
    } else {
        echo "  ⚠ No encontrado o sin cambio: $slug\n";
    }
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// 11. USUARIO ADMIN (solo desarrollo local)
// ════════════════════════════════════════════════════════════════
echo "11. Usuario admin\n";

$stmtAdminChk = $pdo->prepare("SELECT 1 FROM admin_usuarios WHERE usuario = ? LIMIT 1");
$stmtAdminChk->execute(['admin']);
if ($stmtAdminChk->fetchColumn()) {
    echo "  ⏭ Usuario 'admin' ya existe\n";
} else {
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $pdo->prepare(
        "INSERT INTO admin_usuarios (nombre, usuario, email, hash, activo) VALUES (?, ?, ?, ?, 1)"
    )->execute(['Dev Local', 'admin', 'admin@localhost', $hash]);
    echo "  ✅ admin / admin123  ⚠ Cambiar en producción\n";
    $stats['admin']++;
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// 12. COLABORADORES
// ════════════════════════════════════════════════════════════════
echo "12. Colaboradores\n";

$colaboradoresData = [
    [
        'nombre'    => 'Dr. José María',
        'profesion' => 'Astrónomo Observacional',
        'redes'     => [
            ['LinkedIn', 'https://linkedin.com']
        ]
    ],
    [
        'nombre'    => 'M. en C. Sofía Ruiz',
        'profesion' => 'Divulgadora Científica',
        'redes'     => [
            ['ResearchGate', 'https://researchgate.net']
        ]
    ]
];

$stmtColChk = $pdo->prepare("SELECT id FROM colaboradores WHERE nombre = ? LIMIT 1");
$stmtColIns = $pdo->prepare("INSERT INTO colaboradores (nombre, profesion, imagen, activo, orden) VALUES (?, ?, NULL, 1, ?)");
$stmtRedIns = $pdo->prepare("INSERT INTO colaborador_redes (colaborador_id, nombre, url, orden) VALUES (?, ?, ?, ?)");

$colOrden = 1;
foreach ($colaboradoresData as $col) {
    $stmtColChk->execute([$col['nombre']]);
    $existingId = $stmtColChk->fetchColumn();
    
    if ($existingId) {
        echo "  ⏭ Ya existe colaborador: {$col['nombre']}\n";
        $stats['saltados']++;
        continue;
    }
    
    $stmtColIns->execute([$col['nombre'], $col['profesion'], $colOrden++]);
    $colId = (int)$pdo->lastInsertId();
    echo "  ✅ Colaborador: {$col['nombre']}\n";
    $stats['colaboradores']++;
    
    $redOrden = 1;
    foreach ($col['redes'] as [$redNombre, $redUrl]) {
        $stmtRedIns->execute([$colId, $redNombre, $redUrl, $redOrden++]);
        $stats['col_redes']++;
    }
}
echo "\n";


// ════════════════════════════════════════════════════════════════
// RESUMEN
// ════════════════════════════════════════════════════════════════
echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  Seed completado                                    ║\n";
echo "╠══════════════════════════════════════════════════════╣\n";
printf("║  Actividades:       %3d (%d ítems, %d imágenes)       ║\n", $stats['actividades'], $stats['act_items'], $stats['act_imagenes']);
printf("║  Observaciones:     %3d (%d ítems)                   ║\n", $stats['observaciones'], $stats['obs_items']);
printf("║  Categorías:        %3d                              ║\n", $stats['categorias']);
printf("║  Noticias:          %3d                              ║\n", $stats['noticias']);
printf("║  Eventos:           %3d (%d ediciones)               ║\n", $stats['eventos'], $stats['ediciones']);
printf("║  Convocatorias:     %3d                              ║\n", $stats['convocatorias']);
printf("║  Astrofotografía:   %3d                              ║\n", $stats['astrofotografia']);
printf("║  Miembros:          %3d                              ║\n", $stats['miembros']);
printf("║  Colaboradores:     %3d (%d redes)                  ║\n", $stats['colaboradores'], $stats['col_redes']);
printf("║  Saltados:          %3d                              ║\n", $stats['saltados']);
echo "╚══════════════════════════════════════════════════════╝\n";
