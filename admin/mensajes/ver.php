<?php
/**
 * admin/mensajes/ver.php — Ver un mensaje de contacto y marcarlo como leído.
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pdo = get_pdo();
$id  = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM mensajes_contacto WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$msg = $stmt->fetch();

if (!$msg) {
    header('Location: index.php');
    exit;
}

// Marcar como leído si no lo estaba
if (!$msg['leido']) {
    $pdo->prepare("UPDATE mensajes_contacto SET leido = 1 WHERE id = ?")->execute([$id]);
}

$pageTitle = 'Ver Mensaje: ' . ($msg['asunto'] ?: 'Sin Asunto');
$basePath  = '../../';
$csrf      = generate_csrf_token();

ob_start(); ?>

<div class="d-flex align-items-center gap-2 mb-3">
  <a href="index.php" style="color:var(--adm-muted);text-decoration:none;font-size:.9rem">
    <i class="bi bi-chevron-left"></i> Mensajes
  </a>
  <span style="color:var(--adm-border)">/</span>
  <span style="font-size:.9rem">Leer</span>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="adm-card">
      <div style="border-bottom: 1px solid var(--adm-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div>
            <h1 class="h4 fw-bold mb-1 text-white"><?= htmlspecialchars($msg['asunto'] ?: '(Sin asunto)') ?></h1>
            <div style="font-size:.875rem; color:var(--adm-muted)">
              De: <strong class="text-white"><?= htmlspecialchars($msg['nombre']) ?></strong> 
              &lt;<a href="mailto:<?= htmlspecialchars($msg['correo']) ?>" style="color:var(--adm-accent); text-decoration:none;"><?= htmlspecialchars($msg['correo']) ?></a>&gt;
            </div>
          </div>
          <div class="text-end">
            <span style="font-size:.8rem; color:var(--adm-muted)">
              <i class="bi bi-clock"></i> <?= date('d/m/Y H:i:s', strtotime($msg['creado_en'])) ?>
            </span>
          </div>
        </div>
      </div>

      <div style="font-size:.95rem; line-height:1.6; white-space:pre-wrap; color:rgba(255,255,255,0.95); min-height: 200px; padding: 1rem; border-radius: 8px; background: rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.02);">
        <?= htmlspecialchars($msg['mensaje']) ?>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="adm-card">
      <h2 class="adm-card-title">Acciones</h2>
      <div class="d-grid gap-2">
        <a href="mailto:<?= htmlspecialchars($msg['correo']) ?>?subject=RE: <?= rawurlencode($msg['asunto']) ?>" class="btn btn-adm-primary"><i class="bi bi-reply-fill me-1"></i> Responder por correo</a>
        
        <form method="POST" action="eliminar.php" class="w-100">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <input type="hidden" name="id" value="<?= $msg['id'] ?>">
          <button type="submit" class="btn btn-adm-danger btn-delete-confirm w-100" data-confirm="¿Estás seguro de que deseas eliminar este mensaje? Esta acción no se puede deshacer.">
            <i class="bi bi-trash me-1"></i> Eliminar mensaje
          </button>
        </form>
        
        <a href="index.php" class="btn" style="background:rgba(255,255,255,.05);border:1px solid var(--adm-border);color:var(--adm-muted);border-radius:8px">Volver al buzón</a>
      </div>
    </div>
  </div>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../base_admin.php';
