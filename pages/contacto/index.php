<?php
/**
 * pages/contacto/index.php
 * Página de contacto: formulario, redes sociales, mapa.
 */
$pageTitle       = 'Contacto — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Contacta a la Sociedad Astronomica de Zacatecas. Formulario, redes sociales y ubicacion.';
$basePath        = '../../';

ob_start();
?>

<section class="py-5">
  <div class="container">
    <h1 class="section-title mb-4">Contacto</h1>
    <div class="row g-4">

      <!-- Columna del formulario -->
      <div class="col-lg-7">
        <div class="surface-card h-100">
          <h2 class="h5 mb-3"><i class="bi bi-chat-dots me-2"></i>Enviar mensaje</h2>
          <form class="row g-3" method="post" action="">
            <div class="col-md-6">
              <label for="ct-nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="ct-nombre" name="nombre" placeholder="Tu nombre" required>
            </div>
            <div class="col-md-6">
              <label for="ct-correo" class="form-label">Correo electronico <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="ct-correo" name="correo" placeholder="correo@ejemplo.com" required>
            </div>
            <div class="col-12">
              <label for="ct-asunto" class="form-label">Asunto</label>
              <input type="text" class="form-control" id="ct-asunto" name="asunto" placeholder="Asunto del mensaje">
            </div>
            <div class="col-12">
              <label for="ct-mensaje" class="form-label">Mensaje <span class="text-danger">*</span></label>
              <textarea id="ct-mensaje" name="mensaje" class="form-control" rows="5" placeholder="Escribe tu mensaje" required></textarea>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i>Enviar mensaje
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Columna de información -->
      <div class="col-lg-5">
        <div class="surface-card mb-4">
          <h2 class="h5 mb-3"><i class="bi bi-info-circle me-2"></i>Informacion de contacto</h2>
          <p class="mb-2"><i class="bi bi-envelope me-2"></i><strong>Correo:</strong> contacto@saz.org.mx</p>
          <p class="mb-2"><i class="bi bi-telephone me-2"></i><strong>Telefono:</strong> +52 492 000 0000</p>
          <p class="mb-0"><i class="bi bi-geo-alt me-2"></i><strong>Direccion:</strong> Zacatecas, Zacatecas, Mexico</p>
        </div>

        <div class="surface-card mb-4">
          <h2 class="h5 mb-3"><i class="bi bi-share me-2"></i>Redes sociales</h2>
          <div class="d-flex gap-3">
            <a href="#" class="btn btn-outline-primary" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
              <i class="bi bi-facebook me-1"></i>Facebook
            </a>
            <a href="#" class="btn btn-outline-danger" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
              <i class="bi bi-instagram me-1"></i>Instagram
            </a>
          </div>
        </div>

        <div class="surface-card">
          <h2 class="h5 mb-3"><i class="bi bi-geo-alt me-2"></i>Ubicacion</h2>
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29068.03782367076!2d-102.58324!3d22.7711!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x86824ebbf47eaaa5%3A0x2c96536bfa1fe2ec!2sZacatecas%2C%20Zac.%2C%20Mexico!5e0!3m2!1ses!2smx!4v1680000000000!5m2!1ses!2smx"
            width="100%" height="250" style="border:0; border-radius: 0.75rem;"
            allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Ubicacion de la SAZ en Zacatecas">
          </iframe>
        </div>
      </div>

    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
