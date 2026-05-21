<?php
// $eventoSlug puede ser pre-establecido por los wrappers estáticos (semanadelaastronomia.php, etc.)
// Si no está definido, se lee desde la URL.
if (!isset($eventoSlug)) {
    $eventoSlug = preg_replace('/[^a-z0-9\-]/', '', $_GET['slug'] ?? '');
}
if (!$eventoSlug) {
    header('Location: ../../index.php');
    exit;
}

$basePath = '../../';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/eventos.php';

$evento = get_evento_completo($eventoSlug);
if (!$evento) {
    header('Location: ../../index.php');
    exit;
}

$pageTitle       = htmlspecialchars($evento['titulo']) . ' — Sociedad Astronómica de Zacatecas';
$pageDescription = strip_tags($evento['descripcion'] ?? '');

ob_start();
include __DIR__ . '/../../templates/evento.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
