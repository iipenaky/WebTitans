<?php

require_once __DIR__ . "/../db/db.php";

class MenuItemService
{
    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare("select * from menu_item");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare("select * from menu_item where menu_item_id = ?");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
