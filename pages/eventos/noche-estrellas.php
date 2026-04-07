<?php
$pageTitle       = 'Noche de las Estrellas — Sociedad Astronomica de Zacatecas';
$pageDescription = 'Noche de las Estrellas: evento anual de observacion astronomica abierto al publico.';
$basePath        = '../../';
$eventoSlug      = 'noche-estrellas';

ob_start();
include __DIR__ . '/../../templates/evento.php';
$content = ob_get_clean();
include __DIR__ . '/../../base.php';
