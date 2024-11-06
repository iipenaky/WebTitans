<?php

require_once __DIR__."/../db/db.php";

class CustomerService
{
    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM customer");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM customer WHERE customer_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function Add($customer)
    {
        global $db;
        $stmt = $db->prepare(
            "INSERT INTO customer ( first_name, last_name, phone_number, email, address) VALUES (:first_name, :last_name, :phone_number, :email, :address)"
        );
        $stmt->bindParam(":first_name", $customer["first_name"]);
        $stmt->bindParam(":last_name", $customer["last_name"]);
        $stmt->bindParam(":phone_number", $customer["phone_number"]);
        $stmt->bindParam(":email", $customer["email"]);
        $stmt->bindParam(":address", $customer["address"]);
        $stmt->execute();
        return $this->GetById($db->lastInsertId());
    }

    public function Update($customer)
    {
        global $db;
        $stmt = $db->prepare(
            "UPDATE customer SET first_name = :first_name, last_name = :last_name, phone_number = :phone_number, email = :email, address = :address WHERE customer_id = :id"
        );
        $stmt->bindParam(":id", $customer["customer_id"]);
        $stmt->bindParam(":first_name", $customer["first_name"]);
        $stmt->bindParam(":last_name", $customer["last_name"]);
        $stmt->bindParam(":phone_number", $customer["phone_number"]);
        $stmt->bindParam(":email", $customer["email"]);
        $stmt->bindParam(":address", $customer["address"]);
        $stmt->execute();
        return $this->GetById($customer["customer_id"]);
    }

    public function Delete($id)
    {
        global $db;
        $stmt = $db->prepare("DELETE FROM customer WHERE customer_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return true;
    }
}
