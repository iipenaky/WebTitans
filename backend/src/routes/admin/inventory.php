<?php

require_once __DIR__ . "/../../services/InventoryService.php";
require_once __DIR__ . "/../../utils.php";

$InventoryService = new InventoryService();

$inventoryRoutes = array(
"GET" => array(
"all" => inventoryAll(...),
"id" => inventoryById(...),
"low-stock" => inventoryLowStock(...)
),
);

function inventoryAll()
{
    global $InventoryService;
    $res = $InventoryService->GetAll();
    header($res["header"]);
    echo json_encode($res["data"]);
}

function inventoryById($id)
{
    global $InventoryService;
    $res = $InventoryService->GetById($id);
    header($res["header"]);
    echo json_encode($res["data"]);
}

function inventoryLowStock()
{
    global $InventoryService;
    $res = $InventoryService->GetLowStock();
    header($res["header"]);
    echo json_encode($res["data"]);
}


function inventoryHandler($verb, $uri)
{
    global $inventoryRoutes;
    return routeHandler($verb, $uri, $inventoryRoutes);
}
