<?php

require_once __DIR__ . "/../../services/PaymentService.php";
require_once __DIR__ . "/../../utils.php";

$PaymentService = new PaymentService();
$fields = ["payment_id","order_id","amount",  "payment_date", "amount", "status"];

function validateNewPayment($data)
{
    global $fields;
    if (any(array_map(function ($item) use ($data) {
        return $data->$item;
    }, array_slice($fields, 2, sizeof($fields))), function ($item) {
        return !isset($item);
    })) {
        header("HTTP/1.1 400 Bad Request");
        throw new Exception("Invalid payment data");
    }
}


function validatePayment($data)
{
    global $fields;
    if (any(array_map(function ($item) use ($data) {
        return $data->$item;
    }, $fields), function ($item) {
        return !isset($item);
    })) {
        header("HTTP/1.1 400 Bad Request");
        throw new Exception("Invalid payment data");
    }
}

$paymentRoutes = array(
"GET" => array(
"all" => paymentsAll(...),
"id" => paymentsById(...),
),

"POST" => array(
"add" => paymentsAdd(...),
),

"PUT" => array(
"update" => paymentsUpdate(...),
),

"DELETE" => array(
"delete" => paymentsDelete(...)
)

);

function paymentsAll()
{
    global $PaymentService;
    $payments = $PaymentService->GetAll();
    header("HTTP/1.1 200 OK");
    echo json_encode($payments);
}

function paymentsById($id)
{
    global $PaymentService;
    $payment = $PaymentService->GetById($id);
    if ($payment) {
        header("HTTP/1.1 200 OK");
        echo json_encode($payment);
    } else {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["error" => "Payment not found"]);
    }
}


function paymentsAdd($data)
{
    global $PaymentService;
    validateNewPayment($data);
    $payment = $PaymentService->Add($data);
    header("HTTP/1.1 201 Created");
    echo json_encode($payment);
}

function paymentsUpdate($data)
{
    global $PaymentService;
    validatePayment($data);
    $payment = $PaymentService->Update($data);
    header("HTTP/1.1 200 OK");
    echo json_encode($payment);
}

function paymentsDelete($id)
{
    global $PaymentService;
    try {
        $res = $PaymentService->Delete($id);
        header("HTTP/1.1 200 OK");
        echo json_encode([ "result" => $res ]);
    } catch (Exception $e) {
        header("HTTP/1.1 500 OK");
        echo json_encode([ "error" => $e->getMessage() ]);
    }
}

function paymentsHandler($verb, $uri)
{
    global $paymentRoutes;
    return routeHandler($verb, $uri, $paymentRoutes);
}
