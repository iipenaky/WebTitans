<?php

require_once __DIR__.'/../../services/MenuItemService.php';
require_once __DIR__.'/../../utils.php';

$MenuItemService = new MenuItemService;

$menuItemRoutes = [
    'GET' => [
        'all' => menuItemsAll(...),
        'id' => menuItemById(...),
    ],
];

function menuItemsAll()
{
    global $MenuItemService;
    $res = $MenuItemService->GetAll();
    header($res['header']);
    echo json_encode($res['data']);
}

function menuItemById($id)
{
    global $MenuItemService;
    $res = $MenuItemService->GetById($id);
    header($res['header']);
    echo json_encode($res['data']);
}

function menuItemHandler($verb, $uri)
{
    global $menuItemRoutes;

    return routeHandler($verb, $uri, $menuItemRoutes);
}
