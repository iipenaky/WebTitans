<?php

require_once __DIR__."/./admin/customers.php";
require_once __DIR__."/./admin/payments.php";

function adminHandler($verb, $subroute)
{
    switch ($subroute[0]) {
        case "info":
            header("HTTP/1.1 200 OK");
            echo json_encode(["data" => "Admin route"]);
            break;
        case "customers":
            customersHandler($verb, $subroute);
            break;
        case "payments":
            paymentsHandler($verb, $subroute);
            break;
        default:
            header("HTTP/1.1 404 Not Found");
            exit();
            break;
    }
}
