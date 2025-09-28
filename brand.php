<?php
require_once 'db_connect.php'; // Your DB connection

$upload_dir = '../electro-hut-img/';

// Ensure the folder exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD BRAND
    if (isset($_POST['add_brand'])) {
        $name = $_POST['brand_name'] ?? '';
        $image_url = '';

        // Handle image upload if exists
        if (isset($_FILES['brand_image']) && $_FILES['brand_image']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['brand_image']['tmp_name'];
            $filename = basename($_FILES['brand_image']['name']);
            $target_file = $upload_dir . $filename;

            if (move_uploaded_file($tmp_name, $target_file)) {
                // Store relative path (adjust path as needed for your app)
                $image_url = 'electro-hut-img/' . $filename;
            }
        }

        if ($name) {
            $stmt = $conn->prepare("INSERT INTO Brand (brand_name, brand_image_url) VALUES (?, ?)");
            $stmt->execute([$name, $image_url]);
            header("Location: brand.php");
            exit;
        }
    }

    // EDIT BRAND
    if (isset($_POST['edit_brand'])) {
        $id = $_POST['brand_id'] ?? 0;
        $name = $_POST['brand_name'] ?? '';
        $image_url = $_POST['existing_image_url'] ?? ''; // fallback if no new image uploaded

        // Handle image upload if new image is uploaded
        if (isset($_FILES['brand_image']) && $_FILES['brand_image']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['brand_image']['tmp_name'];
            $filename = basename($_FILES['brand_image']['name']);
            $target_file = $upload_dir . $filename;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_url = 'electro-hut-img/' . $filename;
            }
        }

        if ($id && $name) {
            $stmt = $conn->prepare("UPDATE Brand SET brand_name = ?, brand_image_url = ? WHERE brand_id = ?");
            $stmt->execute([$name, $image_url, $id]);
            header("Location: brand.php");
            exit;
        }
    }
}

// Fetch Brands with Product Count
$sql = "
    SELECT 
        b.brand_id, 
        b.brand_name, 
        b.brand_image_url,
        COUNT(p.product_id) AS total_products
    FROM Brand b
    LEFT JOIN Product p ON b.brand_id = p.brand_id
    GROUP BY b.brand_id, b.brand_name, b.brand_image_url
    ORDER BY b.brand_name ASC
";

$stmt = $conn->query($sql);
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
