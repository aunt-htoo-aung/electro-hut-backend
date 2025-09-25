<?php
require_once 'db_connect.php';

//Get all orders
try {
    $sql = "
        SELECT 
            o.order_id,
            o.user_id,
            o.order_date,
            o.status,
            o.total_amount,
            COUNT(oi.order_item_id) AS item_count
        FROM Orders o
        LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
        GROUP BY o.order_id, o.user_id, o.order_date, o.status, o.total_amount
        ORDER BY o.order_date DESC
    ";
    $stmt = $conn->query($sql);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching orders: " . $e->getMessage();
}

// Get order details by ID
$order_id = $_GET['id'] ?? null;

if ($order_id) {
    try {
        $sql = "SELECT 
    oi.order_item_id,
    p.product_name,
    b.brand_name as brand,
    oi.quantity,
    oi.total_amount as amount,
    o.total_amount AS total,
    (SELECT image_url FROM Images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) AS image_url
    FROM Orders o
    LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
    LEFT JOIN Product p ON oi.product_id = p.product_id
    LEFT JOIN Brand b ON p.brand_id = b.brand_id
    LEFT JOIN Images i ON i.product_id = oi.product_id
    WHERE o.order_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$order_id]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching order items: " . $e->getMessage();
    }
}

// Get pending order
try {
    $sql = "SELECT * FROM Orders WHERE status = 'Pending' ORDER BY order_date DESC";
    $stmt = $conn->query($sql);
    $pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching orders: " . $e->getMessage();
}
