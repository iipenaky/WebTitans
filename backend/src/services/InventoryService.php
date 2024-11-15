<?php

require_once __DIR__.'/../db/db.php';

class InventoryService
{
    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare('select * from inventory');
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'An error occurred while trying to get all inventory'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetchAll(),
        ];
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare('select * from inventory where inventory_id = ?');
        $stmt->bindParam(1, $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => "inventory with id $id not found"],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetch(),
        ];
    }

    public function GetLowStock()
    {
        global $db;
        $stmt = $db->prepare('select * from inventory where quantity <= reorder_level');
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'An error occurred while trying to get low stock inventory'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetchAll(),
        ];

    }

    public function Restock($id, $quantity)
    {
        if ($quantity <= 0) {
            return [
                'header' => 'HTTP/1.1 400 Bad Request',
                'data' => ['error' => 'Quantity must be greater than 0'],
            ];
        }

        global $db;
        $stmt = $db->prepare('update inventory set quantity = quantity + ? where inventory_id = ?');
        $stmt->bindParam(1, $quantity);
        $stmt->bindParam(2, $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => "An error occurred while trying to restock inventory with id $id"],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => ['message' => "Successfully restocked inventory with id $id by $quantity units"],
        ];
    }
}
