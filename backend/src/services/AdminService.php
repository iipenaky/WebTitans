<?php

require_once __DIR__."/../db/db.php";

class AdminService
{
    private function doesUserExist($username)
    {
        global $db;
        $stmt = $db->prepare("select username from admin where username = :uname");
        $stmt->bindParam(":uname", $username);
        $stmt->execute();
        $res = $stmt->fetchAll();
        return sizeof($res) >= 1;
    }

    private function doesStaffExist($email)
    {
        global $db;
        $stmt = $db->prepare("select email from staff where email = ?");
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $res = $stmt->fetchAll();
        return sizeof($res) >= 1;
    }

    public function SignUp($username, $password)
    {
        if ($this->doesUserExist($username)) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        global $db;
        $stmt = $db->prepare("insert into admin (username, passhash) values (:uname, :hash);");
        $stmt->bindParam(":uname", $username);
        $stmt->bindParam(":hash", $hash);
        $stmt->execute();
        return true;
    }

    public function Login($username, $password)
    {
        if (!$this->doesUserExist($username)) {
            return null;
        }

        global $db;
        $stmt = $db->prepare("select * from admin where username = ?");
        $stmt->bindParam(1, $username);
        $stmt->execute();
        $res = $stmt->fetchAll()[0];
        $hash = $res["passhash"];

        if (!password_verify($password, $hash)) {
            return null;
        }

        return json_encode(["user" => $res ]);
    }


    public function SignUpStaff($data)
    {
        $fname   = $data["first_name"];
        $lname   = $data["last_name"];
        $pos = $data["position"];
        $email   = $data["email"];
        $salary = $data["salary"];
        $password = $data["password"];

        if ($this->doesStaffExist($email)) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        global $db;
        $stmt = $db->prepare("insert into staff (first_name, last_name, position, email, salary, passhash) values (?, ?, ?,  ?, ?, ?)");
        $stmt->bindParam(1, $fname);
        $stmt->bindParam(2, $lname);
        $stmt->bindParam(3, $pos);
        $stmt->bindParam(4, $email);
        $stmt->bindParam(5, $salary);
        $stmt->bindParam(6, $hash);
        $stmt->execute();
        return true;
    }

    public function LoginStaff($email, $password)
    {
        if (!$this->doesStaffExist($email)) {
            return null;
        }

        global $db;
        $stmt = $db->prepare("select * from staff where email = ?");
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
