<?php

session_start();
require_once __DIR__.'/./admin/customers.php';
require_once __DIR__.'/./admin/staff.php';
require_once __DIR__.'/./admin/payments.php';
require_once __DIR__.'/./admin/orders.php';
require_once __DIR__.'/./admin/menu_items.php';
require_once __DIR__.'/./admin/inventory.php';
require_once __DIR__.'/./admin/tables.php';
require_once __DIR__.'/./admin/reservations.php';
require_once __DIR__.'/../utils.php';
require_once __DIR__.'/../services/AdminService.php';

$AdminService = new AdminService;

function signUp($data)
{
    global $AdminService;
    $fields = ['username', 'password', 'token'];
    if (validateData($fields, $data, 'sign up')) {
        $res = $AdminService->SignUp($data['username'], $data['password'], $data['token']);
        header($res['header']);
        echo json_encode($res['data']);
    }
}

function login($data)
{
    global $AdminService;
    $fields = ['username', 'password'];
    if (validateData($fields, $data, 'login')) {
        $res = $AdminService->Login($data['username'], $data['password']);
        if ($res['header'] == 'HTTP/1.1 200 OK') {
            $_SESSION['admin'] = $res['data']['user'];
        }
        header($res['header']);
        echo json_encode($res['data']);
    }
}

function checkAuth()
{
    if (! isset($_SESSION['admin'])) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['data' => ['error' => 'Unauthorized']]);

        return false;
    }

    return true;
}

function adminHandler($verb, $subroute)
{
    switch ($subroute[0]) {
        case 'logout':
            if ($verb != 'POST') {
                header('HTTP/1.1 405 Method Not Allowed');
                echo json_encode(['data' => 'Method Not Allowed']);
                break;
            }
            $res = destroySession();
            header($res['header']);
            echo json_encode($res['data']);
            break;
        case 'info':
            if (! checkAuth()) {
                break;
            }
            header('HTTP/1.1 200 OK');
            echo json_encode(['data' => 'Admin route']);
            break;
        case 'signup':
            if ($verb != 'POST') {
                header('HTTP/1.1 405 Method Not Allowed');
                break;
            }

            return handleBody(signUp(...));
            break;
        case 'login':
            if ($verb != 'POST') {
                header('HTTP/1.1 405 Method Not Allowed');
                break;
            }

            return handleBody(login(...));
            break;
        case 'staff':
            if (! checkAuth()) {
                break;
            }
            staffHandler($verb, $subroute);
            break;
        case 'customers':
            if (! checkAuth()) {
                break;
            }
            customersHandler($verb, $subroute);
            break;
        case 'payments':
            if (! checkAuth()) {
                break;
            }
            paymentsHandler($verb, $subroute);
            break;
        case 'orders':
            if (! checkAuth()) {
                break;
            }
            ordersHandler($verb, $subroute);
            break;
        case 'menu-items':
            if (! checkAuth()) {
                break;
            }
            menuItemHandler($verb, $subroute);
            break;
        case 'tables':
            if (! checkAuth()) {
                break;
            }
            tableHandler($verb, $subroute);
            break;
        case 'inventory':
            if (! checkAuth()) {
                break;
            }
            inventoryHandler($verb, $subroute);
            break;
        case 'reservations':
            if (! checkAuth()) {
                break;
            }
            reservationHandler($verb, $subroute);
            break;
        default:
            header('HTTP/1.1 404 Not Found');
            exit();
            break;
    }
}
