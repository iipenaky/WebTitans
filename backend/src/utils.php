<?php

function any($array, $func)
{
    foreach ($array as $item) {
        if (!$func($item)) {
            return false;
        };
    }
    return true;
}

function slice($array, $start = 1, $end = null)
{
    if ($end == null) {
        return array_slice($array, $start, sizeof($array));
    } else {
        return array_slice($array, $start, $end);
    }
}


function handleNoBody($uri, $func)
{
    if (isset($uri[2]) && $uri[2] !== null) {
        $func($uri[2]);
    } else {
        $func();
    }
}

function handleBody($func)
{
    $data = json_decode(file_get_contents("php://input"));
    $func($data);
}


function routeHandler($verb, $uri, $routes)
{
    try {
        $subroute = $uri[1];
        $func = $routes[$verb][$subroute];

        if (!$func) {
            header("HTTP/1.1 404 Not Found");
            exit();
        }

        match ($verb) {
            "GET", "DELETE" => handleNoBody($uri, $func),
            "POST", "PUT" => handleBody($func),
        };

    } catch (Exception $e) {
        throw $e;
    }
}
