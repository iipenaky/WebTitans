<?php

require_once __DIR__.'/../../services/CustomerService.php';
require_once __DIR__.'/../../utils.php';

/**
 * Initialize the CustomerService
 * A service class handles the business logic of the application performing the actual database operations and returning responses.
 * We call the methods of the service class from the route handlers to get the data we need.
 */
$CustomerService = new CustomerService;

function customersAll()
{
    global $CustomerService;
    $res = $CustomerService->GetAll();
    header($res['header']);
    echo json_encode($res['data']);
}

function customersById($id)
{
    global $CustomerService;
    $res = $CustomerService->GetById($id);
    header($res['header']);
    echo json_encode($res['data']);

}

function validateCustomer($data)
{
    if (! isset($data->first_name) || ! isset($data->last_name) || ! isset($data->phone_number) || ! isset($data->email) || ! isset($data->address)) {
        header('HTTP/1.1 400 Bad Request');
        throw new Exception('Invalid customer data');
    }
}

function customersAdd($data)
{
    global $CustomerService;
    validateCustomer($data);
    $res = $CustomerService->Add($data);
    header($res['header']);
    echo json_encode($res['data']);
}

function customersUpdate($data)
{
    global $CustomerService;
    validateCustomer($data);
    $res = $CustomerService->Update($data);
    header($res['header']);
    echo json_encode($res['data']);
}

function customersDelete($id)
{
    global $CustomerService;
    $res = $CustomerService->Delete($id);
    header($res['header']);
    echo json_encode($res['data']);
}

function customersNum()
{
    global $CustomerService;
    $res = $CustomerService->GetNum();
    header($res['header']);
    echo json_encode($res['data']);
}

/*
* A map of routes for the customers resource.
* Each key in this map corresponds to an HTTP method. and its value an array of the available routes for that method.
* This setup allows for easy addition of new routes and methods and O (1) dispatching of requests.
*/
$customerRoutes = [

    'GET' => [
        'all' => customersAll(...),
        'id' => customersById(...),
        'num' => customersNum(...),
    ],

    'POST' => [
        'add' => customersAdd(...),
    ],

    'PUT' => [
        'update' => customersUpdate(...),
    ],

    'DELETE' => [
        'delete' => customersDelete(...),
    ],

];

/**
 * The handler function for the customers resource.
 * The function takes the HTTP verb and the URI components as arguments and dispatches the request to the appropriate route handler.
 */
function customersHandler($verb, $uri)
{
    global $customerRoutes;

    return routeHandler($verb, $uri, $customerRoutes);
}
