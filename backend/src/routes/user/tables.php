<?php

require_once __DIR__ . "/../../services/TableService.php";
require_once __DIR__ . "/../../utils.php";

$TableService = new TableService();
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

$tableRoutes = array(
"GET" => array(
"available" => availableTables(...)
),

/*"POST" => array(*/
/*"add" => makeReservation(...),*/
/*"cancel" => cancelReservation(...),*/
/*),*/

);



function availableTables()
{
    global $TableService;
    $tables = $TableService->GetAvailable();
    header("HTTP/1.1 200 OK");
    echo json_encode($tables);
}


function tableHandler($verb, $uri)
{
    global $tableRoutes;
    return routeHandler($verb, $uri, $tableRoutes);
}
