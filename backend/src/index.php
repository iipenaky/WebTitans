<?php

session_start();
require_once __DIR__.'/./utils.php';
require_once __DIR__.'/./middleware.php';

/*ini_set('display_errors', 1);*/
/*error_reporting(E_ALL);*/

$out = <<<'_GAN'
    They have taken the bridge and the second hall.
    We have barred the gates but cannot hold them for long.
    The ground shakes, drums... drums in the deep. We cannot get out.
    A shadow lurks in the dark. We can not get out.
    They are coming.
    _GAN;

define('HEADERS', [
    'Content-Type: application/json; charset=UTF-8',
    'Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE',
    'Access-Control-Max-Age: 3600',
    'Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With',
]);

$requestOrigin = rtrim($_SERVER['HTTP_ORIGIN'], '/');
$handler = new MiddlewareHandler;

if ($requestOrigin == '') {
    header('Access-Control-Allow-Origin: http://169.239.251.102:3341');
    header('Access-Control-Allow-Credentials: true');
    foreach (HEADERS as $header) {
        header($header);
    }
} else {
    $corsOptions = [
        'allowedOrigins' => [
            'http://localhost:80',
            'http://localhost:8080',
            'http://169.239.251.102:3341',
            'http://169.239.251.102:3341/~madiba.quansah/frontend',
        ],
        'allowedMethods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowedHeaders' => ['Content-Type', 'Authorization', 'X-Requested-With'],
        'allowCredentials' => true,
        'maxAge' => 3600,
    ];

    $handler->add(new CorsMiddleware($corsOptions))->add(new JsonMiddleware);

    if (! $handler->handle()) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['error' => 'Middleware error']);
    }
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
            echo json_encode(['error' => 'Route not found']);
            exit();
            break;
    }
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
}
