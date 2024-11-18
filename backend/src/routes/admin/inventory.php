<?php

require_once __DIR__.'/../../services/InventoryService.php';
require_once __DIR__.'/../../utils.php';

$InventoryService = new InventoryService;

$inventoryRoutes = [
    'GET' => [
        'all' => inventoryAll(...),
        'id' => inventoryById(...),
        'low-stock' => inventoryLowStock(...),
    ],
    'PUT' => [
        'restock' => inventoryRestock(...),
    ],
];

function inventoryAll()
{
    global $InventoryService;
    $res = $InventoryService->GetAll();
    header($res['header']);
    echo json_encode($res['data']);
}

function inventoryById($id)
{
    global $InventoryService;
    $res = $InventoryService->GetById($id);
    header($res['header']);
    echo json_encode($res['data']);
}

function inventoryLowStock()
{
    global $InventoryService;
    $res = $InventoryService->GetLowStock();
    header($res['header']);
    echo json_encode($res['data']);
}

function inventoryRestock($data)
{
    global $InventoryService;
    if (validateData(['id', 'quantity'], $data, 'restock')) {
        try {
            $quantInt = intval($data['quantity']);
        } catch (Exception $e) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Invalid quantity data']);
        }
        $res = $InventoryService->Restock($data['id'], $quantInt);
        header($res['header']);
        echo json_encode($res['data']);
    }

}

function inventoryHandler($verb, $uri)
{
    global $inventoryRoutes;

    return routeHandler($verb, $uri, $inventoryRoutes);
}
