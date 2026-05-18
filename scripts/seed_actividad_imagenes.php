<?php
/**
 * scripts/seed_actividad_imagenes.php
 * Puebla la tabla actividad_imagenes con las imágenes existentes
 * en assets/img/actividades/{slug}/
 *
 * Ejecutar una sola vez: php scripts/seed_actividad_imagenes.php
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$pdo = get_pdo();

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  SAZ CMS — Seed: Imágenes de Actividades           ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

// Obtener todas las actividades
$actividades = $pdo->query("SELECT id, slug, titulo FROM actividades WHERE activo = 1 ORDER BY orden")->fetchAll();

if (empty($actividades)) {
    echo "ERROR: No hay actividades en la DB. Ejecuta seed_actividades.php primero.\n";
    exit(1);
}

$rootPath = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR;
$allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

$stmtCheck = $pdo->prepare("SELECT id FROM actividad_imagenes WHERE actividad_id = ? AND ruta = ? LIMIT 1");
$stmtInsert = $pdo->prepare(
    "INSERT INTO actividad_imagenes (actividad_id, ruta, alt_texto, orden)
     VALUES (?, ?, ?, ?)"
);

$totalInserted = 0;
$totalSkipped = 0;

foreach ($actividades as $act) {
    $imgDir = $rootPath . 'assets' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'actividades' . DIRECTORY_SEPARATOR . $act['slug'];
    
    echo "📁 {$act['titulo']} ({$act['slug']})\n";
    echo "   Directorio: assets/img/actividades/{$act['slug']}/\n";
    
    if (!is_dir($imgDir)) {
        echo "   ⚠️  Directorio no encontrado, saltando\n\n";
        continue;
    }
    
    // Escanear imágenes
    $imagenes = [];
    foreach (scandir($imgDir) as $file) {
        if ($file === '.' || $file === '..') continue;
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExt)) {
            $imagenes[] = $file;
        }
    }
    sort($imagenes);
    
    if (empty($imagenes)) {
        echo "   ⚠️  Sin imágenes, saltando\n\n";
        continue;
    }
    
    $orden = 1;
    foreach ($imagenes as $file) {
        // Ruta relativa tal como la espera el template
        $ruta = 'assets/img/actividades/' . $act['slug'] . '/' . $file;
        $altTexto = $act['titulo'] . ' — Sociedad Astronómica de Zacatecas';
        
        // Verificar si ya existe
        $stmtCheck->execute([$act['id'], $ruta]);
        if ($stmtCheck->fetch()) {
            echo "   ⏭ Ya existe: $file\n";
            $totalSkipped++;
        } else {
            $stmtInsert->execute([$act['id'], $ruta, $altTexto, $orden]);
            echo "   ✅ $file (orden=$orden)\n";
            $totalInserted++;
        }
        $orden++;
    }
    echo "\n";
}

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  Seed completado                                    ║\n";
echo "╠══════════════════════════════════════════════════════╣\n";
printf("║  Insertadas:  %-3d                                  ║\n", $totalInserted);
printf("║  Saltadas:    %-3d                                  ║\n", $totalSkipped);
echo "╚══════════════════════════════════════════════════════╝\n";
