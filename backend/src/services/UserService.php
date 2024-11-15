<?php

require_once __DIR__.'/../db/db.php';

class UserService
{
    private function doesUserExist($email)
    {
        global $db;
        $stmt = $db->prepare('select email from customer where email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $res = $stmt->fetchAll();

        return count($res) >= 1;
    }

    public function SignUp($data)
    {
        $fname = $data['first_name'];
        $lname = $data['last_name'];
        $email = $data['email'];
        $password = $data['password'];

        if ($this->doesUserExist($email)) {
            return [
                'header' => 'HTTP/1.1 400 Bad Request',
                'data' => ['error' => 'User already exists'],
            ];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        global $db;
        $stmt = $db->prepare('insert into customer (first_name, last_name, email, passhash) values (?, ?, ?, ?)');
        $stmt->bindParam(1, $fname);
        $stmt->bindParam(2, $lname);
        $stmt->bindParam(3, $email);
        $stmt->bindParam(4, $hash);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to add user'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 201 Created',
            'data' => ['message' => 'User created successfully'],
        ];
    }

    public function Login($email, $password)
    {
        if (! $this->doesUserExist($email)) {
            return [
                'header' => 'HTTP/1.1 400 Bad Request',
                'data' => ['error' => 'User does not exist'],
            ];
        }

        global $db;
        $stmt = $db->prepare('select * from customer where email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $res = $stmt->fetchAll()[0];
        $hash = $res['passhash'];

        if (! password_verify($password, $hash)) {
            return [
                'header' => 'HTTP/1.1 400 Bad Request',
                'data' => ['error' => 'Incorrect password'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => [
                'customer_id' => $res['customer_id'],
                'first_name' => $res['first_name'],
                'last_name' => $res['last_name'],
                'email' => $res['email'],
            ]];
    }
}
