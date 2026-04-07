<?php
$pageTitle       = 'Cursos — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Cursos de formacion en astronomia ofrecidos por la SAZ.';
$basePath        = '../../';
$actividadTitulo = 'Cursos';
$actividadIcono  = 'bi bi-book';
$actividadDesc   = 'Programas de formacion estructurados con duracion de varias semanas, dirigidos a diferentes niveles de conocimiento.';
$actividadItems  = [
    ['titulo' => 'Astronomia basica', 'descripcion' => 'Curso introductorio de 8 semanas que cubre el sistema solar, estrellas, galaxias y cosmologia.'],
    ['titulo' => 'Mecanica celeste', 'descripcion' => 'Estudio del movimiento de cuerpos celestes. Requiere conocimientos basicos de fisica y matematicas.'],
    ['titulo' => 'Astrofotografia intermedia', 'descripcion' => 'Tecnicas avanzadas de captura y procesamiento de imagen astronomica con equipo dedicado.'],
    ['titulo' => 'Historia de la astronomia', 'descripcion' => 'Recorrido historico desde las civilizaciones antiguas hasta los descubrimientos mas recientes.'],
];

ob_start();
include __DIR__ . '/../../templates/actividad.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
