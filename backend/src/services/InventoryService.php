<?php

require_once __DIR__ . "/../db/db.php";

class InventoryService
{
    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare("select * from inventory");
        if (!$stmt->execute()) {
            return [
            "header" => "HTTP/1.1 500 Internal Server Error",
            "data" => ["error" => "An error occurred while trying to get all inventory"]
            ];
        }
        return [
            "header" => "HTTP/1.1 200 OK",
            "data" => $stmt->fetchAll()
        ];
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare("select * from inventory where inventory_id = ?");
        $stmt->bindParam(1, $id);
        if (!$stmt->execute()) {
            return [
            "header" => "HTTP/1.1 404 Not Found",
            "data" => ["error" => "inventory with id $id not found"]
            ];
        }
        return [
            "header" => "HTTP/1.1 200 OK",
            "data" => $stmt->fetch()
        ];
    }

    public function GetLowStock()
    {
        global $db;
        $stmt = $db->prepare("select * from inventory where quantity <= reorder_level");
        if (!$stmt->execute()) {
            return [
            "header" => "HTTP/1.1 500 Internal Server Error",
            "data" => ["error" => "An error occurred while trying to get low stock inventory"]
            ];
        }
        return [
            "header" => "HTTP/1.1 200 OK",
            "data" => $stmt->fetchAll()
        ];

    }
}
