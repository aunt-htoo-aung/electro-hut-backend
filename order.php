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
    o.*,
    oi.order_item_id,
    p.product_name,
    b.brand_name AS brand,
    oi.quantity,
    oi.total_amount AS amount,
    (
        SELECT image_url 
        FROM Images 
        WHERE product_id = p.product_id AND is_primary = 1 
        LIMIT 1
    ) AS image_url
    FROM Orders o
    LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
    LEFT JOIN Product p ON oi.product_id = p.product_id
    LEFT JOIN Brand b ON p.brand_id = b.brand_id
    WHERE o.order_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$order_id]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching order items: " . $e->getMessage();
    }
    try {
        $sql = "Select * from Payment where order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$order_id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching order deliver info : " . $e->getMessage();
    }
}

// Get pending order
$sql = "SELECT 
    o.*,
    CONCAT(u.first_name, ' ', u.last_name) AS user_name,
    (
      SELECT SUM(quantity) 
      FROM Order_Items oi 
      WHERE oi.order_id = o.order_id
    ) AS item_count
FROM Orders o
JOIN User u ON o.user_id = u.user_id
WHERE o.status = 'Pending'
ORDER BY o.order_date DESC;";
$stmt = $conn->query($sql);
$pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

//change status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];

    // Validate status
    $allowedStatuses = ['DELIVERED', 'CANCELED'];
    if (!in_array($newStatus, $allowedStatuses)) {
        die('Invalid status');
    }

    try {
        $sql = "UPDATE Orders SET status = :status WHERE order_id = :order_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':status' => $newStatus, ':order_id' => $orderId]);

        // Redirect back to the page (to avoid form resubmission)
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        echo "Error updating order status: " . $e->getMessage();
    }
}
