<?php

require_once __DIR__.'/../../services/TableService.php';
require_once __DIR__.'/../../utils.php';

$TableService = new TableService;

$tableRoutes = [
    'GET' => [
        'available' => availableTables(...),
    ],

];

function availableTables()
{
    global $TableService;
    $res = $TableService->GetAvailable();
    header($res['header']);
    echo json_encode($res['data']);
}

function tableHandler($verb, $uri)
{
    global $tableRoutes;

    return routeHandler($verb, $uri, $tableRoutes);
}
