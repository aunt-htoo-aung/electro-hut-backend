<?php
require_once 'db_connect.php'; // Your DB connection

$upload_dir = '../electro-hut-img/';

// Ensure the folder exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD CATEGORY
    if (isset($_POST['add_category'])) {
        $name = $_POST['category_name'] ?? '';
        $image_url = '';

        // Handle image upload if exists
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['category_image']['tmp_name'];
            $filename = basename($_FILES['category_image']['name']);
            $target_file = $upload_dir . $filename;

            if (move_uploaded_file($tmp_name, $target_file)) {
                // Store relative path (adjust path as needed for your app)
                $image_url = 'electro-hut-img/' . $filename;
            }
        }

        if ($name) {
            $stmt = $conn->prepare("INSERT INTO Category (category_name, category_image_url) VALUES (?, ?)");
            $stmt->execute([$name, $image_url]);
            header("Location: category.php");
            exit;
        }
    }

    // EDIT CATEGORY
    if (isset($_POST['edit_category'])) {
        $id = $_POST['category_id'] ?? 0;
        $name = $_POST['category_name'] ?? '';
        $image_url = $_POST['existing_image_url'] ?? ''; // fallback if no new image uploaded

        // Handle image upload if new image is uploaded
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['category_image']['tmp_name'];
            $filename = basename($_FILES['category_image']['name']);
            $target_file = $upload_dir . $filename;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_url = 'electro-hut-img/' . $filename;
            }
        }

        if ($id && $name) {
            $stmt = $conn->prepare("UPDATE Category SET category_name = ?, category_image_url = ? WHERE category_id = ?");
            $stmt->execute([$name, $image_url, $id]);
            header("Location: category.php");
            exit;
        }
    }
}


// Fetch Categories with Product Count
$sql = "
    SELECT 
        c.category_id, 
        c.category_name, 
        c.category_image_url,
        COUNT(p.product_id) AS total_products
    FROM Category c
    LEFT JOIN Product p ON c.category_id = p.category_id
    GROUP BY c.category_id, c.category_name, c.category_image_url
    ORDER BY c.category_name ASC
";

$stmt = $conn->query($sql);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
