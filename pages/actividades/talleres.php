<?php
$pageTitle       = 'Talleres — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Talleres practicos de astronomia e instrumentacion organizados por la SAZ.';
$basePath        = '../../';
$actividadTitulo = 'Talleres';
$actividadIcono  = 'bi bi-tools';
$actividadDesc   = 'Actividades practicas orientadas a desarrollar habilidades en observacion, instrumentacion y procesamiento de datos astronomicos.';
$actividadItems  = [
    ['titulo' => 'Construccion de telescopios', 'descripcion' => 'Taller practico para construir un telescopio refractor basico con materiales accesibles.'],
    ['titulo' => 'Fotografia nocturna', 'descripcion' => 'Introduccion a la captura de imagenes del cielo con camaras DSLR y smartphones.'],
    ['titulo' => 'Procesamiento de imagenes', 'descripcion' => 'Uso de software libre para apilar y procesar fotografias astronomicas.'],
    ['titulo' => 'Orientacion celeste', 'descripcion' => 'Aprende a localizar constelaciones, planetas y objetos de cielo profundo con y sin telescopio.'],
];

ob_start();
include __DIR__ . '/../../templates/actividad.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
