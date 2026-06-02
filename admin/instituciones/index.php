<?php
/**
 * admin/instituciones/index.php — Listado de instituciones colaboradoras.
 */
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../auth.php';
require_admin_auth();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';

$pageTitle = 'Instituciones Colaboradoras';
$basePath  = '../../';
$pdo       = get_pdo();
$items     = $pdo->query("SELECT id, nombre, imagen, url, orden, activo FROM instituciones ORDER BY orden, nombre")->fetchAll();
$msg       = $_GET['msg'] ?? '';

ob_start(); ?>

<?php if ($msg === 'creado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Institución creada correctamente.</div>
<?php elseif ($msg === 'editado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Institución actualizada correctamente.</div>
<?php elseif ($msg === 'eliminado'): ?>
  <div class="alert-adm alert-adm-success"><i class="bi bi-check-circle-fill"></i> Institución eliminada correctamente.</div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <h1 class="h4 fw-bold mb-0">Instituciones Colaboradoras</h1>
  <a href="crear.php" class="btn-adm-primary btn"><i class="bi bi-plus-lg me-1"></i> Nueva institución</a>
</div>

<div class="adm-card mb-3" style="font-size:.85rem;color:var(--adm-muted)">
  <i class="bi bi-info-circle me-1"></i>
  Administra las universidades, centros de investigación y organizaciones de divulgación científica con las que colabora la SAZ.
</div>

<div class="adm-card">
  <?php if ($items): ?>
    <div class="table-responsive">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Logo</th>
            <th>Nombre</th>
            <th>Enlace (URL)</th>
            <th>Orden</th>
            <th>Activa</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $inst): ?>
            <tr>
              <td>
                <?php if ($inst['imagen'] && file_exists(__DIR__ . '/../../assets/img/instituciones/' . $inst['imagen'])): ?>
                  <img src="../../assets/img/instituciones/<?= htmlspecialchars($inst['imagen']) ?>" alt="<?= htmlspecialchars($inst['nombre']) ?>" style="height:40px; max-width:120px; object-fit:contain; background:rgba(255,255,255,0.05); padding:2px; border-radius:4px; border:1px solid var(--adm-border);">
                <?php else: ?>
                  <span style="font-size:.78rem;color:var(--adm-muted)">Sin logo</span>
                <?php endif; ?>
              </td>
              <td class="fw-semibold"><?= htmlspecialchars($inst['nombre']) ?></td>
              <td>
                <?php if ($inst['url']): ?>
                  <a href="<?= htmlspecialchars($inst['url']) ?>" target="_blank" style="color:var(--adm-accent); text-decoration:none; font-size:.85rem;">
                    <?= htmlspecialchars(mb_strimwidth($inst['url'], 0, 40, '...')) ?> <i class="bi bi-box-arrow-up-right" style="font-size:.75rem"></i>
                  </a>
                <?php else: ?>
                  <span style="color:var(--adm-muted); font-size:.85rem">-</span>
                <?php endif; ?>
              </td>
              <td style="color:var(--adm-muted); font-size:.85rem"><?= $inst['orden'] ?></td>
              <td>
                <?= $inst['activo'] 
                  ? '<i class="bi bi-check-circle-fill" style="color:#4ade80"></i>' 
                  : '<i class="bi bi-dash-circle" style="color:var(--adm-border)"></i>' 
                ?>
              </td>
              <td>
                <div class="d-flex gap-2">
                  <a href="editar.php?id=<?= $inst['id'] ?>" class="btn btn-sm"
                     style="background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);color:#60a5fa;border-radius:6px;padding:.25rem .65rem">
                    <i class="bi bi-pencil"></i> Editar
                  </a>
                  <form method="POST" action="eliminar.php" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= $inst['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-adm-danger btn-delete-confirm"
                            style="padding:.25rem .65rem"
                            data-confirm="¿Eliminar la institución «<?= htmlspecialchars($inst['nombre'], ENT_QUOTES) ?>»? Esta acción no se puede deshacer.">
                      <i class="bi bi-trash"></i> Eliminar
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
    <p class="mb-0" style="color:var(--adm-muted)">No hay instituciones registradas aún.</p>
  <?php endif; ?>
</div>

<?php $content = ob_get_clean(); include __DIR__ . '/../base_admin.php';
