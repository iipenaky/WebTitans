<?php
require_once '../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['customer_id'];
    $menuItemId = $_POST['menu_item_id'];
    $quantity = $_POST['quantity'];
    $paymentMethod = $_POST['payment_method'];
    $orderType = $_POST['order_type'];

    try {
        // Insert new order
        $stmt = $db->prepare("INSERT INTO `order` (customer_id, total_amount, status) VALUES (?, 0, 'Pending')");
        $stmt->execute([$customerId]);
        $orderId = $db->lastInsertId();

        // Insert order details
        $stmt = $db->prepare("INSERT INTO order_details (order_id, menu_item_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$orderId, $menuItemId, $quantity]);

        // Update inventory via the trigger
        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully!',
            'order_id' => $orderId,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
