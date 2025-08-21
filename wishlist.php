<?php
try {
    $query = "SELECT * FROM Wishlist";
    $stmt = $conn->query($query);
    $wishlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $E->getMessage();
}
if (isset($_POST['addWishlist'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];

    try {
        $wishlist_check_query = "SELECT * FROM Cart where user_id=? AND product_id=?";
        $stmt = $conn->prepare($wishlist_check_query);
        $stmt->execute([$user_id, $product_id]);
        $wishlists = $stmt->fetch();

        if ($wishlists) {
            $delete_query = "DELETE FROM Wishlist WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->execute([$userId, $productId]);
        } else {
            $insert_query = "INSERT INTO Wishlist (user_id,product_id) VALUES (?,?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->execute([$user_id, $product_id]);
        }
        header("location:index.html");
    } catch (PDOException $e) {
        $e->getMessage();
    }
}
