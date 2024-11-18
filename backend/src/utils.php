<?php

function destroySession()
{
    session_start();
    $destroy = session_destroy();
    $unset = session_unset();
    $abort = session_abort();

    return [
        'header' => 'HTTP/1.1 200 OK',
        'data' => [
            'destroy' => $destroy,
            'unset' => $unset,
            'abort' => $abort,

        ],
    ];
}

function mysql_datetime($timestamp)
{
    return date('Y-m-d H:i:s', $timestamp);
}

function mysql_datetime_from_mystring($date)
{
    return date('Y-m-d H:i:s', strtotime($date));
}

function any($array, $func)
{
    foreach ($array as $item) {
        if (! $func($item)) {
            return false;
        }
    }

    return true;
}

function slice($array, $start = 1, $end = null)
{
    if ($end == null) {
        return array_slice($array, $start, count($array));
    } else {
        return array_slice($array, $start, $end);
    }
}

function handleNoBody($uri, $func)
{
    try {
        // Determine if $func is a callable function or a method
        $reflection = is_array($func)
            ? new ReflectionMethod($func[0], $func[1]) // For class methods
            : new ReflectionFunction($func);          // For standalone functions or closures

        // Get the number of required parameters
        $requiredParams = $reflection->getNumberOfRequiredParameters();

        // Call $func with or without arguments based on the number of required parameters
        if ($requiredParams === 0) {
            $func(); // No arguments required
        } elseif (isset($uri[2]) && $uri[2] !== null) {
            $func($uri[2]); // Provide the argument
        } else {
            throw new InvalidArgumentException('Insufficient arguments for the function.');
        }
    } catch (ReflectionException $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Internal Server Error']);
    } catch (InvalidArgumentException $e) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Bad Request']);
    }
}

function handleBody($func)
{
    $data = json_decode(file_get_contents('php://input'), associative: true);
    $func($data);
}

function isAnyEmpty($array)
{
    return any($array, function ($item) {
        if (gettype($item) == 'string') {
            return empty($item);
        }
    });
}

function validateNewData($fields, $data, $dataType)
{
    $data = trimArray($data);
    if (isAnyEmpty($data)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Empty fields']);

        return false;
    }

    if (any(array_map(function ($item) use ($data) {
        return $data[$item];
    }, slice($fields, 2)), function ($item) {
        return ! isset($item);
    })) {
        header('HTTP/1.1 400 Bad Request');

        echo json_encode(['error' => "Invalid $dataType data"]);

        return false;
    }

    return true;
}

function trimArray($array)
{
    if ($array == null) {
        return $array;
    }

    if (count($array) == 0) {
        return $array;
    }

    if (! is_array($array)) {
        return trim($array);
    }

    return array_map(function ($item) {
        return trim($item);
    }, $array);
}

function validateData($fields, $data, $dataType)
{
    $data = trimArray($data);

    if (isAnyEmpty($data)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Empty fields']);

        return false;
    }

    if (! is_array($data)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => "Invalid $dataType data"]);

        return false;
    }

    if (any(array_map(function ($item) use ($data) {
        return $data[$item];
    }, $fields), function ($item) {
        return ! isset($item);
    })) {
        header('HTTP/1.1 400 Bad Request');

        echo json_encode(['error' => "Invalid $dataType data"]);

        return false;
    }

    return true;
}

function routeHandler($verb, $uri, $routes)
{
    try {
        $subroute = $uri[1];

        if (! isset($routes[$verb][$subroute])) {
            header('HTTP/1.1 404 Not Found');
            exit();
        }

        $func = $routes[$verb][$subroute];

        if (! $func) {
            header('HTTP/1.1 404 Not Found');
            exit();
        }

        match ($verb) {
            'GET', 'DELETE' => handleNoBody($uri, $func),
            'POST', 'PUT' => handleBody($func),
        };

    } catch (Exception $e) {
        throw $e;
    }
}
