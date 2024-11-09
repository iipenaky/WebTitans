<?php

require_once __DIR__."/../db/db.php";

class StaffService{
    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM staff");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM staff WHERE staff_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function Update($id)
    {
        global $db;
        $stmt = $db->prepare(
            "UPDATE staff SET first_name = :first_name, last_name = :last_name, phone_number = :phone_number, email = :email, address = :address WHERE staff_id = :id"
        );
        $stmt->bindParam(":id", $staff["staff_id"]);
        $stmt->bindParam(":first_name", $staff["first_name"]);
        $stmt->bindParam(":last_name", $staff["last_name"]);
        $stmt->bindParam(":position", $staff["position"]);
        $stmt->bindParam(":email", $staff["email"]);
        $stmt->bindParam(":hire_date", $staff["hire_date"]);
        $stmt->bindParam(":salary", $staff["salary"]);
        $stmt->bindParam(":passhash", $staff["passhash"]);
        $stmt->execute();
        return $this->GetById($staff["customer_id"]);
    }
    public function Delete($id)
    {
        global $db;
        $stmt = $db->prepare("DELETE FROM staff WHERE staff_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return true;
    }
}  
