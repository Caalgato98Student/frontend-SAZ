<?php
if (!function_exists('get_config')) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/repositories/configuracion.php';
}
$_footerFacebook        = get_config('social_facebook')             ?? 'https://www.facebook.com/SAZacatecas';
$_footerInstagram       = get_config('social_instagram')            ?? 'https://www.instagram.com/sazacatecas/';
$_footerX               = get_config('social_x')                    ?? '';
$_footerLavnetUrl       = get_config('footer_lavnet_url')           ?? 'http://gipimo.ddns.net:8000/lavnet-zac/';
$_footerLavnetNombre    = get_config('footer_lavnet_nombre')        ?? 'LavNet-Zac-Mx';
$_footerCopyright       = get_config('footer_copyright')            ?? 'Hecho en México. Sociedad Astronómica de Zacatecas, todos los derechos reservados';
$_footerTransparencia   = get_config('footer_transparencia_url')    ?? '#';
$_footerPrivacidad      = get_config('footer_aviso_privacidad_url') ?? '#';
?>
<!-- ============================================================
     partials/footer.php
     ============================================================ -->
<footer id="footer" class="pt-5 pb-4">
  <div class="container">

    <!-- ── Links principales + redes sociales ── -->
    <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 mb-4">
      <a href="<?= $basePath ?>pages/suscribirse/index.php" class="footer-link">
        <i class="bi bi-envelope-plus me-1"></i>Suscribirse
      </a>
      <a href="<?= $basePath ?>pages/contacto/index.php" class="footer-link">
        <i class="bi bi-chat-dots me-1"></i>Contacto
      </a>
      <a href="<?= htmlspecialchars($_footerLavnetUrl) ?>" class="footer-link" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars($_footerLavnetNombre) ?>">
        <i class="bi bi-globe me-1"></i><?= htmlspecialchars($_footerLavnetNombre) ?>
      </a>
      <?php if (!empty($_footerFacebook)): ?>
      <a href="<?= htmlspecialchars($_footerFacebook) ?>" class="footer-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook de la Sociedad Astronómica de Zacatecas">
        <i class="bi bi-facebook me-1"></i>Facebook
      </a>
      <?php endif; ?>
      <?php if (!empty($_footerInstagram)): ?>
      <a href="<?= htmlspecialchars($_footerInstagram) ?>" class="footer-link" target="_blank" rel="noopener noreferrer" aria-label="Instagram de la Sociedad Astronómica de Zacatecas">
        <i class="bi bi-instagram me-1"></i>Instagram
      </a>
      <?php endif; ?>
      <?php if (!empty($_footerX)): ?>
      <a href="<?= htmlspecialchars($_footerX) ?>" class="footer-link" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter) de la Sociedad Astronómica de Zacatecas">
        <i class="bi bi-twitter-x me-1"></i>X
      </a>
      <?php endif; ?>
    </div>

    <!-- ── Copyright ── -->
    <div class="text-center border-top border-secondary-subtle pt-3">
      <p class="mb-1 small"><?= htmlspecialchars($_footerCopyright) ?> <span id="currentYear"></span>.</p>
      <div class="d-flex justify-content-center gap-3 small">
        <a href="<?= htmlspecialchars($_footerTransparencia) ?>" class="footer-link">Transparencia</a>
        <a href="<?= htmlspecialchars($_footerPrivacidad) ?>" class="footer-link" aria-label="Aviso de privacidad de la SAZ">Aviso de privacidad</a>
      </div>
    </div>

  </div>
</footer>
