<?php

require_once __DIR__ . "/../../services/ReservationService.php";
require_once __DIR__ . "/../../utils.php";

$ReservationService = new ReservationService();
$reserveFields = ["reservation_id","customer_id", "table_id", "reservation_datetime", "number_of_guests", "special_requests"];

function validateNewReservation($data)
{

    global $reserveFields;
    return validateNewData($reserveFields, $data, "reservation");
}


function validateReservation($data)
{
    global $reserveFields;
    return validateData($reserveFields, $data, "reservation");
}

$reserveRoutes = array(
"GET" => array(
"id" => reservationsByCustomer(...),
),

"POST" => array(
"add" => makeReservation(...),
),

"DELETE" => array(
"cancel" => cancelReservation(...),
)

);

function reservationsByCustomer($id)
{
    global $ReservationService;
    $reservations = $ReservationService->GetByCustomerId($id);
    header("HTTP/1.1 200 OK");
    echo json_encode($reservations);
}

function makeReservation($data)
{
    global $ReservationService;
    if (!validateNewReservation($data)) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["error" => "Invalid reservation data"]);
        return;
    }
    $reservation = $ReservationService->Add($data);

    if ($reservation == null) {
        return;
    }
    header("HTTP/1.1 201 Created");
    echo json_encode($reservation);
}

function cancelReservation($id)
{
    global $ReservationService;
    if (!$ReservationService->Delete($id)) {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["error" => "Reservation not found"]);
        return;
    }

    header("HTTP/1.1 200 OK");
    echo json_encode(
        ["message" => "Reservation cancelled"]
    );

}


function reserveHandler($verb, $uri)
{
    global $reserveRoutes ;
    return routeHandler($verb, $uri, $reserveRoutes);
}
