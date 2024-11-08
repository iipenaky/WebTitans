<?php

require_once __DIR__ . "/../../services/AdminService.php";
require_once __DIR__ . "/../../utils.php";

$AdminService = new AdminService();
$sFields = ["staff_id","first_name","last_name",  "position", "email", "salary", "password"];

function validateNewStaff($data)
{
    global $sFields;
    return validateNewData($sFields, $data, "staff");
}


function validateStaff($data)
{
    global $sFields;
    return validateData($sFields, $data, "staff");
}

$staffRoutes = array(
/*"GET" => array(*/
/*"all" => paymentsAll(...),*/
/*"id" => paymentsById(...),*/
/*),*/
/**/
"POST" => array(
"signup" => staffSignUp(...),
"login" => staffLogin(...),
),
/**/
/*"PUT" => array(*/
/*"update" => paymentsUpdate(...),*/
/*),*/
/**/
/*"DELETE" => array(*/
/*"delete" => paymentsDelete(...)*/
/*)*/

);

function staffSignUp($data)
{
    global $AdminService;
    global $sFields;
    if (validateNewStaff($data)) {
        if (!$AdminService->SignUpStaff($data)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "User already exists"]);
        }
    }

}

function staffLogin($data)
{
    global $AdminService;
    $sFields = ["email", "password"];
    if (validateData($sFields, $data, "login")) {
        $res = $AdminService->LoginStaff($data["email"], $data["password"]);
        if ($res == null) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "User does not exist or incorrect password"]);
        } else {
            echo $res;
        }
    }
}


function staffHandler($verb, $uri)
{
    global $staffRoutes;
    return routeHandler($verb, $uri, $staffRoutes);
}
