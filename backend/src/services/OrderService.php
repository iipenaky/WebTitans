<?php

require_once __DIR__."/../db/db.php";

class OrderService
{
    public function GetAll()
    {
        $query = <<<SQL
select
	customer.customer_id,
	`order`.order_id,
staff_id,
	first_name,
	last_name,
	email,
	name,
	order_time,
	total_amount,
	status,
	quantity
from
	`order`
inner join order_details on
	`order`.order_id = order_details.order_id
inner join menu_item on
	order_details.menu_item_id = menu_item.menu_item_id
inner join customer on
	`order`.customer_id = customer.customer_id;
SQL;
        global $db;
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM `order` WHERE order_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function GetByCustomerId($id)
    {
        global $db;
        $query = <<<SQL
        select
customer_id,
`order`.order_id,
            name,
            order_time,
            total_amount,
            status,
            quantity,
            description
        from
            `order`
        inner join order_details on
            `order`.order_id = order_details.order_id
        inner join menu_item on
            order_details.menu_item_id = menu_item.menu_item_id
        where 
            customer_id = ?;
SQL;
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function GetByStaffId($id)
    {
        global $db;
        $query = <<<SQL
        SELECT
            customer_id,
            `order`.order_id,
            staff_id
        from
            `order`
        where 
            staff_id = ?;
SQL;
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetchAll();
    }



    public function Add($orderAndOrderDetails)
    {
        $order = $orderAndOrderDetails["order"];
        $orderDetails = $orderAndOrderDetails["order_details"];

        global $db;
        $stmt = $db->prepare(
            "INSERT INTO `order` ( customer_id, staff_id, total_amount) VALUES (:cid, :sid, :amnt)"
        );
        $stmt->bindParam(":cid", $order["customer_id"]);
        $stmt->bindParam(":sid", $order["staff_id"]);
        $stmt->bindParam(":amnt", $order["total_amount"]);
        $stmt->execute();
        $orderId = $db->lastInsertId();

        $stmt = $db->prepare(
            "INSERT INTO order_details (order_id, menu_item_id, quantity) VALUES (:oid, :mid, :qty)"
        );

        foreach ($orderDetails as $orderDetail) {
            $stmt->bindParam(":oid", $orderId);
            $stmt->bindParam(":mid", $orderDetail["menu_item_id"]);
            $stmt->bindParam(":qty", $orderDetail["quantity"]);
            $stmt->execute();
        }

        return $this->GetById($orderId);
    }

    public function Update($order)
    {
        global $db;
        if ($this->GetById($order["order_id"]) == null) {
            return null;
        }

        $stmt = $db->prepare(
            "UPDATE `order` SET customer_id = :cid, staff_id = :sid, total_amount = :amnt, status = :status WHERE order_id = :id"
        );
        $stmt->bindParam(":id", $order["order_id"]);
        $stmt->bindParam(":cid", $order["customer_id"]);
        $stmt->bindParam(":sid", $order["staff_id"]);
        $stmt->bindParam(":amnt", $order["total_amount"]);
        $stmt->bindParam(":status", $order["status"]);
        $stmt->execute();
        return $this->GetById($db->lastInsertId());
    }

    public function Delete($id)
    {
        global $db;
        $stmt = $db->prepare("DELETE FROM `order` WHERE order_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return true;
    }
}
