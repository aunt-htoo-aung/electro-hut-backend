<?php
require_once 'db_connect.php';
// Get all product
try {
    $query = "SELECT 
    Product.*, 
    Images.image_url AS primary_image_url,
    Brand.brand_name,
    Category.category_name
    FROM 
    Product
    INNER JOIN Brand ON Product.brand_id = Brand.brand_id
    INNER JOIN Category ON Product.category_id = Category.category_id
    LEFT JOIN Images 
    ON Product.product_id = Images.product_id 
    AND Images.is_primary = TRUE;
";
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
    $upload_dir = '../electro-hut-img/';

    // Ensure the folder exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $filenames = $_FILES['image_urls']['name'];
    $tmp_names = $_FILES['image_urls']['tmp_name'];

    $primary_set = false;

    foreach ($filenames as $key => $original_name) {
        $tmp_path = $tmp_names[$key];

        // Optional: Add a unique name to avoid overwriting
        $safe_filename = time() . '_' . basename($original_name);
        $target_path = $upload_dir . $safe_filename;

        if (move_uploaded_file($tmp_path, $target_path)) {
            // First image is set as primary
            $is_primary = !$primary_set ? 1 : 0;
            $primary_set = true;

            // Store relative path or just the filename, depending on your frontend
            $db_image_url = 'electro-hut-img/' . $safe_filename;

            // Insert into Images table
            try {
                $stmt = $conn->prepare("INSERT INTO Images (product_id, image_url, is_primary)
                                    VALUES (?, ?, ?)");
                $stmt->execute([$productId, $db_image_url, $is_primary]);
            } catch (PDOException $e) {
                echo "Error adding product images: " . $e->getMessage();
            }
        } else {
            echo "Failed to move uploaded file: $original_name<br>";
        }
    }
}

// Delete Product with ID
// if (isset($_POST['delete_product'])) {
//     $productId = $_POST['product_id'];
//     try {
//         $query = "DELETE FROM Product WHERE product_id = ?";
//         $stmt = $conn->prepare($query);
//         $stmt->execute([$productId]);
//     } catch (PDOException $e) {
//         echo "Error deleting product: " . $e->getMessage();
//     }
// }
// Update Product with ID
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    try {
        $sql = "SELECT p.product_id, p.product_name, p.brand_id, p.category_id, p.price, p.stock_qty, p.description,
               b.brand_name, c.category_name,
               -- Get the primary image separately
               (SELECT image_url FROM Images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) AS primary_image_url,
               -- Get all other images
               GROUP_CONCAT(CASE WHEN i.is_primary = 0 THEN i.image_url END) AS other_images
                FROM Product p
                LEFT JOIN Brand b ON p.brand_id = b.brand_id
                LEFT JOIN Category c ON p.category_id = c.category_id
                LEFT JOIN Images i ON p.product_id = i.product_id
                WHERE p.product_id = $product_id
                GROUP BY p.product_id";
        $stmt = $conn->query($sql);
        $product_detail = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $e->getMessage();
    }
    if (isset($_POST['edit_product'])) {
        $product_name = $_POST['product_name'];
        $description = $_POST['description'];
        $categoryId = $_POST['category'];
        $brandId = $_POST['brand'];
        $price = $_POST['price'];
        $stock_amount = $_POST['stock_amount'];
        try {
            $sql = "UPDATE Product SET product_name = ?, brand_id = ?, category_id = ?, price = ?, stock_qty = ?, description = ? WHERE product_id =?";

            $stmt = $conn->prepare($sql);

            $stmt->execute([$product_name, $brand_id, $category_id, $price, $stock_qty, $description, $product_id]);

            echo "Product updated successfully!";
        } catch (PDOException $e) {
            echo "Error updating product: " . $e->getMessage();
        }
    }
}
