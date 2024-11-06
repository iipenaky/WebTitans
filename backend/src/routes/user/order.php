<?php

require_once __DIR__ . "/../../services/OrderService.php";
require_once __DIR__ . "/../../utils.php";

$OrderService = new OrderService();
$fields = ["order_id","customer_id","staff_id",  "order_time", "total_amount", "status"];

function validateNewOrder($data)
{
    global $fields;
    return validateNewData($fields, $data, "payment");
}


function validateOrder($data)
{
    global $fields;
    return validateData($fields, $data, "payment");
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
    validateNewOrder($data);
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
