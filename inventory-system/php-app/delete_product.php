<?php
session_start();
include 'db_config.php';

if (isset($_GET['id'])) {

    $id = $conn->real_escape_string($_GET['id']);

    // 1. Get product info
    $res = $conn->query("SELECT Product_Name FROM products WHERE Product_ID = '$id'");

    if ($res && $res->num_rows > 0) {

        $row = $res->fetch_assoc();
        $product_name = $conn->real_escape_string($row['Product_Name']);

        // 2. Delete product
        $delete = $conn->query("DELETE FROM products WHERE Product_ID = '$id'");

        if ($delete) {

            // 3. INSERT ACTIVITY LOG (IMPORTANT FIX)
            $conn->query("
                INSERT INTO activity_logs (action, product_name, details)
                VALUES ('DELETE', '$product_name', 'Product deleted from system')
            ");

        }
    }
}

// redirect always
header("Location: dashboard.php");
exit();
?>
