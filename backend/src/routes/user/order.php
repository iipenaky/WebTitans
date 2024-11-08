<?php

require_once __DIR__ . "/../../services/OrderService.php";
require_once __DIR__ . "/../../utils.php";

$OrderService = new OrderService();
$orderFields = ["order_id","customer_id","staff_id", "total_amount"];
$orderDetailsFields = ["order_detail_id", "menu_item_id", "quantity"];

function validateOrderDetails($data)
{
    global $orderDetailsFields;
    foreach ($data as $orderDetail) {
        if (!validateNewData($orderDetailsFields, $orderDetail, "order detail")) {
            return false;
        }
    }
    return true;
}

function validateNewOrder($data)
{
    if (!isset($data["order"])) {
        return false;
    }
    if (!isset($data["order_details"])) {
        return false;
    }

    global $orderFields;
    return validateNewData($orderFields, $data["order"], "order") && validateOrderDetails($data["order_details"]);
}


function validateOrder($data)
{
    global $orderFields;
    return validateData($orderFields, $data, "order");
}

$userOrderRoutes = array(
"GET" => array(
"id" => ordersByCustomer(...),
),

"POST" => array(
"add" => makeOrder(...),
"cancel" => cancelOrder(...),
),

);

function ordersByCustomer($id)
{
    global $OrderService;
    $orders = $OrderService->GetByCustomerId($id);
    header("HTTP/1.1 200 OK");
    echo json_encode($orders);
}

function makeOrder($data)
{
    global $OrderService;
    if (!validateNewOrder($data)) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["error" => "Invalid order data"]);
        return;
    }
    $order = $OrderService->Add($data);
    header("HTTP/1.1 201 Created");
    echo json_encode($order);
}

function cancelOrder($data)
{
    global $OrderService;
    validateOrder($data);
    $data["status"] = "Cancelled";
    $order = $OrderService->Update($data);

    if (!$order) {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["error" => "Order not found"]);
        return;
    }

    header("HTTP/1.1 200 OK");
    echo json_encode($order);

}


function userOrderHandler($verb, $uri)
{
    global $userOrderRoutes;
    return routeHandler($verb, $uri, $userOrderRoutes);
}
