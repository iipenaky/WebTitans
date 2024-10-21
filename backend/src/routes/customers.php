<?php

require_once __DIR__."/../services/CustomerService.php";

$CustomerService = new CustomerService();

function customersAll()
{
    global $CustomerService;
    $customers = $CustomerService->GetAll();
    header("HTTP/1.1 200 OK");
    echo json_encode($customers);
}

function customersById($id)
{
    global $CustomerService;
    $customer = $CustomerService->GetById($id);
    if ($customer) {
        header("HTTP/1.1 200 OK");
        echo json_encode($customer);
    } else {
        header("HTTP/1.1 404 Not Found");
    }
}

function validateCustomer($data)
{
    if (!isset($data->first_name) || !isset($data->last_name) || !isset($data->phone_number) || !isset($data->email) || !isset($data->address)) {
        header("HTTP/1.1 400 Bad Request");
        throw new Exception("Invalid customer data");
    }
}

function customersAdd($data)
{
    global $CustomerService;
    validateCustomer($data);
    $customer = $CustomerService->Add($data);
    header("HTTP/1.1 201 Created");
    echo json_encode($customer);
}

function customersUpdate($data)
{
    global $CustomerService;
    validateCustomer($data);
    $customer = $CustomerService->Update($data);
    header("HTTP/1.1 200 OK");
    echo json_encode($customer);
}

$routes = array(

"GET" => array(
"all" => customersAll(...),
"id" => customersById(...),
),

"POST" => array(
"add" => customersAdd(...),
"update" => customersUpdate(...),
)

);

function handleNoBody($uri, $func)
{
    if ($uri[3] && $uri[3] !== null) {
        $func($uri[3]);
    } else {
        $func();
    }
}

function handleBody($func)
{
    $data = json_decode(file_get_contents("php://input"));
    $func($data);
}

function customersHandler($verb, $uri)
{
    /*try {*/
    global $routes;
    $subroute = $uri[2];
    $func = $routes[$verb][$subroute];

    if (!$func) {
        header("HTTP/1.1 404 Not Found");
        exit();
    }

    match ($verb) {
        "GET", "DELETE" => handleNoBody($uri, $func),
        "POST", "PUT" => handleBody($func),
    };

    /*} catch (Exception $e) {*/
    /*    throw $e;*/
    /*}*/
}
