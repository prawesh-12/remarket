<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = getUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => 'Unknown error'];

    $order_id = intval($_POST['order_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if (!$order_id || !in_array($action, ['delivered', 'cancel'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }

    // ✅ Verify that the logged-in user is the seller of this order
    $check_sql = "
        SELECT o.*, p.id AS product_id 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        WHERE o.id = ? AND o.seller_id = ?
    ";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized or invalid order']);
        exit();
    }

    $order = $result->fetch_assoc();
    $stmt->close();

    // ✅ Use a transaction for safety
    $conn->begin_transaction();
    try {
        if ($action === 'delivered') {
            // Mark order as delivered
            $update_order = $conn->prepare("UPDATE orders SET status = 'delivered' WHERE id = ?");
            $update_order->bind_param("i", $order_id);
            $update_order->execute();

            // Keep product as sold
            $update_product = $conn->prepare("UPDATE products SET status = 'sold' WHERE id = ?");
            $update_product->bind_param("i", $order['product_id']);
            $update_product->execute();

            $conn->commit();
            $response = ['success' => true, 'message' => '✅ Order marked as delivered successfully!'];

        } elseif ($action === 'cancel') {
            // Mark order as cancelled
            $update_order = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
            $update_order->bind_param("i", $order_id);
            $update_order->execute();

            // Make product available again
            $update_product = $conn->prepare("UPDATE products SET status = 'available' WHERE id = ?");
            $update_product->bind_param("i", $order['product_id']);
            $update_product->execute();

            $conn->commit();
            $response = ['success' => true, 'message' => 'Order cancelled successfully!'];
        }

    } catch (Exception $e) {
        $conn->rollback();
        $response = ['success' => false, 'message' => 'Server error: failed to update order'];
    }

    echo json_encode($response);
    exit();
}

// ✅ If accessed directly (not via AJAX)
redirect('orders.php');
?>
