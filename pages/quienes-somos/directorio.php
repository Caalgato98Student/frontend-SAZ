<?php
/**
 * pages/quienes-somos/directorio.php
 * Directorio de miembros de la SAZ con tabla responsiva.
 */
$pageTitle       = 'Directorio — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Directorio de miembros de la Sociedad Astronomica de Zacatecas.';
$basePath        = '../../';

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Directorio</h1>
    <p class="mb-4">Informacion de contacto de los miembros y coordinadores de la Sociedad Astronomica de Zacatecas.</p>
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Nombre</th>
            <th>Cargo</th>
            <th>Especialidad</th>
            <th>Contacto</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>Dr. Carlos Mendez Ramirez</td><td>Presidente</td><td>Astrofisica estelar</td><td>cmendez@saz.org.mx</td></tr>
          <tr><td>Mtra. Ana Lucia Fernandez</td><td>Secretaria</td><td>Divulgacion cientifica</td><td>afernandez@saz.org.mx</td></tr>
          <tr><td>Ing. Roberto Salazar Nunez</td><td>Tesorero</td><td>Instrumentacion optica</td><td>rsalazar@saz.org.mx</td></tr>
          <tr><td>Lic. Patricia Herrera Gomez</td><td>Vocal de vinculacion</td><td>Gestion cultural</td><td>pherrera@saz.org.mx</td></tr>
          <tr><td>Mtro. Fernando Alanis Torres</td><td>Coord. de observaciones</td><td>Astronomia observacional</td><td>falanis@saz.org.mx</td></tr>
          <tr><td>Ing. Laura Patricia Vega</td><td>Coord. de astrofotografia</td><td>Imagen astronomica</td><td>lvega@saz.org.mx</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
