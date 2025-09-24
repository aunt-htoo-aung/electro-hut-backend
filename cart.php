<?php
require_once 'db_connect.php';
include "authenticate.php";

$user_id = $_SESSION['user_id'];

//Get Cart Data
try {
    $query = "SELECT 
    c.cart_id,
    c.quantity,
    p.product_name,
    b.brand_name,
    p.stock_qty,
    p.price,
    i.image_url AS primary_image_url
    FROM Cart c
    JOIN Product p ON c.product_id = p.product_id
    JOIN Brand b ON p.brand_id = b.brand_id
    JOIN Images i ON i.product_id = p.product_id AND i.is_primary = 1
    WHERE c.user_id = $user_id";
    $stmt = $conn->query($query);
    $carts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $e->getMessage();
}

//Add or Update to Cart When Click Add to Cart Button
if (isset($_POST['addToCart'])) {

    $product_id = $_POST['product_id'];

    $check_cart = check_cart($product_id, $user_id, $conn);

    try {
        if ($check_cart) {
            $update_query = 'UPDATE Cart SET quantity = quantity+ 1 WHERE user_id=? AND product_id=?';
            $stmt = $conn->prepare($update_query);
            $stmt->execute([$user_id, $product_id]);
            echo "success update cart";
        } else {
            $insert_query = 'INSERT INTO Cart (user_id,product_id,quantity) VALUES (?,?,?)';
            $stmt = $conn->prepare($insert_query);
            $stmt->execute([$user_id, $product_id, 1]);
            echo "success add cart";
        }
        header('location:cart.php');
    } catch (PDOException $e) {
        $e->getMessage();
    }
    exit;
}

//Delete Cart From Cart Page
if (isset($_POST['deleteCart'])) {
    $product_id = $_POST['product_id'];
    try {
        $delete_query = "DELETE FROM Cart WHERE user_id=? AND product_id=?";
        $stmt = $conn->prepare($delete_query);
        $stmt->execute([$user_id, $product_id]);
    } catch (PDOException $e) {
        $e->getMessage();
    }
    header('location:cart.php');
    exit;
}

//Update Cart From Cart Page
if (isset($_POST['updateCart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    try {
        $update_query = 'UPDATE Cart SET quantity=? WHERE user_id=? AND product_id=?';
        $stmt = $conn->prepare($update_query);
        $stmt->execute([$quantity, $user_id, $product_id]);
    } catch (PDOException $e) {
        $e->getMessage();
    }
    header('location:cart.php');
    exit;
}

//Check Cart is existed or not
function check_cart($product_id, $user_id, $conn)
{
    try {
        $cart_check_query = "SELECT * FROM Cart where user_id=? AND product_id=?";
        $stmt = $conn->prepare($cart_check_query);
        $stmt->execute([$user_id, $product_id]);
        $cart_result = $stmt->fetch();
    } catch (PDOException $e) {
        $e->getMessage();
    }
    return $cart_result;
}
