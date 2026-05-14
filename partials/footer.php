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
      <a href="http://gipimo.ddns.net:8000/lavnet-zac/" class="footer-link" target="_blank" rel="noopener noreferrer" aria-label="LavNet Zac Mx">
        <i class="bi bi-globe me-1"></i>LavNet-Zac-Mx
      </a>
      <a href="https://www.facebook.com/SAZacatecas" class="footer-link" target="_blank" rel="noopener noreferrer" aria-label="Facebook de la Sociedad Astronómica de Zacatecas">
        <i class="bi bi-facebook me-1"></i>Facebook
      </a>
      <a href="https://www.instagram.com/sazacatecas/" class="footer-link" target="_blank" rel="noopener noreferrer" aria-label="Instagram de la Sociedad Astronómica de Zacatecas">
        <i class="bi bi-instagram me-1"></i>Instagram
      </a>
    </div>

    <!-- ── Copyright ── -->
    <div class="text-center border-top border-secondary-subtle pt-3">
      <p class="mb-1 small">Hecho en Mexico. Sociedad Astronomica de Zacatecas, todos los derechos reservados <span id="currentYear"></span>.</p>
      <div class="d-flex justify-content-center gap-3 small">
        <a href="#" class="footer-link">Transparencia</a>
        <a href="#" class="footer-link" aria-label="Aviso de privacidad de la SAZ">Aviso de privacidad</a>
      </div>
    </div>

  </div>
</footer>
