<?php

require_once __DIR__."/./admin/customers.php";
require_once __DIR__."/./admin/staff.php";
require_once __DIR__."/./admin/payments.php";
require_once __DIR__."/./admin/orders.php";
require_once __DIR__."/./admin/menu_items.php";
require_once __DIR__."/./admin/tables.php";
require_once __DIR__."/../utils.php";
require_once __DIR__ . "/../services/AdminService.php";

$AdminService = new AdminService();

function signUp($data)
{
    global $AdminService;
    $fields = ["username", "password"];
    if (validateData($fields, $data, "sign up")) {
        if (!$AdminService->SignUp($data["username"], $data["password"])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "User already exists"]);
        }
    }

}

function login($data)
{
    global $AdminService;
    $fields = ["username", "password"];
    if (validateData($fields, $data, "login")) {
        $res = $AdminService->Login($data["username"], $data["password"]);
        if ($res == null) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "User does not exist or incorrect password"]);
        } else {
            echo $res;
        }
    }
}


function adminHandler($verb, $subroute)
{
    switch ($subroute[0]) {
        case "info":
            header("HTTP/1.1 200 OK");
            echo json_encode(["data" => "Admin route"]);
            break;
        case "signup":
            if ($verb != "POST") {
                header("HTTP/1.1 405 Method Not Allowed");
                break;
            }
            return handleBody(signUp(...));
            break;
        case "login":
            if ($verb != "POST") {
                header("HTTP/1.1 405 Method Not Allowed");
                break;
            }
            return handleBody(login(...));
            break;
        case "staff":
            staffHandler($verb, $subroute);
            break;
        case "customers":
            customersHandler($verb, $subroute);
            break;
        case "payments":
            paymentsHandler($verb, $subroute);
            break;
        case "orders":
            ordersHandler($verb, $subroute);
            break;
        case "menu-items":
            menuItemHandler($verb, $subroute);
            break;
        case "tables":
            tableHandler($verb, $subroute);
            break;
        default:
            header("HTTP/1.1 404 Not Found");
            exit();
            break;
    }
}
