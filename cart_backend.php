<?php
include "authenticate.php";
$user_id = $_SESSION['user_id'];

//Get Cart Data
try {
    $query = "SELECT * FROM Cart";
    $stmt = $conn->query($query);
    $carts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $E->getMessage();
}

//Add or Update to Cart When Click Add to Cart Button
if (isset($_POST['addToCart'])) {

    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $check_cart = check_cart($product_id, $user_id, $conn);

    try {
        if ($check_cart) {
            $update_query = 'UPDATE Cart SET qantity = quantity+1 WHERE user_id=? AND product_id=?';
            $stmt = $conn->prepare($update_query);
            $stmt->execute([$user_id, $product_id]);
        } else {
            $insert_query = 'INSERT INTO Cart (user_id,product_id,quantity) VALUES (?,?,?)';
            $stmt = $conn->prepare($insert_query);
            $stmt->execute([$user_id, $product_id, 1]);
        }
    } catch (PDOException $e) {
        $e->getMessage();
    }
    header('location:index.php');
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