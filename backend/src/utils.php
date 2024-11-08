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
    $data = json_decode(file_get_contents("php://input"), associative: true);
    $func($data);
}

function validateNewData($fields, $data, $dataType)
{
    if (any(array_map(function ($item) use ($data) {
        return $data[$item];
    }, slice($fields, 2)), function ($item) {
        return !isset($item);
    })) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["error" => "Invalid $dataType data"]);
        return false;
    }
    return true;
}

function validateData($fields, $data, $dataType)
{
    if (!is_array($data)) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["error" => "Invalid $dataType data"]);
        return false;
    }

    if (any(array_map(function ($item) use ($data) {
        return $data[$item];
    }, $fields), function ($item) {
        return !isset($item);
    })) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["error" => "Invalid $dataType data"]);
        return false;
    }
    return true;
}


function routeHandler($verb, $uri, $routes)
{
    try {
        $subroute = $uri[1];

        if (!isset($routes[$verb][$subroute])) {
            header("HTTP/1.1 404 Not Found");
            exit();
        }

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
