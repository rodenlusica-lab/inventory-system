<?php
include 'db_config.php';
session_start();

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    
    // 1. Kuhaa una ang name sa product sa dili pa i-delete
    $res = $conn->query("SELECT Product_Name FROM products WHERE Product_ID = '$id'");
    if($res && $res->num_rows > 0) {
        $p_data = $res->fetch_assoc();
        $p_name = $conn->real_escape_string($p_data['Product_Name']);

        // 2. I-delete na ang product
        $sql = "DELETE FROM products WHERE Product_ID = '$id'";

        if ($conn->query($sql)) {
            // 3. I-record sa activity_logs nga gi-papas ni nimo
            $conn->query("INSERT INTO activity_logs (action_type, product_name, details) 
                          VALUES ('DELETE', '$p_name', 'Product permanently removed from inventory')");
            
            // 4. Balik sa dashboard nga mubu-an ang limit (Smart Count)
            header("Location: dashboard.php?deleted=true");
            exit();
        }
    }
}
header("Location: dashboard.php");
?>