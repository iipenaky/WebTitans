<?php

require_once __DIR__.'/../../services/TableService.php';
require_once __DIR__.'/../../utils.php';

$TableService = new TableService;
/*$reserveFields = ["reservation_id","customer_id", "table_id", "reservation_datetime", "number_of_guests", "special_requests"];*/

/*function validateNewReservation($data)*/
/*{*/
/**/
/*    global $reserveFields;*/
/*    return validateNewData($reserveFields, $data, "reservation");*/
/*}*/
/**/
/**/
/*function validateReservation($data)*/
/*{*/
/*    global $reserveFields;*/
/*    return validateData($reserveFields, $data, "reservation");*/
/*}*/

$tableRoutes = [
    'GET' => [
        'available' => availableTables(...),
    ],

    /*"POST" => array(*/
    /*"add" => makeReservation(...),*/
    /*"cancel" => cancelReservation(...),*/
    /*),*/

];

function availableTables()
{
    global $TableService;
    $res = $TableService->GetAvailable();
    header($res['header']);
    echo json_encode($res['data']);
}

function tableHandler($verb, $uri)
{
    global $tableRoutes;

    return routeHandler($verb, $uri, $tableRoutes);
}
