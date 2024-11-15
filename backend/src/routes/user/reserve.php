<?php

require_once __DIR__.'/../../services/ReservationService.php';
require_once __DIR__.'/../../utils.php';

$ReservationService = new ReservationService;
$reserveFields = ['reservation_id', 'customer_id', 'table_id', 'reservation_datetime', 'number_of_guests', 'special_requests'];

function validateNewReservation($data)
{

    global $reserveFields;

    return validateNewData($reserveFields, $data, 'reservation');
}

function validateReservation($data)
{
    global $reserveFields;

    return validateData($reserveFields, $data, 'reservation');
}

$reserveRoutes = [
    'GET' => [
        'id' => reservationsByCustomer(...),
    ],

    'POST' => [
        'add' => makeReservation(...),
    ],

    'DELETE' => [
        'cancel' => cancelReservation(...),
    ],

];

function reservationsByCustomer($id)
{
    global $ReservationService;
    $res = $ReservationService->GetByCustomerId($id);
    header($res['header']);
    echo json_encode($res['data']);
}

function makeReservation($data)
{
    global $ReservationService;
    if (! validateNewReservation($data)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid reservation data']);

        return;
    }
    $res = $ReservationService->Add($data);
    header($res['header']);
    echo json_encode($res['data']);

}

function cancelReservation($id)
{
    global $ReservationService;
    $res = $ReservationService->GetById($id);
    header($res['header']);
    echo json_encode($res['data']);

}

function reserveHandler($verb, $uri)
{
    global $reserveRoutes;

    return routeHandler($verb, $uri, $reserveRoutes);
}
