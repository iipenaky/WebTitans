<?php

require_once __DIR__.'/../db/db.php';
require_once __DIR__.'/./MenuItemService.php';

class OrderService
{
    public function GetAll()
    {
        $query = <<<'SQL'
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
menu_item.price,
	status,
	quantity
from
	`order`
inner join order_details on
	`order`.order_id = order_details.order_id
inner join menu_item on
	order_details.menu_item_id = menu_item.menu_item_id
inner join customer on
	`order`.customer_id = customer.customer_id
        order by order_id asc;
SQL;
        global $db;
        $stmt = $db->prepare($query);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Error fetching all orders '],
            ];
        }
        $res = $stmt->fetchAll();

        $grouped = [];
        foreach ($res as $row) {
            $orderId = $row['order_id'];
            if (! isset($grouped[$orderId])) {
                $grouped[$orderId] = [
                    'order_id' => $orderId,
                    'customer_id' => $row['customer_id'],
                    'staff_id' => $row['staff_id'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'order_time' => $row['order_time'],
                    'total_amount' => $row['total_amount'],
                    'status' => $row['status'],
                    'items' => [],
                ];
            }

            $grouped[$orderId]['items'][] = [
                'name' => $row['name'],
                'price' => $row['price'],
                'quantity' => $row['quantity'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => array_values($grouped),
        ];
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare('SELECT * FROM `order` WHERE order_id = :id');
        $stmt->bindParam(':id', $id);
        if (! $stmt->execute()) {

            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => "Order with id $id not found"],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetch(),
        ];
    }

    public function GetByCustomerId($id)
    {
        global $db;
        $query = <<<'SQL'
        select
customer_id,
`order`.order_id,
staff_id,
menu_item.price,
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
            customer_id = ? and status <> "Cancelled";
SQL;
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => "Order with customer id $id not found"],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetchAll(),
        ];
    }

    public function GetByStaffId($id)
    {
        global $db;
        $query = <<<'SQL'
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
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => "Order with staff id $id not found"],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetchAll(),
        ];
    }

    public function Add($orderAndOrderDetails)
    {
        $MenuItemService = new MenuItemService;
        $order = $orderAndOrderDetails['order'];
        $orderDetails = $orderAndOrderDetails['order_details'];

        $totalAmount = 0;
        foreach ($orderDetails as $detail) {
            $info = $MenuItemService->GetById($detail['menu_item_id'])['data'];
            $totalAmount += $info['price'] * $detail['quantity'];
        }

        global $db;
        try {
            $db->beginTransaction();
            $stmt = $db->prepare(
                'INSERT INTO `order` ( customer_id, staff_id, total_amount) VALUES (:cid, :sid, :amnt)'
            );
            $stmt->bindParam(':cid', $order['customer_id']);
            $stmt->bindParam(':sid', $order['staff_id']);
            $stmt->bindParam(':amnt', $totalAmount);
            if (! $stmt->execute()) {
                $db->rollBack();

                return [
                    'header' => 'HTTP/1.1 500 Internal Server Error',
                    'data' => ['error' => 'Failed to add order'],
                ];
            }
            $orderId = $db->lastInsertId();

            $stmt = $db->prepare(
                'INSERT INTO order_details (order_id, menu_item_id, quantity) VALUES (:oid, :mid, :qty)'
            );

            foreach ($orderDetails as $orderDetail) {
                $stmt->bindParam(':oid', $orderId);
                $stmt->bindParam(':mid', $orderDetail['menu_item_id']);
                $stmt->bindParam(':qty', $orderDetail['quantity']);
                if (! $stmt->execute()) {
                    $db->rollBack();

                    return [
                        'header' => 'HTTP/1.1 500 Internal Server Error',
                        'data' => ['error' => 'Failed to add order details'],
                    ];
                }
            }
            $db->commit();

            return $this->GetById($orderId);
        } catch (Exception $e) {
            $db->rollBack();

            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to add order'],
            ];
        }
    }

    public function Update($order)
    {
        global $db;
        $res = $this->GetById($order['order_id']);
        if (! isset($res['data']) || ! is_array($res['data']) || count($res['data']) === 0) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => 'Order not found'],
            ];
        }

        $stmt = $db->prepare(
            'UPDATE `order` SET customer_id = :cid, staff_id = :sid, total_amount = :amnt, status = :status WHERE order_id = :id'
        );

        $stmt->bindParam(':id', $order['order_id']);
        $stmt->bindParam(':cid', $order['customer_id']);
        $stmt->bindParam(':sid', $order['staff_id']);
        $stmt->bindParam(':amnt', $order['total_amount']);
        $stmt->bindParam(':status', $order['status']);

        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to update order'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $this->GetById($db->lastInsertId())['data'],
        ];
    }

    public function Delete($id)
    {
        global $db;
        $res = $this->GetById($id);
        if (! isset($res['data']) || ! is_array($res['data']) || count($res['data']) === 0) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => 'Order not found'],
            ];
        }
        $stmt = $db->prepare('DELETE FROM `order` WHERE order_id = :id');
        $stmt->bindParam(':id', $id);

        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to delete order'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => ['message' => 'Order deleted successfully'],
        ];

    }
}
