<?php
include 'db_config.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================
// UPDATE PRODUCT (FIRST)
// ==========================
if (isset($_POST['update_product'])) {

    $new_name  = $conn->real_escape_string($_POST['p_name']);
    $new_stock = (int)$_POST['p_stock'];
    $new_price = (float)$_POST['p_price'];
    $p_id      = $conn->real_escape_string($_POST['p_id']);

    // kuha old data
    $old_res = $conn->query("SELECT * FROM products WHERE Product_ID = '$p_id'");
    $old = $old_res->fetch_assoc();

    $update_sql = "UPDATE products SET 
                   Product_Name = '$new_name', 
                   Stock_Quantity = $new_stock, 
                   Unit_Price = $new_price 
                   WHERE Product_ID = '$p_id'";

    if ($conn->query($update_sql)) {

        // ACTIVITY LOG
        $details = "Updated: ";

        if ($old['Product_Name'] != $new_name) {
            $details .= "Name '{$old['Product_Name']}' → '$new_name', ";
        }
        if ($old['Stock_Quantity'] != $new_stock) {
            $details .= "Stock {$old['Stock_Quantity']} → $new_stock, ";
        }
        if ($old['Unit_Price'] != $new_price) {
            $details .= "Price ₱{$old['Unit_Price']} → ₱$new_price, ";
        }

        $details = rtrim($details, ", ");

        $conn->query("
            INSERT INTO activity_logs (action, product_name, details)
            VALUES ('UPDATE', '$new_name', '$details')
        ");

        header("Location: dashboard.php");
        exit();
    } else {
        die("SQL ERROR: " . $conn->error);
    }
}

// ==========================
// GET PRODUCT (IMPORTANT)
// ==========================
if (isset($_GET['id'])) {

    $id = $conn->real_escape_string($_GET['id']);

    $res = $conn->query("SELECT * FROM products WHERE Product_ID = '$id'");

    if (!$res) {
        die("SQL ERROR: " . $conn->error);
    }

    if ($res->num_rows == 0) {
        die("❌ Product not found.");
    }

    $product = $res->fetch_assoc();

} else {
    die("❌ No ID provided.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <style>
        body { font-family: Arial; background: #f4f7f6; padding: 50px; }
        .edit-box { background: white; padding: 30px; max-width: 400px; margin: auto; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <div class="edit-box">
        <h2>Edit Product</h2>
        <form method="POST">
            <input type="hidden" name="p_id" value="<?php echo $product['Product_ID']; ?>">
            
            <label>Product Name:</label>
            <input type="text" name="p_name" value="<?php echo htmlspecialchars($product['Product_Name']); ?>" required>
            
            <label>Stock Quantity:</label>
            <input type="number" name="p_stock" value="<?php echo $product['Stock_Quantity']; ?>" required>
            
            <label>Unit Price (₱):</label>
            <input type="number" step="0.01" name="p_price" value="<?php echo $product['Unit_Price']; ?>" required>
            
            <button type="submit" name="update_product">Update Product Info</button>
            <p style="text-align:center;"><a href="dashboard.php" style="color:#7f8c8d; text-decoration:none; font-size:14px;">Cancel</a></p>
        </form>
    </div>
</body>
</html>
