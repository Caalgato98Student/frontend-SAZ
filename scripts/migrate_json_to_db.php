<?php
/**
 * scripts/migrate_json_to_db.php
 *
 * Migra todo el contenido de los archivos JSON (content/) a la base de datos.
 * Diseñado para ser ejecutado por cualquier dev después de crear la BD.
 *
 * REQUISITOS PREVIOS:
 *   1. Tener XAMPP con MySQL corriendo.
 *   2. Haber importado scripts/schema.sql en la base "saz_cms".
 *   3. Tener config.php configurado (cp config.example.php config.php).
 *
 * USO:
 *   php scripts/migrate_json_to_db.php
 *
 * IDEMPOTENTE: Si ya existen registros con el mismo slug, se saltan.
 *              Se puede ejecutar múltiples veces sin duplicar datos.
 */

// ── Configuración ──────────────────────────────────────────────
$rootDir = dirname(__DIR__);
require_once $rootDir . '/config.php';
require_once $rootDir . '/includes/db.php';

$pdo = get_pdo();

// Contadores
$stats = [
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
    'admin'          => 0,
    'config'         => 0,
    'saltados'       => 0,
];

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  SAZ CMS — Migración JSON → Base de datos          ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

// ── Helpers ────────────────────────────────────────────────────

/**
 * Lee todos los JSON de un directorio.
 * @return array<array> Contenido decodificado de cada archivo.
 */
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
            // Usar nombre del archivo como slug/id si no viene en el JSON
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

/**
 * Verifica si un slug ya existe en una tabla.
 */
function slugExiste(PDO $pdo, string $tabla, string $slug): bool
{
    $stmt = $pdo->prepare("SELECT 1 FROM `$tabla` WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    return (bool) $stmt->fetchColumn();
}

/**
 * Parsea una fecha que puede venir como "2026-03-20" o como texto libre.
 * Retorna [fecha_inicio, fecha_fin] o [null, null].
 */
function parsearFecha(string $fechaRaw): array
{
    // Si es una fecha ISO válida
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaRaw)) {
        return [$fechaRaw, null];
    }

    // Intentar extraer rango "Del 21 al 25 de abril"
    // No podemos convertirlo sin año, así que dejamos null
    return [null, null];
}

/**
 * Detecta la categoría de astrofotografía por el prefijo del nombre de archivo.
 */
function detectarCategoriaAstro(string $filename): string
{
    if (str_starts_with($filename, 'sol'))      return 'sol';
    if (str_starts_with($filename, 'luna'))     return 'luna';
    if (str_starts_with($filename, 'profundo')) return 'espacio_profundo';
    return 'espacio_profundo'; // Default
}


// ════════════════════════════════════════════════════════════════
// 1. CATEGORÍAS — Extraer de las noticias y crear
// ════════════════════════════════════════════════════════════════
echo "1. Categorías\n";

$noticias = leerJsonDir($rootDir . '/content/noticias/');
$categoriasUnicas = [];
foreach ($noticias as $n) {
    if (!empty($n['categoria'])) {
        $cat = trim($n['categoria']);
        $categoriasUnicas[$cat] = true;
    }
}

$stmtCatInsert = $pdo->prepare(
    "INSERT IGNORE INTO categorias (nombre, slug) VALUES (?, ?)"
);

foreach (array_keys($categoriasUnicas) as $cat) {
    $slug = mb_strtolower(trim($cat));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    $stmtCatInsert->execute([ucfirst($cat), $slug]);
    if ($stmtCatInsert->rowCount() > 0) {
        echo "  ✅ Categoría: $cat\n";
        $stats['categorias']++;
    } else {
        echo "  ⏭ Categoría ya existe: $cat\n";
    }
}

// Cargar mapa de categorías para usar en noticias
$catMap = [];
$catRows = $pdo->query("SELECT id, slug FROM categorias")->fetchAll();
foreach ($catRows as $row) {
    $catMap[$row['slug']] = $row['id'];
}

echo "\n";


// ════════════════════════════════════════════════════════════════
// 2. NOTICIAS
// ════════════════════════════════════════════════════════════════
echo "2. Noticias\n";

$stmtNoticia = $pdo->prepare(
    "INSERT INTO noticias (slug, titulo, resumen, contenido, imagen, autor, categoria_id, fecha, estado, visible_en_principal, fijado)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'publicado', 1, 0)"
);

foreach ($noticias as $n) {
    $slug = $n['id'] ?? $n['_filename'];

    if (slugExiste($pdo, 'noticias', $slug)) {
        echo "  ⏭ Ya existe: $slug\n";
        $stats['saltados']++;
        continue;
    }

    // Resolver categoría
    $catId = null;
    if (!empty($n['categoria'])) {
        $catSlug = mb_strtolower(trim($n['categoria']));
        $catSlug = preg_replace('/[^a-z0-9]+/', '-', $catSlug);
        $catSlug = trim($catSlug, '-');
        $catId = $catMap[$catSlug] ?? null;
    }

    $stmtNoticia->execute([
        $slug,
        $n['titulo'] ?? '',
        $n['resumen'] ?? null,
        $n['contenido'] ?? null,
        $n['imagen'] ?? null,
        $n['autor'] ?? null,
        $catId,
        $n['fecha'] ?? date('Y-m-d'),
    ]);

    echo "  ✅ {$n['titulo']}\n";
    $stats['noticias']++;
}

echo "\n";


// ════════════════════════════════════════════════════════════════
// 3. EVENTOS + EDICIONES + IMÁGENES DE EDICIÓN
// ════════════════════════════════════════════════════════════════
echo "3. Eventos\n";

$eventos = leerJsonDir($rootDir . '/content/eventos/');

$stmtEvento = $pdo->prepare(
    "INSERT INTO eventos (slug, titulo, descripcion, imagen_principal, activo, orden)
     VALUES (?, ?, ?, ?, 1, 0)"
);

$stmtEdicion = $pdo->prepare(
    "INSERT INTO evento_ediciones (evento_id, anio, fecha_inicio, fecha_fin, lugar, resumen, imagen, pdf)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

$stmtEdImg = $pdo->prepare(
    "INSERT INTO edicion_imagenes (edicion_id, ruta, alt_texto, orden)
     VALUES (?, ?, ?, ?)"
);

foreach ($eventos as $ev) {
    $slug = $ev['slug'] ?? $ev['_filename'];

    if (slugExiste($pdo, 'eventos', $slug)) {
        echo "  ⏭ Ya existe: $slug\n";
        $stats['saltados']++;
        continue;
    }

    $stmtEvento->execute([
        $slug,
        $ev['titulo'] ?? '',
        $ev['descripcion'] ?? null,
        $ev['imagen_principal'] ?? null,
    ]);
    $eventoId = (int) $pdo->lastInsertId();
    echo "  ✅ Evento: {$ev['titulo']}\n";
    $stats['eventos']++;

    // Ediciones
    if (!empty($ev['ediciones'])) {
        foreach ($ev['ediciones'] as $ed) {
            $anio = $ed['anio'] ?? date('Y');

            // Parsear fecha
            $fechaRaw = $ed['fecha'] ?? '';
            [$fechaInicio, $fechaFin] = parsearFecha($fechaRaw);

            $stmtEdicion->execute([
                $eventoId,
                $anio,
                $fechaInicio,
                $fechaFin,
                $ed['lugar'] ?? null,
                $ed['resumen'] ?? null,
                $ed['imagen'] ?? null,
                $ed['pdf'] ?? null,
            ]);
            $edicionId = (int) $pdo->lastInsertId();
            echo "     ✅ Edición $anio\n";
            $stats['ediciones']++;

            // Imágenes de la edición (puede ser "imagenes" o "_imagenes")
            $imagenes = $ed['imagenes'] ?? $ed['_imagenes'] ?? [];
            foreach ($imagenes as $idx => $img) {
                $stmtEdImg->execute([
                    $edicionId,
                    $img['archivo'] ?? '',
                    $img['label'] ?? null,
                    $idx,
                ]);
                $stats['edicion_imgs']++;
            }
        }
    }
}

echo "\n";


// ════════════════════════════════════════════════════════════════
// 4. CONVOCATORIAS
// ════════════════════════════════════════════════════════════════
echo "4. Convocatorias\n";

$convocatorias = leerJsonDir($rootDir . '/content/convocatorias/');

$stmtConv = $pdo->prepare(
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

    // Mapear activa (bool) → estado (enum)
    $activa = !empty($c['activa']);
    $estado = $activa ? 'publicada' : 'cerrada';

    $stmtConv->execute([
        $slug,
        $c['titulo'] ?? '',
        $c['resumen'] ?? null,
        $c['contenido'] ?? null,
        $c['pdf'] ?? null,
        $c['fecha'] ?? date('Y-m-d'),
        $c['cierre'] ?? null,
        $estado,
    ]);

    echo "  ✅ {$c['titulo']}\n";
    $stats['convocatorias']++;
}

echo "\n";


// ════════════════════════════════════════════════════════════════
// 5. ASTROFOTOGRAFÍA
// ════════════════════════════════════════════════════════════════
echo "5. Astrofotografía\n";

$astrofotos = leerJsonDir($rootDir . '/content/astrofotografia/');

$stmtAstro = $pdo->prepare(
    "INSERT INTO astrofotografia (slug, titulo, fotografo, lugar, fecha, descripcion, imagen, categoria,
     telescopio, montura, camara, integracion, iso_gain, filtros, post_procesamiento, visible, destacada)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)"
);

foreach ($astrofotos as $a) {
    $slug = $a['id'] ?? $a['_filename'];

    if (slugExiste($pdo, 'astrofotografia', $slug)) {
        echo "  ⏭ Ya existe: $slug\n";
        $stats['saltados']++;
        continue;
    }

    $categoria = detectarCategoriaAstro($slug);
    $hw = $a['hardware'] ?? [];
    $params = $a['parametros'] ?? [];

    // Usar 'fotografo' o 'colaborador' como fallback
    $fotografo = $a['fotografo'] ?? $a['colaborador'] ?? 'SAZ';

    $stmtAstro->execute([
        $slug,
        $a['titulo'] ?? null,
        $fotografo,
        $a['lugar'] ?? null,
        $a['fecha'] ?? date('Y-m-d'),
        $a['descripcion'] ?? null,
        $a['imagen'] ?? null,
        $categoria,
        $hw['telescopio'] ?? null,
        $hw['montura'] ?? null,
        $hw['camara'] ?? null,
        $params['integracion'] ?? null,
        $params['iso_gain'] ?? null,
        $params['filtros'] ?? null,
        $a['post_procesamiento'] ?? null,
    ]);

    echo "  ✅ {$a['titulo']} [$categoria]\n";
    $stats['astrofotografia']++;
}

echo "\n";


// ════════════════════════════════════════════════════════════════
// 6. MIEMBROS + FORMACIÓN + DIVULGACIÓN
// ════════════════════════════════════════════════════════════════
echo "6. Miembros\n";

$miembros = leerJsonDir($rootDir . '/content/miembros/');

$stmtMiembro = $pdo->prepare(
    "INSERT INTO miembros (slug, nombre, especialidad, correo, ubicacion, distincion, imagen, cv, generalidades, activo, orden)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0)"
);

$stmtFormacion = $pdo->prepare(
    "INSERT INTO miembro_formacion (miembro_id, descripcion) VALUES (?, ?)"
);

$stmtDivulgacion = $pdo->prepare(
    "INSERT INTO miembro_divulgacion (miembro_id, descripcion) VALUES (?, ?)"
);

foreach ($miembros as $m) {
    $slug = $m['id'] ?? $m['_filename'];

    // Saltar el archivo "ejemplo.json" que es un placeholder
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

    $stmtMiembro->execute([
        $slug,
        $m['nombre'] ?? '',
        $m['especialidad'] ?? null,
        $m['correo'] ?? null,
        $m['ubicacion'] ?? null,
        $m['distincion'] ?? null,
        $m['imagen'] ?? null,
        $m['cv'] ?? null,
        $perfil['generalidades'] ?? null,
    ]);
    $miembroId = (int) $pdo->lastInsertId();
    echo "  ✅ {$m['nombre']}\n";
    $stats['miembros']++;

    // Formación
    foreach ($perfil['formacion'] ?? [] as $f) {
        $stmtFormacion->execute([$miembroId, $f]);
        $stats['formacion']++;
    }

    // Divulgación
    foreach ($perfil['divulgacion'] ?? [] as $d) {
        $stmtDivulgacion->execute([$miembroId, $d]);
        $stats['divulgacion']++;
    }
}

echo "\n";


// ════════════════════════════════════════════════════════════════
// 7. USUARIO ADMIN (para desarrollo local)
// ════════════════════════════════════════════════════════════════
echo "7. Usuario admin\n";

$stmtAdminCheck = $pdo->prepare("SELECT 1 FROM admin_usuarios WHERE usuario = ? LIMIT 1");
$stmtAdminCheck->execute(['admin']);

if ($stmtAdminCheck->fetchColumn()) {
    echo "  ⏭ Usuario 'admin' ya existe\n";
} else {
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmtAdmin = $pdo->prepare(
        "INSERT INTO admin_usuarios (nombre, usuario, email, hash, activo)
         VALUES (?, ?, ?, ?, 1)"
    );
    $stmtAdmin->execute(['Dev Local', 'admin', 'admin@localhost', $hash]);
    echo "  ✅ Usuario: admin / Contraseña: admin123\n";
    echo "     ⚠ SOLO para desarrollo local — cambiar en producción\n";
    $stats['admin']++;
}

echo "\n";


// ════════════════════════════════════════════════════════════════
// 8. CONFIGURACIÓN INICIAL
// ════════════════════════════════════════════════════════════════
echo "8. Configuración\n";

$configDefaults = [
    ['noticias_home_limite', '5', 'Cantidad de noticias visibles en la portada'],
];

$stmtConfig = $pdo->prepare(
    "INSERT IGNORE INTO configuracion (clave, valor, descripcion) VALUES (?, ?, ?)"
);

foreach ($configDefaults as [$clave, $valor, $desc]) {
    $stmtConfig->execute([$clave, $valor, $desc]);
    if ($stmtConfig->rowCount() > 0) {
        echo "  ✅ $clave = $valor\n";
        $stats['config']++;
    } else {
        echo "  ⏭ Ya existe: $clave\n";
    }
}

echo "\n";


// ════════════════════════════════════════════════════════════════
// 9. MESA DIRECTIVA — Actualizar cargo y en_mesa_directiva
// ════════════════════════════════════════════════════════════════
echo "9. Mesa directiva (actualizar cargos)\n";

// Datos que estaban hardcodeados en pages/quienes-somos/directorio.php
// y pages/quienes-somos/mesa-directiva.php
$cargos = [
    'jesus-ivan-santamaria-najar' => ['Presidente', 1],
    // Los siguientes miembros no tienen JSON — se crean directamente
];

// Miembros que NO tienen JSON pero estaban hardcodeados en directorio.php
$miembrosExtra = [
    ['ciro-robles-berumen',   'M.C. Ciro Robles Berumen',                     'Maestro en Ciencias',     'Secretario',          1, 'cirorobles2405@gmail.com',      'ciro-robles.png'],
    ['armando-garcia-castillo','L.E. Armando García Castillo',                 'Licenciado en Economía',  'Tesorero',            1, 'garcia.a.castillo@gmail.com',   'armando-garcia.png'],
    ['alejandro-gonzalez-sanchez','Dr. Alejandro González Sánchez',            'Doctor en Astronomía',    'Consejo Consultivo',  0, 'alejandro.gonzalez@uaz.edu.mx', null],
    ['berenice-gomez-martinez','Berenice Gómez Martínez',                      'Divulgadora',             'Consejo Consultivo',  0, 'Berebankrobber@gmail.com',       null],
    ['victor-munoz-suarez',   'Ing. Víctor Alejandro Rafael Muñoz Suárez',    'Ingeniero',               'Consejo Consultivo',  0, 'geovector2010@gmail.com',       null],
    ['corina-bobadilla-larios','M.L.M. Corina Bobadilla Larios',              'Maestro en Lengua Materna','Consejo de Vigilancia',0,'sazac2010@gmail.com',           null],
];

// Actualizar el miembro que ya existe (Iván Santamaría)
foreach ($cargos as $slug => [$cargo, $enMesa]) {
    $stmt = $pdo->prepare("UPDATE miembros SET cargo = ?, en_mesa_directiva = ? WHERE slug = ?");
    $stmt->execute([$cargo, $enMesa, $slug]);
    if ($stmt->rowCount() > 0) {
        echo "  🔄 Actualizado: $slug → $cargo\n";
    } else {
        echo "  ⏭ No encontrado o sin cambio: $slug\n";
    }
}

// Insertar miembros que solo existían hardcodeados
$stmtMiembroExtra = $pdo->prepare(
    "INSERT IGNORE INTO miembros (slug, nombre, especialidad, cargo, en_mesa_directiva, correo, imagen, activo, orden)
     VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)"
);

foreach ($miembrosExtra as $idx => [$slug, $nombre, $espec, $cargo, $enMesa, $correo, $imagen]) {
    $stmtMiembroExtra->execute([$slug, $nombre, $espec, $cargo, $enMesa, $correo, $imagen, $idx + 1]);
    if ($stmtMiembroExtra->rowCount() > 0) {
        echo "  ✅ $nombre [$cargo]\n";
        $stats['miembros']++;
    } else {
        // Ya existe, actualizar cargo
        $stmtUpd = $pdo->prepare("UPDATE miembros SET cargo = ?, en_mesa_directiva = ? WHERE slug = ?");
        $stmtUpd->execute([$cargo, $enMesa, $slug]);
        echo "  🔄 Actualizado: $slug → $cargo\n";
    }
}

echo "\n";


// ════════════════════════════════════════════════════════════════
// 10. OBSERVACIONES — Contenido que era HTML estático
// ════════════════════════════════════════════════════════════════
echo "10. Observaciones\n";

$observacionesData = [
    [
        'slug'  => 'diurna',
        'titulo' => 'Observacion Diurna',
        'icono' => 'bi bi-brightness-high text-info',
        'descripcion_intro' => 'Sesiones educativas para publico general donde se exploran fenomenos astronomicos visibles durante el dia.',
        'recomendaciones' => null,
        'orden' => 1,
        'items' => [
            ['La Luna de dia',        'bi bi-moon-stars me-2',       'Observacion de la Luna cuando es visible durante el dia. Se explican las fases lunares y la mecanica orbital.',   1],
            ['Planetas visibles',     'bi bi-globe me-2',            'En condiciones favorables, Venus puede observarse a plena luz del dia. Se explica por que y como localizarlo.', 2],
            ['Gnomonica',             'bi bi-compass me-2',          'Uso de relojes de sol y proyeccion de sombras para comprender el movimiento aparente del Sol y las estaciones del ano.', 3],
            ['Meteorologia basica',   'bi bi-cloud-sun me-2',        'Introduccion a la atmosfera terrestre: como afecta la observacion astronomica y como interpretar condiciones del cielo.', 4],
        ],
    ],
    [
        'slug'  => 'nocturna',
        'titulo' => 'Observacion Nocturna',
        'icono' => 'bi bi-moon-stars-fill text-primary',
        'descripcion_intro' => 'Sesiones de observacion de planetas, nebulosas, cumulos estelares y galaxias con telescopios de diferentes tipos y aperturas.',
        'recomendaciones' => '<ul><li>Llegar 15 minutos antes para adaptacion visual a la oscuridad.</li><li>Llevar ropa abrigadora; las noches zacatecanas pueden ser frias.</li><li>Evitar el uso de luces blancas; se recomienda linterna roja.</li><li>No se requiere telescopio propio; la SAZ proporciona equipo.</li></ul>',
        'orden' => 2,
        'items' => [
            ['Planetas',         'bi bi-globe2 me-2 text-warning',    'Observacion de los planetas visibles: Jupiter con sus lunas galileanas, Saturno y sus anillos, Marte y Venus.', 1],
            ['Cielo profundo',   'bi bi-cloud-haze2 me-2 text-info',  'Nebulosas de emision y reflexion, cumulos abiertos y globulares, galaxias cercanas como Andromeda y el Triangulo.', 2],
            ['Estrellas dobles', 'bi bi-stars me-2 text-warning',     'Sistemas estelares multiples con contrastes de color y brillo. Ideales para telescopios de cualquier apertura.', 3],
        ],
    ],
    [
        'slug'  => 'solar',
        'titulo' => 'Observacion Solar',
        'icono' => 'bi bi-sun text-warning',
        'descripcion_intro' => 'Jornadas de observacion segura del Sol con filtros solares certificados y telescopios especializados.',
        'recomendaciones' => null,
        'orden' => 3,
        'items' => [
            ['Seguridad',      'bi bi-shield-check me-2 text-success', 'Todas las observaciones solares se realizan con filtros certificados ISO 12312-2. Nunca se debe observar el Sol directamente sin proteccion adecuada.', 1],
            ['Equipo',         'bi bi-telescope me-2 text-primary',    'Utilizamos telescopios solares con filtro H-alpha que permiten observar protuberancias, manchas solares y filamentos en la cromosfera.', 2],
            ['Programacion',   'bi bi-calendar-event me-2 text-info',  'Las sesiones se realizan generalmente los sabados por la manana en plazas publicas de Zacatecas. Consulta el calendario de eventos para fechas especificas.', 3],
            ['Publico',        'bi bi-people me-2 text-primary',       'Actividad abierta a todas las edades. No se requiere experiencia previa ni inscripcion. Solo acude al punto de observacion en la fecha indicada.', 4],
        ],
    ],
];

$stmtObs = $pdo->prepare(
    "INSERT IGNORE INTO observaciones (slug, titulo, icono, descripcion_intro, recomendaciones, activo, orden)
     VALUES (?, ?, ?, ?, ?, 1, ?)"
);

$stmtObsItem = $pdo->prepare(
    "INSERT INTO observacion_items (observacion_id, titulo, icono, descripcion, orden)
     VALUES (?, ?, ?, ?, ?)"
);

$obsCount = 0;
$obsItemCount = 0;

foreach ($observacionesData as $obs) {
    $stmtObs->execute([
        $obs['slug'],
        $obs['titulo'],
        $obs['icono'],
        $obs['descripcion_intro'],
        $obs['recomendaciones'],
        $obs['orden'],
    ]);

    if ($stmtObs->rowCount() > 0) {
        $obsId = (int) $pdo->lastInsertId();
        echo "  ✅ {$obs['titulo']}\n";
        $obsCount++;

        foreach ($obs['items'] as [$titulo, $icono, $desc, $orden]) {
            $stmtObsItem->execute([$obsId, $titulo, $icono, $desc, $orden]);
            $obsItemCount++;
        }
        echo "     + " . count($obs['items']) . " ítems\n";
    } else {
        echo "  ⏭ Ya existe: {$obs['slug']}\n";
    }
}

echo "\n";


// ════════════════════════════════════════════════════════════════
// RESUMEN
// ════════════════════════════════════════════════════════════════
echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  Migración completada                              ║\n";
echo "╠══════════════════════════════════════════════════════╣\n";
printf("║  Categorías:      %3d                              ║\n", $stats['categorias']);
printf("║  Noticias:        %3d                              ║\n", $stats['noticias']);
printf("║  Eventos:         %3d                              ║\n", $stats['eventos']);
printf("║  Ediciones:       %3d                              ║\n", $stats['ediciones']);
printf("║  Imágenes edición:%3d                              ║\n", $stats['edicion_imgs']);
printf("║  Convocatorias:   %3d                              ║\n", $stats['convocatorias']);
printf("║  Astrofotografía: %3d                              ║\n", $stats['astrofotografia']);
printf("║  Miembros:        %3d                              ║\n", $stats['miembros']);
printf("║  Formación:       %3d                              ║\n", $stats['formacion']);
printf("║  Divulgación:     %3d                              ║\n", $stats['divulgacion']);
printf("║  Observaciones:   %3d (%d ítems)                   ║\n", $obsCount, $obsItemCount);
printf("║  Admin:           %3d                              ║\n", $stats['admin']);
printf("║  Config:          %3d                              ║\n", $stats['config']);
printf("║  Saltados:        %3d                              ║\n", $stats['saltados']);
echo "╚══════════════════════════════════════════════════════╝\n";

