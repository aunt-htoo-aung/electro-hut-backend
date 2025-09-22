<?php
require_once 'db_connect.php';
// Get all product
try {
    $query = "Select * From Product";
    $stmt = $conn->query($query);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

// Add Product
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $categoryId = $_POST['category'];
    $brandId = $_POST['brand'];
    $price = $_POST['price'];
    $stock_amount = $_POST['stock_amount'];

    try {
        $sql = "INSERT INTO Product (product_name, brand_id, category_id, price, stock_qty, description)
            VALUES (?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);

        $stmt->execute([$product_name, $brandId, $categoryId, $price, $stock_amount, $description]);

        $productId = $conn->lastInsertId();
    } catch (PDOException $e) {
        echo "Error adding product: " . $e->getMessage();
    }
    $filename = $_FILES['image_urls']['name'];


    $primary_set = false;
    foreach ($filename as $key => $tmp_name) {
        $url_path = '../images/' . $tmp_name;
        move_uploaded_file($tmp_name, $url_path);
        // First image is set as primary
        $is_primary = !$primary_set ? 1 : 0;
        $primary_set = true;

        // Insert into Images table
        try {
            $stmt = $conn->prepare("INSERT INTO Images (product_id, image_url, is_primary)
                                VALUES (?, ?, ?)");
            $stmt->execute([$productId, $url_path, $is_primary]);
        } catch (PDOException $e) {
            echo "Error adding product images: " . $e->getMessage();
        }
    }
}

// Delete Product with ID
// try {
//     $query = "DELETE FROM Product WHERE product_id = ?";
//     $stmt = $conn->prepare($query);
//     $stmt->execute([$productId]);
// } catch (PDOException $e) {
//     echo "Error deleting product: " . $e->getMessage();
// }

// // Update Product with ID
// try {
//     $sql = "UPDATE Product SET product_name = ?, brand_id = ?, category_id = ?, price = ?, stock_qty = ?, description = ? WHERE product_id =?";

//     $stmt = $conn->prepare($sql);

//     $stmt->execute([$product_name, $brand_id, $category_id, $price, $stock_qty, $description, $product_id]);

//     echo "Product updated successfully!";
// } catch (PDOException $e) {
//     echo "Error updating product: " . $e->getMessage();
// }
