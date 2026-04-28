<?php
$basePath = '../../';
$idMiembro = $_GET['id'] ?? '';
$rutaJson  = __DIR__ . "/../../content/miembros/{$idMiembro}.json";

if (!file_exists($rutaJson)) {
    header("Location: miembros-activos.php");
    exit;
}

$m = json_decode(file_get_contents($rutaJson), true);
$pageTitle = $m['nombre'] . ' — SAZ';
ob_start();
?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= $basePath ?>index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="miembros-activos.php">Miembros activos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $m['nombre'] ?></li>
        </ol>
    </nav>

    <div class="row mt-4">
        <aside class="col-lg-4">
            <div class="surface-card text-center p-4 shadow-sm mb-4">
                <img src="<?= $basePath ?>assets/img/miembros/<?= ($m['imagen']) ?>" 
                     alt="<?= ($m['nombre']) ?>" class="member-photo-img-sm mb-3"
                     style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;">
                <h1 class="h4 mb-1"><?= ($m['nombre']) ?></h1>
                <p class="text-muted small"><?= ($m['especialidad']) ?></p>
                
                <ul class="list-unstyled text-start mt-4 small">
                    <li class="mb-2"><i class="bi bi-envelope me-2"></i><?= $m['correo'] ?></li>
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i><?= $m['ubicacion'] ?></li>
                    <li class="mb-2"><i class="bi bi-award me-2"></i><?= $m['distincion'] ?></li>
                </ul>

                <?php if (!empty($m['cv'])): ?>
                    <div class="d-grid mt-4">
                        <a href="<?= ($m['cv']) ?>" target="_blank" 
                        class="btn btn-primary btn-sm">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Ver CV Completo
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </aside>

        <main class="col-lg-8">
            <section class="surface-card p-4 shadow-sm mb-4">
                <h2 class="h5 mb-3"><i class="bi bi-mortarboard me-2"></i>Formación académica</h2>
                <ul class="mb-0">
                    <?php if(!empty($m['perfil_detallado']['formacion'])): ?>
                        <?php foreach($m['perfil_detallado']['formacion'] as $f): ?>
                            <li><?= ($f) ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Información en actualización</li>
                    <?php endif; ?>
                </ul>
            </section>

            <section class="surface-card p-4 shadow-sm mb-4">
                <h2 class="h5 mb-3"><i class="bi bi-search me-2"></i>Labor de Divulgación</h2>
                <ul class="mb-0">
                    <?php if(!empty($m['perfil_detallado']['divulgacion'])): ?>
                        <?php foreach($m['perfil_detallado']['divulgacion'] as $d): ?>
                            <li><?= ($d) ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>Información en actualización</li>
                    <?php endif; ?>
                </ul>
            </section> 

            <section class="surface-card p-4 shadow-sm">
                <h2 class="h5 mb-3"><i class="bi bi-info-circle me-2"></i>Temas de Interés</h2>
                <p class="mb-0">
                    <?= !empty($m['perfil_detallado']['generalidades']) ? ($m['perfil_detallado']['generalidades']) : 'Perfil en proceso de actualización.' ?>
                </p>
            </section>
        </main>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';