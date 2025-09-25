<?php
require_once 'cart.php';

$user_id = $_SESSION['user_id'];
if (isset($_POST['submit']) && $_POST['count'] > 0) {

    try {
        // Gather data from the form
        $address = $_POST['address'];
        $city = $_POST['city'];
        $phone = $_POST['phone'];
        $total_amount = $_POST['total']; // Total from your cart
        $user_id = $_SESSION['userId']; // Assuming the user is logged in

        $sql = "INSERT INTO `orders` (user_id, total_amount, address, city, contact_phone,status) VALUES (?, ?, ?, ?, ?,?)";
        // Insert into `order` table
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $total_amount, $address, $city, $phone, 'PENDING']);
        $order_id = $conn->lastInsertId();
        // Insert into `order_items` table
        if (isset($carts)) {
            $stmt = $conn->prepare("INSERT INTO `order_items` (order_id, product_id, quantity, total_amount) VALUES (?, ?, ?, ?)");
            foreach ($carts as $item) {
                $product_id = $item['product_id'];

                $quantity = $item['quantity'];
                $amount = $item['quantity'] * $item['price'];
                $stmt->execute([$order_id, $product_id, $quantity, $amount]);
            }
        }

        // Insert into `payment` table
        $payment_method = $_POST['flexRadioDefault'];
        $card_name = $_POST['card_name'];
        $card_num = $_POST['card_num'];
        $transition_num = $_POST['transition_num'];

        $stmt = $conn->prepare("INSERT INTO `payment` (order_id, method, name, card_number, transition_number) VALUES (?, ?, ?, ?, ?)");

        $stmt->execute([$order_id, $payment_method, $card_name, $card_num, $transition_num]);

        $stmt = $conn->prepare("DELETE FROM Cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        header('location:../electro-hut/order.php');
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
