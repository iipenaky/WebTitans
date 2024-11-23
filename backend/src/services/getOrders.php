<?php
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_GET['user_id'] ?? null; // For user-specific orders
    $query = "SELECT o.order_id, od.menu_item_id, m.name as menu_item_name, od.quantity, o.status 
              FROM `order` o
              JOIN order_details od ON o.order_id = od.order_id
              JOIN menu_item m ON od.menu_item_id = m.menu_item_id";

    if ($userId) {
        $query .= " WHERE o.customer_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$userId]);
    } else {
        $stmt = $db->query($query);
    }

    $orders = $stmt->fetchAll();
    echo json_encode($orders);
}
?>
