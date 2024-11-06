<?php

require_once __DIR__."/../../services/CustomerService.php";
require_once __DIR__ . "/../../utils.php";

/**
* Initialize the CustomerService
* A service class handles the business logic of the application performing the actual database operations and returning responses.
* We call the methods of the service class from the route handlers to get the data we need.
 */
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
        echo json_encode(["error" => "Customer not found"]);
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

function customersDelete($id)
{
    global $CustomerService;
    try {
        $res = $CustomerService->Delete($id);
        header("HTTP/1.1 200 OK");
        echo json_encode([ "result" => $res ]);
    } catch (Exception $e) {
        header("HTTP/1.1 500 OK");
        echo json_encode([ "error" => $e->getMessage() ]);
    }
}

/*
* A map of routes for the customers resource.
* Each key in this map corresponds to an HTTP method. and its value an array of the available routes for that method.
* This setup allows for easy addition of new routes and methods and O (1) dispatching of requests.
*/
$customerRoutes = array(

"GET" => array(
"all" => customersAll(...),
"id" => customersById(...),
),

"POST" => array(
"add" => customersAdd(...),
),

"PUT" => array(
"update" => customersUpdate(...),
),

"DELETE" => array(
"delete" => customersDelete(...)
)

);


/**
 * The handler function for the customers resource.
 * The function takes the HTTP verb and the URI components as arguments and dispatches the request to the appropriate route handler.
 */
function customersHandler($verb, $uri)
{
    global $customerRoutes;
    return routeHandler($verb, $uri, $customerRoutes);
}
