<?php

require_once __DIR__.'/../../services/OrderService.php';
require_once __DIR__.'/../../utils.php';

$OrderService = new OrderService;
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

$orderRoutes = [
    'GET' => [
        'all' => ordersAll(...),
        'id' => ordersByCustomerId(...),
    ],

    /*"POST" => array(*/
    /*"add" => paymentsAdd(...),*/
    /*),*/
    /**/
    /*"PUT" => array(*/
    /*"update" => paymentsUpdate(...),*/
    /*),*/

    'DELETE' => [
        'deleteById' => orderDeleteById(...),
    ],

];

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
