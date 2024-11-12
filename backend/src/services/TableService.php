<?php

require_once __DIR__ . "/../db/db.php";

class TableService
{
    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare("select * from `table`");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function Add($data)
    {
        global $db;
        $stmt = $db->prepare("insert into `table` (table_number, seating_capacity, location) values (?, ?, ?)");
        $stmt->bindParam(1, $data["table_number"]);
        $stmt->bindParam(2, $data["seating_capacity"]);
        $stmt->bindParam(3, $data["location"]);
        $stmt->execute();
        return $this->GetById($db->lastInsertId());
    }

    public function Update($data)
    {
        global $db;
        $stmt = $db->prepare("update `table` set table_number = ?, seating_capacity = ?, location = ? where table_id = ?");
        $stmt->bindParam(1, $data["table_number"]);
        $stmt->bindParam(2, $data["seating_capacity"]);
        $stmt->bindParam(3, $data["location"]);
        $stmt->bindParam(4, $data["table_id"]);
        $stmt->execute();
        return $this->GetById($data["table_id"]);
    }

    public function Delete($id)
    {
        global $db;
        $stmt = $db->prepare("delete from `table` where table_id = ?");
        $stmt->bindParam(1, $id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function GetAvailable()
    {
        global $db;
        $stmt = $db->prepare("select * from `table` where table_id not in (select table_id from reservations)");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare("select * from `table` where table_id = ?");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
