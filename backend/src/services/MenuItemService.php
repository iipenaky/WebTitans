<?php

require_once __DIR__.'/../db/db.php';

class MenuItemService
{
    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare('select * from menu_item');
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'An error occurred while trying to get all menu items'],
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
        $stmt = $db->prepare('select * from menu_item where menu_item_id = ?');
        $stmt->bindParam(1, $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => "menu item with id $id not found"],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetch(),
        ];
    }
}
