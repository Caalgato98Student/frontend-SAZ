<?php
$pageTitle       = 'Diplomados — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Diplomados en astronomia y ciencias afines ofrecidos por la SAZ.';
$basePath        = '../../';
$actividadTitulo = 'Diplomados';
$actividadIcono  = 'bi bi-mortarboard';
$actividadDesc   = 'Programas academicos de mayor profundidad y duracion, con reconocimiento institucional, dirigidos a profesionales y docentes.';
$actividadItems  = [
    ['titulo' => 'Diplomado en astronomia general', 'descripcion' => 'Programa de 6 meses que abarca fundamentos de astrofisica, instrumentacion y divulgacion cientifica.'],
    ['titulo' => 'Diplomado en didactica de la astronomia', 'descripcion' => 'Orientado a docentes. Estrategias y recursos para ensenar astronomia en el aula.'],
    ['titulo' => 'Certificacion en observacion astronomica', 'descripcion' => 'Programa intensivo con evaluacion practica en manejo de telescopios y tecnicas observacionales.'],
    ['titulo' => 'Proximos diplomados', 'descripcion' => 'La SAZ esta desarrollando nuevos programas en colaboracion con universidades de la region. Consulta esta pagina para actualizaciones.'],
];

ob_start();
include __DIR__ . '/../../templates/actividad.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
