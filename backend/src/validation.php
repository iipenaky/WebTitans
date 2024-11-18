<?php

const emailRegex = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z.]{2,6}$/";
const passwordRegexes = [
    [
        'msg' => 'Must be at least 8 characters long',
        'regex' => '/.{8,}/',
    ],
    [
        'msg' => 'Must have at least one uppercase letter',
        'regex' => '/[A-Z]/',
    ],
    [
        'msg' => 'Must include at least 3 digits',
        'regex' => "/\d{3,}/",
    ],
    [
        'msg' => 'Must contain at least one special character',
        'regex' => "~[!@#$%^&*()\-_=+\|{};:/?.>]~",
    ],
];

function validateEmail($email)
{
    return preg_match(emailRegex, $email);
}

function validatePassword($password)
{
    $problems = [];
    $result = true;

    array_map(function ($item) use ($password, &$problems, &$result) {
        $msg = $item['msg'];
        $regex = $item['regex'];
        $res = preg_match($regex, $password);
        if (! $res) {
            $problems[] = $msg;
        }
        if ($result) {
            $result = $res;
        }
    }, passwordRegexes);

    return ['result' => $result, 'problems' => $problems];
}

function handleEmail($email)
{
    if (! validateEmail($email)) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid email']);

        return false;
    }

    return true;
}

function handlePasswordProblems($password)
{
    $res = validatePassword($password);
    if (! $res['result']) {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Invalid password', 'problems' => $res['problems']]);

        return false;
    }

    return true;
}
