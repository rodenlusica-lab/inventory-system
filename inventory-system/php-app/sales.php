<?php 
include 'db_config.php'; 

if (isset($_POST['process_sale'])) {
    $p_id = $conn->real_escape_string($_POST['product_id']);
    $qty_ordered = (int)$_POST['qty'];

    $sql = "SELECT * FROM products WHERE Product_ID = '$p_id'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $current_stock = $product['Stock_Quantity'];
        $product_name  = $product['Product_Name'];
        $price         = $product['Unit_Price'];

        if ($current_stock >= $qty_ordered) {
            $new_qty = $current_stock - $qty_ordered;
            $total = $qty_ordered * $price;

            // 1. Save Record
            $save_sql = "INSERT INTO sales_records (product_name, quantity, total_price, sale_date) 
                         VALUES ('$product_name', $qty_ordered, $total, NOW())";
            
            // 2. Update Stock
            $update_stock = "UPDATE products SET Stock_Quantity = $new_qty WHERE Product_ID = '$p_id'";
            
            if ($conn->query($update_stock) && $conn->query($save_sql)) {
                // GI-FIX: Imbes dashboard.php, moadto na ta sa recipt.php dala ang data
                echo "<script>
                    alert('Sale Recorded Successfully!');
                    window.location.href = 'recipt.php?name=" . urlencode($product_name) . "&qty=$qty_ordered&total=$total'; 
                </script>";
                exit();
            } else {
                echo "Error: " . $conn->error;
            }
        } else {
            echo "<script>alert('Dili igo ang stock!'); window.location.href='sales.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Process Sale</title>
    <style>
        body { 
            background-image: url('https://comicbook.com/wp-content/uploads/sites/4/2024/04/b6b22bc0-41f5-4054-b206-9a1282d242ae.jpg?w=1024');
            background-size: cover; background-repeat: no-repeat; background-attachment: fixed;
            font-family: Arial, sans-serif; margin: 0;
            background: transparent;
        }
        nav { background: #2c3e50; padding: 15px; text-align: center; }
        nav a { color: white; text-decoration: none; margin: 0 15px; font-weight: bold; }
        .form-box { 
            background: rgba(255, 255, 255, 0.95); 
            padding: 25px; width: 350px; border-radius: 10px; 
            margin: 50px auto; box-shadow: 0 4px 15px rgba(0,0,0,0.3); 
        }
        input, select { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="inventory.php">Inventory</a>
        <a href="reports.php">Reports</a>
    </nav>

    <div class="form-box">
        <h2 style="text-align:center;">Sales Management</h2>
        <form method="POST">
            <label>Select Product (Latest 60):</label>
            <select name="product_id" required>
                <option value="">-- Choose Product --</option>
                <?php
                $res = $conn->query("SELECT * FROM products ORDER BY Date_Received DESC, Product_ID DESC LIMIT 60");
                while($row = $res->fetch_assoc()) {
                    echo "<option value='".htmlspecialchars($row['Product_ID'])."'>";
                    echo htmlspecialchars($row['Product_Name']) . " (" . $row['Stock_Quantity'] . ")";
                    echo "</option>";
                }
                ?>
            </select>
            <label>Quantity:</label>
            <input type="number" name="qty" min="1" required>
            <button type="submit" name="process_sale">Confirm Sale</button>
        </form>
    </div>
</body>
</html>