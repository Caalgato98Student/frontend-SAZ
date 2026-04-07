<?php
/**
 * pages/suscribirse/index.php
 * Formulario de suscripción: nombre, correo, teléfono, área de interés, mensaje.
 */
$pageTitle       = 'Suscribirse — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Unete a la Sociedad Astronomica de Zacatecas. Completa el formulario de suscripcion.';
$basePath        = '../../';

ob_start();
?>

<section class="py-5">
  <div class="container">
    <div class="col-lg-8 mx-auto">
      <div class="text-center mb-5">
        <i class="bi bi-envelope-plus text-primary" style="font-size: 3rem;"></i>
        <h1 class="section-title mt-3">Suscribirse</h1>
        <p class="lead">Unete a la SAZ y recibe informacion sobre eventos, actividades y oportunidades de colaboracion.</p>
      </div>

      <div class="surface-card">
        <form class="row g-3" method="post" action="">
          <div class="col-md-6">
            <label for="sub-nombre" class="form-label">Nombre completo <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="sub-nombre" name="nombre" placeholder="Tu nombre completo" required>
          </div>
          <div class="col-md-6">
            <label for="sub-correo" class="form-label">Correo electronico <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="sub-correo" name="correo" placeholder="correo@ejemplo.com" required>
          </div>
          <div class="col-md-6">
            <label for="sub-telefono" class="form-label">Telefono</label>
            <input type="tel" class="form-control" id="sub-telefono" name="telefono" placeholder="(000) 000 0000">
          </div>
          <div class="col-md-6">
            <label for="sub-interes" class="form-label">Area de interes</label>
            <select id="sub-interes" name="interes" class="form-select">
              <option selected disabled>Selecciona una opcion</option>
              <option>Divulgacion</option>
              <option>Astrofotografia</option>
              <option>Observacion</option>
              <option>Investigacion</option>
              <option>Educacion</option>
            </select>
          </div>
          <div class="col-12">
            <label for="sub-mensaje" class="form-label">Mensaje</label>
            <textarea id="sub-mensaje" name="mensaje" class="form-control" rows="4" placeholder="Cuentanos por que deseas unirte o cualquier informacion adicional"></textarea>
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-send me-1"></i>Enviar solicitud
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
