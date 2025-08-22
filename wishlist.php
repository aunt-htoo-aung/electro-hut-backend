<?php
$user_id = $_SESSION['user_id'];

//Get Wishlist Data
try {
    $query = "SELECT * FROM Wishlist";
    $stmt = $conn->query($query);
    $wishlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $E->getMessage();
}

//Add or Delete Wishlist When Click Wishlist Button
if (isset($_POST['addWishlist'])) {
    $product_id = $_POST['product_id'];
    $check_wishlist = check_wishlist($user_id, $product_id, $conn);

    try {
        if ($wishlists) {
            $delete_query = "DELETE FROM Wishlist WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->execute([$userId, $productId]);
        } else {
            $insert_query = "INSERT INTO Wishlist (user_id,product_id) VALUES (?,?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->execute([$user_id, $product_id]);
        }
    } catch (PDOException $e) {
        $e->getMessage();
    }
    header("location:index.html");
}

//Delete Wishlist From Wishlist Page
if (isset($_POST['deleteWishlist'])) {
    $product_id = $_POST['product_id'];
    try {
        $delete_query = 'DELETE FROM Wishlist WHERE user_id=? AND product_id=?';
        $stmt = $conn->prepare($delete_query);
        $stmt->execute([$userId, $product_id]);
    } catch (PDOException $e) {
        $e->getMessage();
    }
}

//Check Wishlist is existed or not
function check_wishlist($user_id, $product_id, $conn)
{
    try {
        $wishlist_check_query = "SELECT * FROM Wishlist WHERE user_id=? AND product_id=?";
        $stmt = $conn->prepare($wishlist_check_query);
        $stmt->execute([$user_id, $product_id]);
        $wishlists = $stmt->fetch();
    } catch (PDOException $e) {
        $e->getMessage();
    }
    return $wishlists;
}
