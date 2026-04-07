<?php
$pageTitle       = 'Inicio — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Portal de noticias y actividades de la Sociedad Astronomica de Zacatecas.';
$basePath        = '';

ob_start();
include __DIR__ . '/templates/home.php';
$content = ob_get_clean();

include __DIR__ . '/base.php';
