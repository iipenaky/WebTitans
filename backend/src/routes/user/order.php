<?php

require_once __DIR__ . '/../../services/OrderService.php';
require_once __DIR__ . '/../../utils.php';

$OrderService = new OrderService;
$orderFields = ['order_id', 'customer_id'];
$orderDetailsFields = ['order_detail_id', 'menu_item_id', 'quantity'];

function validateOrderDetails($data)
{
    global $orderDetailsFields;
    foreach ($data as $orderDetail) {
        if (! validateNewData($orderDetailsFields, $orderDetail, 'order detail')) {
            return false;
        }
    }

    return true;
}

function validateNewOrder($data)
{
    if (! isset($data['order'])) {
        return false;
    }
    if (! isset($data['order_details'])) {
        return false;
    }

    global $orderFields;

    return validateNewData($orderFields, $data['order'], 'order') && validateOrderDetails($data['order_details']);
}

function validateOrder($data)
{
    global $orderFields;

    return validateData($orderFields, $data, 'order');
}

$userOrderRoutes = [
    'GET' => [
        'id' => ordersByCustomer(...),
        'menu' => menuItems(...),
    ],

    'POST' => [
        'add' => makeOrder(...),
        'cancel' => cancelOrder(...),
    ],

];

function menuItems()
{

    require_once __DIR__ . '/../../services/MenuItemService.php';
    $MenuItemService = new MenuItemService;
    $res = $MenuItemService->GetAll();
    header($res['header']);
    echo json_encode($res['data']);
}

function ordersByCustomer($id)
{
    global $OrderService;
    $res = $OrderService->GetByCustomerId($id);
    header($res['header']);
    echo json_encode($res['data']);
}

function makeOrder($data)
{
    global $OrderService;
    if (! validateNewOrder($data)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid order data']);

        return;
    }
    $res = $OrderService->Add($data);
    header($res['header']);
    echo json_encode($res['data']);
}

function cancelOrder($data)
{
    if (! validateOrder($data)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid order data']);

        return;
    }

    global $OrderService;
    $data['status'] = 'Cancelled';

    $res = $OrderService->Update($data);
    header($res['header']);
    echo json_encode($res['data']);
}

function userOrderHandler($verb, $uri)
{
    global $userOrderRoutes;

    return routeHandler($verb, $uri, $userOrderRoutes);
}
