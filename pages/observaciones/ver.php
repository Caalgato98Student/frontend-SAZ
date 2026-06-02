<?php
// $observacionSlug puede ser pre-establecido por los wrappers estáticos (diurna.php, etc.)
// Si no está definido, se lee desde la URL.
if (!isset($observacionSlug)) {
    $observacionSlug = preg_replace('/[^a-z0-9\-]/', '', $_GET['slug'] ?? '');
}
if (!$observacionSlug) {
    header('Location: ../../index.php');
    exit;
}

$basePath = '../../';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/observaciones.php';

$observacion = get_observacion_por_slug($observacionSlug);
if (!$observacion) {
    header('Location: ../../index.php');
    exit;
}

$pageTitle       = htmlspecialchars($observacion['titulo']) . ' — Sociedad Astronómica de Zacatecas';
$pageDescription = $observacion['descripcion_intro'] ?? '';

ob_start();
include __DIR__ . '/../../templates/observacion.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
