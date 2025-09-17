<?php

// Get all product
try {
    $query = "Select * From Category";
    $stmt = $conn->query($query);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

// Add Category
try {
    $query = "INSERT INTO Category (category_name, category_image_url) VALUES (?, ?)";

    $stmt = $conn->prepare($query);

    $stmt->execute([$categoryName, $categoryImageUrl]);

    echo "Category added successfully!";
} catch (PDOException $e) {
    echo "Error adding category: " . $e->getMessage();
}
