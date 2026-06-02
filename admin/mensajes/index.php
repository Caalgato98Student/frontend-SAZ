<?php
/**
 * admin/mensajes/index.php — Bandeja de entrada de mensajes de contacto.
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Mensajes de Contacto';
$basePath  = '../../';
$pdo       = get_pdo();
$items     = $pdo->query("SELECT id, nombre, correo, asunto, leido, creado_en FROM mensajes_contacto ORDER BY creado_en DESC")->fetchAll();
$msg       = $_GET['msg'] ?? '';

ob_start(); ?>

<?php if ($msg === 'eliminado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Mensaje eliminado correctamente.</div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <h1 class="h4 fw-bold mb-0">Mensajes de Contacto</h1>
</div>

<div class="adm-card mb-3" style="font-size:.85rem;color:var(--adm-muted)">
  <i class="bi bi-info-circle me-1"></i>
  Bandeja de entrada con las consultas enviadas por los usuarios desde la página de contacto pública. Los mensajes no leídos se muestran destacados.
</div>

<div class="adm-card">
  <?php if ($items): ?>
    <div class="table-responsive">
      <table class="adm-table">
        <thead>
          <tr>
            <th style="width: 50px;">Estado</th>
            <th>Remitente</th>
            <th>Asunto</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $m): ?>
            <tr style="<?= !$m['leido'] ? 'background: rgba(59,130,246,0.03);' : '' ?>">
              <td>
                <?php if (!$m['leido']): ?>
                  <span class="badge" style="background:#3b82f6; color:#fff; font-size:.7rem; padding:.2rem .4rem;">Nuevo</span>
                <?php else: ?>
                  <span style="color:var(--adm-muted); font-size:.78rem;"><i class="bi bi-envelope-open"></i></span>
                <?php endif; ?>
              </td>
              <td class="<?= !$m['leido'] ? 'fw-bold text-white' : '' ?>" style="font-size:.9rem">
                <?= htmlspecialchars($m['nombre']) ?><br>
                <small style="color:var(--adm-muted); font-weight:normal;"><?= htmlspecialchars($m['correo']) ?></small>
              </td>
              <td class="<?= !$m['leido'] ? 'fw-bold text-white' : '' ?>" style="font-size:.9rem">
                <?= htmlspecialchars($m['asunto'] ?: '(Sin asunto)') ?>
              </td>
              <td style="color:var(--adm-muted); font-size:.85rem">
                <?= date('d/m/Y H:i', strtotime($m['creado_en'])) ?>
              </td>
              <td>
                <div class="d-flex gap-2">
                  <a href="ver.php?id=<?= $m['id'] ?>" class="btn btn-sm"
                     style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:6px;padding:.25rem .65rem">
                    <i class="bi bi-eye"></i> Leer
                  </a>
                  <form method="POST" action="eliminar.php" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm"
                            style="padding:.25rem .65rem"
                            data-confirm="¿Eliminar este mensaje permanentemente?">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="mb-0" style="color:var(--adm-muted)">No hay mensajes en la bandeja de entrada.</p>
  <?php endif; ?>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../base_admin.php';
