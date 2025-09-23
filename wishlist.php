<?php
require_once 'db_connect.php';
require_once "authenticate.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check Wishlist is existed or not
function check_wishlist($user_id, $product_id, $conn)
{
    try {
        $wishlist_check_query = "SELECT * FROM Wishlist WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($wishlist_check_query);
        $stmt->execute([$user_id, $product_id]);
        return $stmt->fetch() ? true : false;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
// Now, the function is defined and can be called from anywhere below.

$user_id = $_SESSION['user_id'] ?? null;

// Get Wishlist Data for logged-in user
try {
    $query = "SELECT * FROM Wishlist WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id]);
    $wishlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Add or Delete Wishlist When Click Wishlist Button
if (isset($_POST['addWishlist'])) {
    $product_id = $_POST['product_id'];
    $check_wishlist = check_wishlist($user_id, $product_id, $conn); // This call is now valid.
    try {
        if ($check_wishlist) {
            $delete_query = "DELETE FROM Wishlist WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($delete_query);
            $stmt->execute([$user_id, $product_id]);
        } else {
            $insert_query = "INSERT INTO Wishlist (user_id, product_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->execute([$user_id, $product_id]);
        }

        header("location:wishlist.php");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}

// Delete Wishlist From Wishlist Page
if (isset($_POST['deleteWishlist'])) {
    $product_id = $_POST['product_id'];
    try {
        $delete_query = 'DELETE FROM Wishlist WHERE user_id = ? AND product_id = ?';
        $stmt = $conn->prepare($delete_query);
        $stmt->execute([$user_id, $product_id]);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    header('location:wishlist.php');
    exit;
}
