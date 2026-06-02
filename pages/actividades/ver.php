<?php
// $actividadSlug puede ser pre-establecido por los wrappers estáticos (charlas.php, etc.)
// Si no está definido, se lee desde la URL.
if (!isset($actividadSlug)) {
    $actividadSlug = preg_replace('/[^a-z0-9\-]/', '', $_GET['slug'] ?? '');
}
if (!$actividadSlug) {
    header('Location: ../../index.php');
    exit;
}

$basePath = '../../';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/actividades.php';

$actividad = get_actividad_por_slug($actividadSlug);
if (!$actividad) {
    header('Location: ../../index.php');
    exit;
}

$pageTitle       = htmlspecialchars($actividad['titulo']) . ' — Sociedad Astronómica de Zacatecas';
$pageDescription = $actividad['descripcion'] ?? '';

$actividadTitulo      = $actividad['titulo'];
$actividadIcono       = $actividad['icono'];
$actividadDesc        = $actividad['descripcion'];
$actividadItems       = $actividad['items'];
$actividadImagenesDir = null;
$imagenes             = $actividad['imagenes'];

ob_start();
include __DIR__ . '/../../templates/actividad.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
