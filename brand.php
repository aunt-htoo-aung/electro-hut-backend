<?php

// Get all brand
try {
    $query = "Select * From Brand";
    $stmt = $conn->query($query);
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

// Add Brand
try {
    $query = "INSERT INTO Brand (brand_name, brand_image_url) VALUES (?, ?)";

    $stmt = $conn->prepare($query);

    $stmt->execute([$categoryName, $categoryImageUrl]);

    echo "Category added successfully!";
} catch (PDOException $e) {
    echo "Error adding category: " . $e->getMessage();
}
