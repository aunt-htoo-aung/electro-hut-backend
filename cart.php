<?php
try {
    $query = "SELECT * FROM Cart";
    $stmt = $conn->query($query);
    $carts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $E->getMessage();
}
if (isset($_POST['addToCart'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    try {
        $cart_check_query = "SELECT * FROM Cart where user_id=? AND product_id=?";
        $stmt = $conn->prepare($cart_check_query);
        $stmt->execute([$user_id, $product_id]);
        $cart_result = $stmt->fetch();

        if ($cart_result) {
            $update_query = "UPDATE Cart SET quantity = quantity+1 WHERE user_id=? AND product_id=?";
            $stmt = $conn->prepare($update_query);
            $stmt->execute([$user_id, $product_id]);
        } else {
            $insert_query = "INSERT INTO Cart (user_id,product_id,quantity) VALUES (?,?,?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->execute([$user_id, $product_id, 1]);
        }
        header("location:index.html");
    } catch (PDOException $e) {
        $e->getMessage();
    }
}
