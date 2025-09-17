<?php

// Get all product
try {
    $query = "Select * From Product";
    $stmt = $conn->query($query);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

// Add Product
try {
    $query = "INSERT INTO Product (product_name, brand_id, category_id, price, stock_qty, description)
            VALUES (?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$productName, $brandId, $categoryId, $price, $stockQty, $description]);
} catch (PDOException $e) {
    echo "Error adding product: " . $e->getMessage();
}

// Delete Product with ID
try {
    $query = "DELETE FROM Product WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$productId]);
} catch (PDOException $e) {
    echo "Error deleting product: " . $e->getMessage();
}

// Update Product with ID
try {
    $sql = "UPDATE Product SET product_name = ?, brand_id = ?, category_id = ?, price = ?, stock_qty = ?, description = ? WHERE product_id =?";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$product_name, $brand_id, $category_id, $price, $stock_qty, $description, $product_id]);

    echo "Product updated successfully!";
} catch (PDOException $e) {
    echo "Error updating product: " . $e->getMessage();
}
