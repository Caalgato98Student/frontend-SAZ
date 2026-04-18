<!-- ============================================================
     partials/instituciones.php
     Carrusel de instituciones colaboradoras.
     Cambio automático cada 4 segundos, con flechas y puntos.
     ============================================================ -->
<section id="instituciones-colaboradoras" class="py-5 section-alt">
  <div class="container">
    <h2 class="section-title mb-3">Instituciones con las que colaboramos</h2>
    <p>Colaboramos con universidades, centros de investigacion y organizaciones de divulgacion cientifica a nivel regional, nacional e internacional.</p>

    <div id="institucionesCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
      <!-- Indicadores (dots) -->
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#institucionesCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#institucionesCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#institucionesCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
      </div>

      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="placeholder-image placeholder-hero">
            <i class="bi bi-building" style="font-size: 2.5rem;"></i>
            <p class="mt-2">Universidad Autonoma de Zacatecas</p>
          </div>
        </div>
        <div class="carousel-item">
          <div class="placeholder-image placeholder-hero">
            <i class="bi bi-building" style="font-size: 2.5rem;"></i>
            <p class="mt-2">Instituto Politecnico Nacional</p>
          </div>
        </div>
        <div class="carousel-item">
          <div class="placeholder-image placeholder-hero">
            <i class="bi bi-building" style="font-size: 2.5rem;"></i>
            <p class="mt-2">Consejo Zacatecano de Ciencia y Tecnologia</p>
          </div>
        </div>
      </div>

      <!-- Controles prev/next -->
      <button class="carousel-control-prev" type="button" data-bs-target="#institucionesCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#institucionesCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
      </button>
    </div>
  </div>
</section>
