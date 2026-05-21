<?php

function get_instituciones_activas(): array
{
    $pdo = get_pdo();
    $sql = "SELECT * FROM instituciones WHERE activo = 1 ORDER BY orden, nombre";
    return $pdo->query($sql)->fetchAll();
}
