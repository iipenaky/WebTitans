<?php

require_once __DIR__ . "/./user/order.php";
require_once __DIR__ . "/../services/UserService.php";
require_once __DIR__ . "/../utils.php";

$UserService = new UserService();

function signUp($data)
{
    global $UserService;
    $fields = ["first_name", "last_name" , "email", "password"];
    if (validateData($fields, $data, "sign up")) {
        if (!$UserService->SignUp($data)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "User already exists"]);
        }
    }

}

function login($data)
{
    global $UserService;
    $fields = ["email", "password"];
    if (validateData($fields, $data, "login")) {
        $res = $UserService->Login($data["email"], $data["password"]);
        if ($res == null) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "User does not exist or incorrect password"]);
        } else {
            echo $res;
        }
    }
}

function userHandler($verb, $subroute)
{
    switch ($subroute[0]) {
        case "info":
            header("HTTP/1.1 200 OK");
            echo json_encode(["data" => "User route"]);
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
        case "order":
            userOrderHandler($verb, $subroute);
            break;
        default:
            header("HTTP/1.1 404 Not Found");
            exit();
            break;
    }
}
