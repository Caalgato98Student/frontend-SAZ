<?php
$pageTitle       = 'Cursos — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Cursos de formacion en astronomia ofrecidos por la SAZ.';
$basePath        = '../../';
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/repositories/actividades.php';

$actividad = get_actividad_por_slug('cursos');

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
