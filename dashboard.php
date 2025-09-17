<?php
try {
    $query = "SELECT 
            (
                SELECT image_url 
                FROM Images 
                WHERE product_id = p.product_id 
                ORDER BY is_primary DESC, image_id ASC 
                LIMIT 1
            ) AS image_url,
            
            p.product_name,
            b.brand_name,
            c.category_name,
            p.price,
            SUM(oi.quantity) AS sold_quantity,
            SUM(oi.quantity * p.price) AS total_revenue,
            p.stock_qty AS stock_left

        FROM 
            Order_Items oi
        JOIN 
            Product p ON oi.product_id = p.product_id
        JOIN 
            Brand b ON p.brand_id = b.brand_id
        JOIN 
            Categories c ON p.category_id = c.category_id

        GROUP BY 
            p.product_id,
            p.product_name,
            b.brand_name,
            c.category_name,
            p.price,
            p.stock_qty

        ORDER BY 
            sold_quantity DESC
        LIMIT 10
    ";

    $stmt = $conn->query($query);
    $top_selling_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

try {
    $query = "SELECT
    COUNT(*) AS total_products,
    SUM(CASE WHEN stock_qty > 0 THEN 1 ELSE 0 END) AS active_stock_products,
    SUM(CASE WHEN stock_qty = 0 THEN 1 ELSE 0 END) AS inactive_stock_products
FROM Product;
";
    $stmt = $conn->query($query);
    $total_stock_products = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}
