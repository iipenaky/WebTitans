<?php

require_once __DIR__.'/../db/db.php';

class AdminService
{
    private function doesUserExist($username)
    {
        global $db;
        $stmt = $db->prepare('select username from admin where username = :uname');
        $stmt->bindParam(':uname', $username);
        $stmt->execute();
        $res = $stmt->fetchAll();

        return count($res) >= 1;
    }

    private function doesStaffExist($email)
    {
        global $db;
        $stmt = $db->prepare('select email from staff where email = ?');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $res = $stmt->fetchAll();

        return count($res) >= 1;
    }

    public function SignUp($username, $password)
    {
        if ($this->doesUserExist($username)) {
            return [
                'header' => 'HTTP/1.1 400 Bad Request',
                'data' => ['error' => 'User already exists'],
            ];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        global $db;
        $stmt = $db->prepare('insert into admin (username, passhash) values (:uname, :hash);');
        $stmt->bindParam(':uname', $username);
        $stmt->bindParam(':hash', $hash);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'An error occurred while trying to sign up'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => ['message' => 'User signed up successfully'],
        ];

    }

    public function Login($username, $password)
    {
        if (! $this->doesUserExist($username)) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => 'User not found'],
            ];
        }

        global $db;
        $stmt = $db->prepare('select * from admin where username = ?');
        $stmt->bindParam(1, $username);

        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'An error occurred while trying to log in'],
            ];
        }

        $res = $stmt->fetchAll()[0];
        $hash = $res['passhash'];

        if (! password_verify($password, $hash)) {
            return [
                'header' => 'HTTP/1.1 401 Unauthorized',
                'data' => ['error' => 'Incorrect password'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => ['message' => 'User logged in successfully', 'user' => $res],
        ];
    }

    public function SignUpStaff($data)
    {
        $fname = $data['first_name'];
        $lname = $data['last_name'];
        $pos = $data['position'];
        $email = $data['email'];
        $salary = $data['salary'];
        $password = $data['password'];

        if ($this->doesStaffExist($email)) {
            return [
                'header' => 'HTTP/1.1 400 Bad Request',
                'data' => ['error' => 'Staff already exists'],
            ];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        global $db;
        $stmt = $db->prepare('insert into staff (first_name, last_name, position, email, salary, passhash) values (?, ?, ?,  ?, ?, ?)');
        $stmt->bindParam(1, $fname);
        $stmt->bindParam(2, $lname);
        $stmt->bindParam(3, $pos);
        $stmt->bindParam(4, $email);
        $stmt->bindParam(5, $salary);
        $stmt->bindParam(6, $hash);

        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'An error occurred while trying to sign up'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => ['message' => 'Staff signed up successfully'],
        ];
    }

    public function LoginStaff($email, $password)
    {
        if (! $this->doesStaffExist($email)) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => 'Staff not found'],
            ];
        }

        global $db;
        $stmt = $db->prepare('select * from staff where email = ?');
        $stmt->bindParam(1, $email);

        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'An error occurred while trying to log in'],
            ];
        }

        $res = $stmt->fetchAll()[0];
        $hash = $res['passhash'];

        if (! password_verify($password, $hash)) {
            return [
                'header' => 'HTTP/1.1 401 Unauthorized',
                'data' => ['error' => 'Incorrect password'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => ['message' => 'User logged in successfully', 'staff' => $res],
        ];
    }
}
