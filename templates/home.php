<?php
/**
 * templates/home.php
 * Página principal — incluye solo las secciones definitivas:
 *   1. Hero / portada
 *   2. 5 noticias recientes
 *   3. 3 astrofotografías recientes
 *   4. Instituciones colaboradoras (carrusel)
 *   5. 3 convocatorias (activas/inactivas)
 */

$root = __DIR__ . '/../';
?>

<?php include $root . 'partials/hero.php'; ?>
<?php include $root . 'partials/noticias-portada.php'; ?>
<?php include $root . 'partials/astrofotografia.php'; ?>
<?php include $root . 'partials/instituciones.php'; ?>
<?php include $root . 'partials/convocatorias.php'; ?>
