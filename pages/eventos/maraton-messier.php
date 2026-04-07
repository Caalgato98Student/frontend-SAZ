<?php
$pageTitle       = 'Maraton Messier — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Maraton Messier: reto observacional para localizar los 110 objetos del catalogo Messier.';
$basePath        = '../../';
$eventoSlug      = 'maraton-messier';

ob_start();
include __DIR__ . '/../../templates/evento.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
