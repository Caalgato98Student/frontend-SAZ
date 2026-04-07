<!doctype html>
<html lang="es" data-bs-theme="light">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'Sociedad Astronomica de Zacatecas') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription ?? 'Portal de noticias y actividades de la Sociedad Astronomica de Zacatecas.') ?>">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts — Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= $basePath ?>assets/img/logo-SAZ.png">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/styles.css">
  </head>
  <body>
    <a class="visually-hidden-focusable skip-link" href="#contenido-principal">Saltar al contenido principal</a>

    <?php include __DIR__ . '/partials/header.php'; ?>

    <main id="contenido-principal">
      <?= $content ?? '' ?>
    </main>

    <?php include __DIR__ . '/partials/footer.php'; ?>

    <button type="button" id="scrollTopBtn" class="btn btn-primary scroll-top-btn" aria-label="Volver arriba">
      <i class="bi bi-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= $basePath ?>assets/js/main.js"></script>
  </body>
</html>
