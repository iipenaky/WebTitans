<?php

session_start();
require_once __DIR__.'/./utils.php';
/*ini_set('display_errors', 1);*/
/*error_reporting(E_ALL);*/

$out = <<<'_GAN'
    They have taken the bridge and the second hall.
    We have barred the gates but cannot hold them for long.
    The ground shakes, drums... drums in the deep. We cannot get out.
    A shadow lurks in the dark. We can not get out.
    They are coming.
    _GAN;

// Set headers
$allowedOrigins = [
    'http://localhost:8080',
    'http://169.239.251.102:3341',
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json; charset=UTF-8');
    header('Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE');
    header('Access-Control-Max-Age: 3600');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Set-Cookie');
} else {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Origin not allowed']);
    exit();
}

// Get the URI and split it into its components
$uri = parse_url($_SERVER['PHP_SELF'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Remove elements before and including index.php
$uri = slice($uri, array_search('index.php', $uri) + 1);

// Remove empty elements
$uri = array_filter($uri, function ($value) {
    return $value !== '';
});

// Get the HTTP verb
$verb = $_SERVER['REQUEST_METHOD'];

// If the URI is empty, return a 404
if (! isset($uri[0])) {
    header('HTTP/1.1 404 Not Found');
    exit();
}

try {
    // Match the first element of the URI to the appropriate route
    /*echo json_encode($uri);*/
    switch ($uri[0]) {
        case 'info':
            header('HTTP/1.1 200 OK');
            echo json_encode(['msg' => 'Welcome to the API']);
            break;
        case 'health':
            header('HTTP/1.1 200 OK');
            echo json_encode(['status' => $out]);
            break;
        case 'admin':
            require_once __DIR__.'/./routes/admin.php';
            adminHandler($verb, slice($uri, 1));
            break;
        case 'user':
            require_once __DIR__.'/./routes/user.php';
            userHandler($verb, slice($uri, 1));
            break;
        default:
            header('HTTP/1.1 404 Not Found');
            exit();
            break;
    }
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
}
