<?php




$author = 'Gandalf the Gray';
$out = <<<_GAN
    They have taken the bridge and the second hall.
    We have barred the gates but cannot hold them for long.
    The ground shakes, drums... drums in the deep. We cannot get out.
    A shadow lurks in the dark. We can not get out.
    They are coming.

    - $author
    _GAN;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$verb = $_SERVER['REQUEST_METHOD'];

try {
    switch ($uri[1]) {
        case "info":
            header("HTTP/1.1 200 OK");
            echo json_encode(["msg" => "Welcome to the API"]);
            break;
        case "health":
            header("HTTP/1.1 200 OK");
            echo json_encode(["status" => $out]);
            break;
        case "customers":
            require_once __DIR__."/./routes/customers.php";
            customersHandler($verb, $uri);
            break;
        default:
            header("HTTP/1.1 404 Not Found");
            exit();
    }
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => $e->getMessage()]);
}
