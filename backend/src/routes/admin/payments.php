<?php

require_once __DIR__ . "/../../services/PaymentService.php";
require_once __DIR__ . "/../../utils.php";

$PaymentService = new PaymentService();
$pFields = ["payment_id","order_id","amount",  "payment_time", "amount", "status"];

function validateNewPayment($data)
{
    global $pFields;
    return validateNewData($pFields, $data, "payment");
}


function validatePayment($data)
{
    global $pFields;
    return validateData($pFields, $data, "payment");
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
