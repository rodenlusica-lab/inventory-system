<?php
include 'db_config.php';
session_start();

// ==========================
// 1. GET PRODUCT
// ==========================
if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $res = $conn->query("SELECT * FROM products WHERE Product_ID = '$id'");

    if ($res && $res->num_rows > 0) {
        $product = $res->fetch_assoc();
    } else {
        die("Product not found.");
    }
}

// ==========================
// 2. UPDATE PRODUCT
// ==========================
if (isset($_POST['update_product'])) {

    $new_name  = $conn->real_escape_string($_POST['p_name']);
    $new_stock = (int)$_POST['p_stock'];
    $new_price = (float)$_POST['p_price'];
    $p_id      = $conn->real_escape_string($_POST['p_id']);

    $update_sql = "UPDATE products SET 
                   Product_Name = '$new_name', 
                   Stock_Quantity = $new_stock, 
                   Unit_Price = $new_price 
                   WHERE Product_ID = '$p_id'";

    if ($conn->query($update_sql)) {

        // ✅ ADD ACTIVITY LOG
        $conn->query("
            INSERT INTO activity_logs (action, product_name, details)
            VALUES ('UPDATE', '$new_name', 'Product updated (name/price/stock)')
        ");

        echo "<script>alert('Product Updated!'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
