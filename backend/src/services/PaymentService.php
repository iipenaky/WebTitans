<?php

require_once __DIR__."/../db/db.php";

class PaymentService
{
    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM payment");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM payment WHERE payment_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function Add($payment)
    {
        global $db;
        $stmt = $db->prepare(
            "INSERT INTO payment ( order_id, payment_method, payment_time, amount, status) VALUES (:oid, :method, :time, :amnt, :status)"
        );
        $stmt->bindParam(":oid", $payment["order_id"]);
        $stmt->bindParam(":method", $payment["payment_method"]);
        $stmt->bindParam(":time", $payment["payment_time"]);
        $stmt->bindParam(":amnt", $payment["amount"]);
        $stmt->bindParam(":status", $payment["status"]);
        $stmt->execute();
        return $this->GetById($db->lastInsertId());
    }

    public function Update($payment)
    {
        global $db;
        $stmt = $db->prepare(
            "UPDATE payment SET order_id = :oid, payment_method = :method, payment_time = :time, amount = :amnt, status = :status WHERE payment_id = :id"
        );
        $stmt->bindParam(":id", $payment["payment_id"]);
        $stmt->bindParam(":oid", $payment["order_id"]);
        $stmt->bindParam(":method", $payment["payment_method"]);
        $stmt->bindParam(":time", $payment["payment_time"]);
        $stmt->bindParam(":amnt", $payment["amount"]);
        $stmt->bindParam(":status", $payment["status"]);
        $stmt->execute();
        return $this->GetById($db->lastInsertId());
    }

    public function Delete($id)
    {
        global $db;
        $stmt = $db->prepare("DELETE FROM payment WHERE payment_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return true;
    }
}
