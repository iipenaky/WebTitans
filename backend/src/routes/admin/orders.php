<?php

require_once __DIR__ . "/../../services/OrderService.php";
require_once __DIR__ . "/../../utils.php";

$OrderService = new OrderService();
/*$oFields = ["payment_id","order_id","amount",  "payment_time", "amount", "status"];*/
/**/
/*function validateNewPayment($data)*/
/*{*/
/*    global $oFields;*/
/*    return validateNewData($oFields, $data, "order");*/
/*}*/
/**/
/**/
/*function validatePayment($data)*/
/*{*/
/*    global $oFields;*/
/*    return validateData($oFields, $data, "order");*/
/*}*/

$orderRoutes = array(
"GET" => array(
"all" => ordersAll(...),
"id" => ordersByCustomerId(...),
),

/*"POST" => array(*/
/*"add" => paymentsAdd(...),*/
/*),*/
/**/
/*"PUT" => array(*/
/*"update" => paymentsUpdate(...),*/
/*),*/

"DELETE" => array(
"deleteById" => orderDeleteById(...)
)

);

function orderDeleteById($id)
{
    global $OrderService;
    $order = $OrderService->GetById($id);
    if ($order) {
        $OrderService->Delete($id);
        header("HTTP/1.1 200 OK");
        echo json_encode(["message" => "Order deleted"]);
    } else {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["error" => "Order not found"]);
    }
}

function ordersAll()
{
    global $OrderService;
    $orders = $OrderService->GetAll();
    header("HTTP/1.1 200 OK");
    echo json_encode($orders);
}

function ordersByCustomerId($id)
{
    global $OrderService;
    $order = $OrderService->GetByCustomerId($id);
    if ($order) {
        header("HTTP/1.1 200 OK");
        echo json_encode($order);
    } else {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["error" => "Customer not found"]);
    }
}

function ordersHandler($verb, $uri)
{
    global $orderRoutes;
    return routeHandler($verb, $uri, $orderRoutes);
}
