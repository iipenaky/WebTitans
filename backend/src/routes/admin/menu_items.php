<?php

require_once __DIR__ . "/../../services/MenuItemService.php";
require_once __DIR__ . "/../../utils.php";

$MenuItemService = new MenuItemService();
/*$sFields = ["staff_id","first_name","last_name",  "position", "email", "salary", "password"];*/

/*function validateNewStaff($data)*/
/*{*/
/*    global $sFields;*/
/*    return validateNewData($sFields, $data, "staff");*/
/*}*/
/**/
/**/
/*function validateStaff($data, $fields = null)*/
/*{*/
/*    global $sFields;*/
/*    if ($fields == null) {*/
/*        $fields = $sFields;*/
/*    }*/
/*    return validateData($fields, $data, "staff");*/
/*}*/

$menuItemRoutes = array(
"GET" => array(
"all" => menuItemsAll(...),
"id" => menuItemById(...),
),
/**/
/*"POST" => array(*/
/*"signup" => staffSignUp(...),*/
/*"login" => staffLogin(...),*/
/*),*/
/**/
/*"PUT" => array(*/
/*"update" => staffUpdate(...),*/
/*),*/
/**/
/*"DELETE" => array(*/
/*"delete" => staffDelete(...)*/
/*)*/

);

function menuItemsAll()
{
    global $MenuItemService;
    $res = $MenuItemService->GetAll();
    header("HTTP/1.1 200 OK");
    echo json_encode($res);
}

function menuItemById($id)
{
    global $MenuItemService;
    $res = $MenuItemService->GetById($id);
    if ($res == null) {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["error" => "Menu item not found"]);
    } else {
        header("HTTP/1.1 200 OK");
        echo json_encode($res);
    }
}


function menuItemHandler($verb, $uri)
{
    global $menuItemRoutes;
    return routeHandler($verb, $uri, $menuItemRoutes);
}
