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
          <tr><td>M.C. Iván Santamaría Najar</td><td>Presidente</td><td>Maestro en Ciencias</td><td>sazac2010@gmail.com</td></tr>
          <tr><td>M.C. Ciro Robles Berumen</td><td>Secretario</td><td>Maestro en Ciencias</td><td>cirorobles2405@gmail.com</td></tr>
          <tr><td>L.E. Armando García Castillo</td><td>Tesorero</td><td>Licenciado en Economía</td><td>garcia.a.castillo@gmail.com</td></tr>
          <tr><td>Dr. Alejandro González Sánchez</td><td>Consejo Consultivo</td><td>Doctor en Astronomía</td><td>alejandro.gonzalez@uaz.edu.mx</td></tr>
          <tr><td>Berenice Gómez Martínez</td><td>Consejo Consultivo</td><td>Divulgadora</td><td>Berebankrobber@gmail.com</td></tr>
          <tr><td>Ing. Víctor Alejandro Rafael Muñoz Suárez</td><td>Consejo Consultiva</td><td>Ingeniero </td><td>geovector2010@gmail.com</td></tr>
          <tr><td>M.L.M. Corina Bobadilla Larios</td><td>Consejo de Vigilancia</td><td>Maestro en Lengua Materna</td><td>sazac2010@gmail.com</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
