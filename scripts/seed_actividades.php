<?php
/**
 * scripts/seed_actividades.php
 * Puebla las tablas actividades + actividad_items con los datos
 * que estaban hardcodeados en pages/actividades/*.php
 *
 * Ejecutar una sola vez: php scripts/seed_actividades.php
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_pdo();

// Datos extraídos de las 5 páginas de actividades
$actividades = [
    [
        'slug'        => 'charlas',
        'titulo'      => 'Charlas',
        'icono'       => 'bi bi-chat-left-text',
        'descripcion' => 'Sesiones informales de conversacion e intercambio de conocimientos entre miembros de la SAZ y el publico interesado.',
        'orden'       => 1,
        'items' => [
            ['titulo' => 'Charlas de cafe astronomico', 'descripcion' => 'Reuniones informales donde se discuten noticias recientes de astronomia en un ambiente relajado.', 'orden' => 1],
            ['titulo' => 'Noches de preguntas', 'descripcion' => 'Sesiones abiertas donde el publico puede hacer preguntas a los miembros sobre cualquier tema astronomico.', 'orden' => 2],
            ['titulo' => 'Charlas para escuelas', 'descripcion' => 'Presentaciones adaptadas para estudiantes de primaria, secundaria y bachillerato.', 'orden' => 3],
            ['titulo' => 'Charlas con invitados', 'descripcion' => 'Sesiones especiales con astronomos y cientificos invitados de otras instituciones.', 'orden' => 4],
        ],
    ],
    [
        'slug'        => 'conferencias',
        'titulo'      => 'Conferencias',
        'icono'       => 'bi bi-easel',
        'descripcion' => 'Sesiones magistrales impartidas por especialistas en astronomia, astrofisica y ciencias afines. Abiertas al publico general.',
        'orden'       => 2,
        'items' => [
            ['titulo' => 'Conferencias de divulgacion', 'descripcion' => 'Charlas accesibles para publico no especializado sobre temas de actualidad en astronomia y ciencias del espacio.', 'orden' => 1],
            ['titulo' => 'Conferencias especializadas', 'descripcion' => 'Presentaciones tecnicas dirigidas a estudiantes y profesionales del area con temas de investigacion avanzada.', 'orden' => 2],
            ['titulo' => 'Ciclos tematicos', 'descripcion' => 'Series de conferencias organizadas alrededor de un tema central, como cosmologia, exoplanetas o historia de la astronomia.', 'orden' => 3],
            ['titulo' => 'Conferencias virtuales', 'descripcion' => 'Transmisiones en linea que permiten la participacion de publico a nivel nacional e internacional.', 'orden' => 4],
        ],
    ],
    [
        'slug'        => 'cursos',
        'titulo'      => 'Cursos',
        'icono'       => 'bi bi-book',
        'descripcion' => 'Programas de formacion estructurados con duracion de varias semanas, dirigidos a diferentes niveles de conocimiento.',
        'orden'       => 3,
        'items' => [
            ['titulo' => 'Astronomia basica', 'descripcion' => 'Curso introductorio de 8 semanas que cubre el sistema solar, estrellas, galaxias y cosmologia.', 'orden' => 1],
            ['titulo' => 'Mecanica celeste', 'descripcion' => 'Estudio del movimiento de cuerpos celestes. Requiere conocimientos basicos de fisica y matematicas.', 'orden' => 2],
            ['titulo' => 'Astrofotografia intermedia', 'descripcion' => 'Tecnicas avanzadas de captura y procesamiento de imagen astronomica con equipo dedicado.', 'orden' => 3],
            ['titulo' => 'Historia de la astronomia', 'descripcion' => 'Recorrido historico desde las civilizaciones antiguas hasta los descubrimientos mas recientes.', 'orden' => 4],
        ],
    ],
    [
        'slug'        => 'diplomados',
        'titulo'      => 'Diplomados',
        'icono'       => 'bi bi-mortarboard',
        'descripcion' => 'Programas academicos de mayor profundidad y duracion, con reconocimiento institucional, dirigidos a profesionales y docentes.',
        'orden'       => 4,
        'items' => [
            ['titulo' => 'Diplomado en astronomia general', 'descripcion' => 'Programa de 6 meses que abarca fundamentos de astrofisica, instrumentacion y divulgacion cientifica.', 'orden' => 1],
            ['titulo' => 'Diplomado en didactica de la astronomia', 'descripcion' => 'Orientado a docentes. Estrategias y recursos para ensenar astronomia en el aula.', 'orden' => 2],
            ['titulo' => 'Certificacion en observacion astronomica', 'descripcion' => 'Programa intensivo con evaluacion practica en manejo de telescopios y tecnicas observacionales.', 'orden' => 3],
            ['titulo' => 'Proximos diplomados', 'descripcion' => 'La SAZ esta desarrollando nuevos programas en colaboracion con universidades de la region. Consulta esta pagina para actualizaciones.', 'orden' => 4],
        ],
    ],
    [
        'slug'        => 'talleres',
        'titulo'      => 'Talleres',
        'icono'       => 'bi bi-tools',
        'descripcion' => 'Actividades practicas orientadas a desarrollar habilidades en observacion, instrumentacion y procesamiento de datos astronomicos.',
        'orden'       => 5,
        'items' => [
            ['titulo' => 'Construccion de telescopios', 'descripcion' => 'Taller practico para construir un telescopio refractor basico con materiales accesibles.', 'orden' => 1],
            ['titulo' => 'Fotografia nocturna', 'descripcion' => 'Introduccion a la captura de imagenes del cielo con camaras DSLR y smartphones.', 'orden' => 2],
            ['titulo' => 'Procesamiento de imagenes', 'descripcion' => 'Uso de software libre para apilar y procesar fotografias astronomicas.', 'orden' => 3],
            ['titulo' => 'Orientacion celeste', 'descripcion' => 'Aprende a localizar constelaciones, planetas y objetos de cielo profundo con y sin telescopio.', 'orden' => 4],
        ],
    ],
];

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  SAZ CMS — Seed: Actividades                       ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

$totalActividades = 0;
$totalItems = 0;

$stmtCheck = $pdo->prepare("SELECT id FROM actividades WHERE slug = ? LIMIT 1");
$stmtInsert = $pdo->prepare(
    "INSERT INTO actividades (slug, titulo, icono, descripcion, activo, orden)
     VALUES (?, ?, ?, ?, 1, ?)"
);
$stmtItem = $pdo->prepare(
    "INSERT INTO actividad_items (actividad_id, titulo, descripcion, orden)
     VALUES (?, ?, ?, ?)"
);

foreach ($actividades as $act) {
    $stmtCheck->execute([$act['slug']]);
    $exists = $stmtCheck->fetch();

    if ($exists) {
        echo "  ⏭ Ya existe: {$act['slug']}\n";
        continue;
    }

    $stmtInsert->execute([
        $act['slug'],
        $act['titulo'],
        $act['icono'],
        $act['descripcion'],
        $act['orden'],
    ]);
    $actividadId = $pdo->lastInsertId();
    $totalActividades++;
    echo "  ✅ {$act['titulo']} (id=$actividadId)\n";

    foreach ($act['items'] as $item) {
        $stmtItem->execute([
            $actividadId,
            $item['titulo'],
            $item['descripcion'],
            $item['orden'],
        ]);
        $totalItems++;
        echo "     + {$item['titulo']}\n";
    }
}

echo "\n╔══════════════════════════════════════════════════════╗\n";
echo "║  Seed completado                                    ║\n";
echo "╠══════════════════════════════════════════════════════╣\n";
echo "║  Actividades:  $totalActividades                              ║\n";
echo "║  Items:        $totalItems                                ║\n";
echo "╚══════════════════════════════════════════════════════╝\n";
