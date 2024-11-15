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
    $tables = $TableService->GetAll();
    header('HTTP/1.1 200 OK');
    echo json_encode($tables);
}

function tableDelete($id)
{
    global $TableService;
    if ($TableService->Delete($id)) {
        header('HTTP/1.1 200 OK');
        echo json_encode(['message' => 'Table deleted']);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Table not found']);

    }
}

function tablesAdd($data)
{
    global $TableService;
    if (validateNewTable($data)) {
        $table = $TableService->Add($data);
        header('HTTP/1.1 201 Created');
        echo json_encode($table);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid data']);
    }
}

function tablesUpdate($data)
{
    global $TableService;
    if (validateTable($data)) {
        $table = $TableService->Update($data);
        header('HTTP/1.1 200 OK');
        echo json_encode($table);
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
