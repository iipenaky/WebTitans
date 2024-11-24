<?php

require_once __DIR__.'/../../services/ReservationService.php';
require_once __DIR__.'/../../utils.php';

$ReservationService = new ReservationService;
$rFields = [
    'reservation_id',
    'customer_id',
    'table_id',
    'reservation_datetime',
    'number_of_guests',
    'special_requests',
];

function validateNewReservation($data)
{
    global $rFields;

    return validateNewData($rFields, $data, 'reservation');
}

function validateReservation($data)
{
    global $rFields;

    return validateData($rFields, $data, 'reservation');
}

$reservationRoutes = [
    'GET' => [
        'all' => reservationsAll(...),
    ],

    'POST' => [
        'add' => reservationsAdd(...),
    ],

    'PUT' => [
        'update' => reservationsUpdate(...),
    ],

    'DELETE' => [
        'delete' => reservationDelete(...),
    ],

];

function reservationsAll()
{
    global $ReservationService;
    $res = $ReservationService->GetAll();
    header($res['header']);
    echo json_encode($res['data']);
}

function reservationDelete($id)
{
    global $ReservationService;
    $res = $ReservationService->GetById($id);
    header($res['header']);
    echo json_encode($res['data']);
}

function reservationsAdd($data)
{
    global $ReservationService;
    if (validateNewTable($data)) {
        $res = $ReservationService->Add($data);
        header($res['header']);
        echo json_encode($res['data']);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid reservation data']);
    }
}

function reservationsUpdate($data)
{
    global $ReservationService;
    if (validateTable($data)) {
        $res = $ReservationService->Update($data);
        header($res['header']);
        echo json_encode($res['data']);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid reservation data']);
    }

}

function reservationHandler($verb, $uri)
{
    global $reservationRoutes;

    return routeHandler($verb, $uri, $reservationRoutes);
}
