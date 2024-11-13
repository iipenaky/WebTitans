<?php

require_once __DIR__.'/../../services/StaffService.php';
require_once __DIR__.'/../../services/AdminService.php';
require_once __DIR__.'/../../utils.php';

$AdminService = new AdminService;
$StaffService = new StaffService;
$sFields = ['staff_id', 'first_name', 'last_name',  'position', 'email', 'salary', 'password'];

function validateNewStaff($data)
{
    global $sFields;

    return validateNewData($sFields, $data, 'staff');
}

function validateStaff($data, $fields = null)
{
    global $sFields;
    if ($fields == null) {
        $fields = $sFields;
    }

    return validateData($fields, $data, 'staff');
}

$staffRoutes = [
    'GET' => [
        'all' => staffAll(...),
        'id' => staffById(...),
    ],

    'POST' => [
        'signup' => staffSignUp(...),
        'login' => staffLogin(...),
    ],

    'PUT' => [
        'update' => staffUpdate(...),
    ],

    'DELETE' => [
        'delete' => staffDelete(...),
    ],

];

function staffAll()
{
    global $StaffService;
    $res = $StaffService->GetAll();
    header('HTTP/1.1 200 OK');
    echo json_encode($res);
}

function staffById($id)
{
    global $StaffService;
    $res = $StaffService->GetById($id);
    if ($res == null) {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Staff not found']);
    } else {
        header('HTTP/1.1 200 OK');
        echo json_encode($res);
    }
}

function staffUpdate($data)
{
    global $StaffService;
    if (validateStaff($data, ['staff_id', 'first_name', 'last_name',  'position', 'email', 'salary', 'passhash'])) {
        $res = $StaffService->Update($data);
        if ($res == null) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Staff not found']);
        } else {
            header('HTTP/1.1 200 OK');
            echo json_encode($res);
        }
    }
}

function staffDelete($id)
{
    global $StaffService;
    $res = $StaffService->Delete($id);
    if ($res) {
        header('HTTP/1.1 200 OK');
        echo json_encode(['message' => 'Staff deleted successfully']);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Staff not found or deleting this staff would leave no one to fulfil orders']);
    }
}

function staffSignUp($data)
{
    global $AdminService;
    if (validateNewStaff($data)) {
        $res = $AdminService->SignUpStaff($data);
        header($res['header']);
        echo json_encode($res['data']);
    }

}

function staffLogin($data)
{
    global $AdminService;
    $sFields = ['email', 'password'];
    if (validateData($sFields, $data, 'login')) {
        $res = $AdminService->LoginStaff($data['email'], $data['password']);
        header($res['header']);
        echo json_encode($res['data']);
    }
}

function staffHandler($verb, $uri)
{
    global $staffRoutes;

    return routeHandler($verb, $uri, $staffRoutes);
}
