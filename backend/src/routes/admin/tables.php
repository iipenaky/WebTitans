<?php

require_once __DIR__.'/../../services/TableService.php';
require_once __DIR__.'/../../utils.php';

$TableService = new TableService;
$tFields = ['table_id', 'table_number', 'seating_capacity', 'location'];

function validateNewTable($data)
{
    global $tFields;

    return validateNewData($tFields, $data, 'table');
}

function validateTable($data)
{
    global $tFields;

    return validateData($tFields, $data, 'table');
}

$tableRoutes = [
    'GET' => [
        'all' => tablesAll(...),
    ],

    'POST' => [
        'add' => tablesAdd(...),
    ],

    'PUT' => [
        'update' => tablesUpdate(...),
    ],

    'DELETE' => [
        'delete' => tableDelete(...),
    ],

];

function tablesAll()
{
    global $TableService;
    $res = $TableService->GetAll();
    header($res['header']);
    echo json_encode($res['data']);
}

function tableDelete($id)
{
    global $TableService;
    $res = $TableService->GetById($id);
    header($res['header']);
    echo json_encode($res['data']);
}

function tablesAdd($data)
{
    global $TableService;
    if (validateNewTable($data)) {
        $res = $TableService->Add($data);
        header($res['header']);
        echo json_encode($res['data']);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid table data']);
    }
}

function tablesUpdate($data)
{
    global $TableService;
    if (validateTable($data)) {
        $res = $TableService->Update($data);
        header($res['header']);
        echo json_encode($res['data']);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid data']);
    }

}

function tableHandler($verb, $uri)
{
    global $tableRoutes;

    return routeHandler($verb, $uri, $tableRoutes);
}
