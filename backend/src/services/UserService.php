<?php

require_once __DIR__."/../db/db.php";

class UserService
{
    private function doesUserExist($email)
    {
        global $db;
        $stmt = $db->prepare("select email from customer where email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $res = $stmt->fetchAll();
        return sizeof($res) >= 1;
    }

    public function SignUp($data)
    {
        $fname   = $data["first_name"];
        $lname   = $data["last_name"];
        $email   = $data["email"];
        $password = $data["password"];

        if ($this->doesUserExist($email)) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        global $db;
        $stmt = $db->prepare("insert into customer (first_name, last_name, email, passhash) values (?, ?, ?, ?)");
        $stmt->bindParam(1, $fname);
        $stmt->bindParam(2, $lname);
        $stmt->bindParam(3, $email);
        $stmt->bindParam(4, $hash);
        $stmt->execute();
        return true;
    }

    public function Login($email, $password)
    {
        if (!$this->doesUserExist($email)) {
            echo "Does not exist";
            return null;
        }

        global $db;
        $stmt = $db->prepare("select * from customer where email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $res = $stmt->fetchAll()[0];
        $hash = $res["passhash"];

        if (!password_verify($password, $hash)) {
            return null;
        }

        return json_encode(["user" => $res ]);
    }
}
