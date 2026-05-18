<?php
/**
 * pages/quienes-somos/directorio.php
 * Directorio de miembros de la SAZ con tabla responsiva.
 */
$pageTitle       = 'Directorio — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Directorio de miembros de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/miembros.php';
$miembros = get_miembros_directorio();

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Directorio</h1>
    <p class="mb-4">Informacion de contacto de los miembros y coordinadores de la Sociedad Astronomica de Zacatecas.</p>
    <div class="table-responsive" tabindex="0" role="group" aria-labelledby="caption-id">
      <table class="table table-striped table-hover align-middle">
        <caption>Directorio oficial de los miembros y coordinadores de la SAZ</caption>
        <thead class="table-light">
          <tr>
              <th scope="col">Nombre</th>
              <th scope="col">Cargo</th>
              <th scope="col">Especialidad</th>
              <th scope="col">Contacto</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($miembros as $m): ?>
          <tr>
            <td><?= htmlspecialchars($m['nombre']) ?></td>
            <td><?= htmlspecialchars($m['cargo'] ?? '—') ?></td>
            <td><?= htmlspecialchars($m['especialidad'] ?? '—') ?></td>
            <td><?= htmlspecialchars($m['correo'] ?? '—') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
