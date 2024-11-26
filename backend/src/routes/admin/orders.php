<?php

require_once __DIR__.'/../../services/OrderService.php';
require_once __DIR__.'/../../utils.php';

$OrderService = new OrderService;

$orderRoutes = [
    'GET' => [
        'all' => ordersAll(...),
        'id' => ordersByCustomerId(...),
        'num' => numOrders(...),
    ],

    'DELETE' => [
        'deleteById' => orderDeleteById(...),
    ],

];

function numOrders()
{
    global $OrderService;
    $res = $OrderService->GetNumPendingOrders();
    header($res['header']);
    echo json_encode($res['data']);
}

function orderDeleteById($id)
{
    global $OrderService;
    $res = $OrderService->Delete($id);
    header($res['header']);
    echo json_encode($res['data']);

}

function ordersAll()
{
    global $OrderService;
    $res = $OrderService->GetAll();
    header($res['header']);
    echo json_encode($res['data']);
}

function ordersByCustomerId($id)
{
    global $OrderService;
    $res = $OrderService->GetByCustomerId($id);
    header($res['header']);
    echo json_encode($res['data']);

}

function ordersHandler($verb, $uri)
{
    global $orderRoutes;

    return routeHandler($verb, $uri, $orderRoutes);
}
