<?php
/**
 * admin/configuracion.php — Panel de Ajustes Generales.
 */
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/auth.php';
require_admin_auth();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Ajustes Generales';
$basePath  = '../';
$pdo       = get_pdo();
$errors    = [];
$msg       = $_GET['msg'] ?? '';

// Cargar todas las configuraciones
$configuraciones = $pdo->query("SELECT * FROM configuracion ORDER BY categoria, clave")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $updated = [];
    foreach ($configuraciones as $conf) {
        $clave = $conf['clave'];
        if (isset($_POST[$clave])) {
            $nuevoValor = $_POST[$clave];
            
            // Validaciones según tipo
            if ($conf['tipo'] === 'number') {
                $nuevoValor = intval($nuevoValor);
            } else {
                $nuevoValor = trim($nuevoValor);
            }
            
            $updated[$clave] = $nuevoValor;
        }
    }

    if (!$errors) {
        $stmtUpdate = $pdo->prepare("UPDATE configuracion SET valor = ? WHERE clave = ?");
        foreach ($updated as $clave => $valor) {
            $stmtUpdate->execute([$valor, $clave]);
        }
        header("Location: configuracion.php?msg=editado"); exit;
    }
}

$csrf = generate_csrf_token();

// Agrupar por categoría
$categorias = [];
foreach ($configuraciones as $conf) {
    $categorias[$conf['categoria']][] = $conf;
}

ob_start(); ?>

<?php if ($msg === 'editado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Ajustes guardados correctamente.</div>
<?php endif; ?>

<?php foreach ($errors as $e): ?>
  <div class="alert-adm alert-adm-danger"><i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>

<form method="POST">
  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
  
  <div class="row g-3">
    <div class="col-lg-8">
      <?php foreach ($categorias as $catName => $configs): ?>
        <div class="adm-card mb-3">
          <h2 class="adm-card-title"><?= htmlspecialchars($catName) ?></h2>
          <div class="row g-3">
            <?php foreach ($configs as $c): ?>
              <div class="col-12">
                <label class="form-label" for="<?= htmlspecialchars($c['clave']) ?>">
                  <?= htmlspecialchars($c['descripcion'] ?: $c['clave']) ?>
                </label>
                
                <?php if ($c['tipo'] === 'textarea' || $c['tipo'] === 'html'): ?>
                  <textarea class="form-control" 
                            id="<?= htmlspecialchars($c['clave']) ?>" 
                            name="<?= htmlspecialchars($c['clave']) ?>" 
                            rows="4"><?= htmlspecialchars($c['valor'] ?? '') ?></textarea>
                            
                <?php elseif ($c['tipo'] === 'number'): ?>
                  <input type="number" 
                         class="form-control" 
                         id="<?= htmlspecialchars($c['clave']) ?>" 
                         name="<?= htmlspecialchars($c['clave']) ?>" 
                         value="<?= htmlspecialchars($c['valor'] ?? '0') ?>">
                         
                <?php else: ?>
                  <input type="text" 
                         class="form-control" 
                         id="<?= htmlspecialchars($c['clave']) ?>" 
                         name="<?= htmlspecialchars($c['clave']) ?>" 
                         value="<?= htmlspecialchars($c['valor'] ?? '') ?>">
                <?php endif; ?>
                
                <small style="color:var(--adm-muted);font-size:.72rem;display:block;margin-top:.2rem">
                  Clave interna: <code><?= htmlspecialchars($c['clave']) ?></code>
                </small>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    
    <div class="col-lg-4">
      <div class="adm-card mb-3">
        <h2 class="adm-card-title">Acciones</h2>
        <p style="font-size:.8rem;color:var(--adm-muted)">Los cambios realizados aquí se reflejan de inmediato en todas las páginas públicas del sitio web de la SAZ.</p>
        <div class="d-grid gap-2">
          <button type="submit" class="btn-adm-primary btn"><i class="bi bi-save me-1"></i> Guardar ajustes</button>
          <a href="index.php" class="btn" style="background:rgba(255,255,255,.05);border:1px solid var(--adm-border);color:var(--adm-muted);border-radius:8px">Volver al inicio</a>
        </div>
      </div>
    </div>
  </div>
</form>

<?php $content = ob_get_clean(); include __DIR__ . '/base_admin.php';
