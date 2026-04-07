<?php
$pageTitle       = 'Conferencias — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Conferencias de divulgacion astronomica organizadas por la SAZ.';
$basePath        = '../../';
$actividadTitulo = 'Conferencias';
$actividadIcono  = 'bi bi-easel';
$actividadDesc   = 'Sesiones magistrales impartidas por especialistas en astronomia, astrofisica y ciencias afines. Abiertas al publico general.';
$actividadItems  = [
    ['titulo' => 'Conferencias de divulgacion', 'descripcion' => 'Charlas accesibles para publico no especializado sobre temas de actualidad en astronomia y ciencias del espacio.'],
    ['titulo' => 'Conferencias especializadas', 'descripcion' => 'Presentaciones tecnicas dirigidas a estudiantes y profesionales del area con temas de investigacion avanzada.'],
    ['titulo' => 'Ciclos tematicos', 'descripcion' => 'Series de conferencias organizadas alrededor de un tema central, como cosmologia, exoplanetas o historia de la astronomia.'],
    ['titulo' => 'Conferencias virtuales', 'descripcion' => 'Transmisiones en linea que permiten la participacion de publico a nivel nacional e internacional.'],
];

ob_start();
include __DIR__ . '/../../templates/actividad.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
