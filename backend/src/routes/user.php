<?php

require_once __DIR__ . "/./user/order.php";

function userHandler($verb, $subroute)
{
    switch ($subroute[0]) {
        case "info":
            header("HTTP/1.1 200 OK");
            echo json_encode(["data" => "User route"]);
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
