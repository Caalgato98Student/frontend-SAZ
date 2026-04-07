<?php
$pageTitle       = 'Charlas — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Charlas informales y sesiones de preguntas sobre astronomia.';
$basePath        = '../../';
$actividadTitulo = 'Charlas';
$actividadIcono  = 'bi bi-chat-left-text';
$actividadDesc   = 'Sesiones informales de conversacion e intercambio de conocimientos entre miembros de la SAZ y el publico interesado.';
$actividadItems  = [
    ['titulo' => 'Charlas de cafe astronomico', 'descripcion' => 'Reuniones informales donde se discuten noticias recientes de astronomia en un ambiente relajado.'],
    ['titulo' => 'Noches de preguntas', 'descripcion' => 'Sesiones abiertas donde el publico puede hacer preguntas a los miembros sobre cualquier tema astronomico.'],
    ['titulo' => 'Charlas para escuelas', 'descripcion' => 'Presentaciones adaptadas para estudiantes de primaria, secundaria y bachillerato.'],
    ['titulo' => 'Charlas con invitados', 'descripcion' => 'Sesiones especiales con astronomos y cientificos invitados de otras instituciones.'],
];

ob_start();
include __DIR__ . '/../../templates/actividad.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
