<?php
session_start();

// login check
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ✅ USE THIS (NOT classes)
require_once 'db_config.php';

$message = "";

if (isset($_POST['save_product'])) {

    $name  = $conn->real_escape_string($_POST['p_name']);
    $price = (float)$_POST['p_price'];
    $stock = (int)$_POST['p_stock'];

    $random_id = rand(1000,9999);

    $sql = "INSERT INTO products 
            (Product_ID, Product_Name, Stock_Quantity, Unit_Price) 
            VALUES ('$random_id', '$name', $stock, $price)";

    if ($conn->query($sql)) {
        $message = "<div style='color:green'>✔️ Product Added</div>";
    } else {
        $message = "<div style='color:red'>❌ Error: ".$conn->error."</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - CBISM</title>
    <style>
        /* CSS STYLING */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        
        body { 
            height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            background-color: #f4f7f6; 
        }

        .form-card { 
            background: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 450px; 
        }

        h2 { color: #2c3e50; margin-bottom: 25px; text-align: center; }

        .input-group { margin-bottom: 20px; }
        
        label { 
            display: block; 
            margin-bottom: 8px; 
            color: #555; 
            font-weight: 600; 
            font-size: 14px;
        }

        input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            font-size: 15px;
            outline: none;
            transition: 0.3s;
        }

        input:focus { border-color: #3498db; box-shadow: 0 0 5px rgba(52,152,219,0.3); }

        .btn-save { 
            width: 100%; 
            padding: 14px; 
            background: #27ae60; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: bold; 
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .btn-save:hover { background: #219150; }

        .back-link { 
            display: block; 
            text-align: center; 
            margin-top: 15px; 
            text-decoration: none; 
            color: #3498db; 
            font-size: 14px; 
            font-weight: 500;
        }

        .back-link:hover { text-decoration: underline; }

        .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 14px; font-weight: bold; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

    <div class="form-card">
        <h2>Add New Product</h2>
        
        <?php echo $message; ?>

        <form method="POST">
            <div class="input-group">
                <label>Product Name</label>
                <input type="text" name="p_name" placeholder="Enter product name" required>
            </div>

            <div class="input-group">
                <label>Price (₱)</label>
                <input type="number" step="0.01" name="p_price" placeholder="0.00" required>
            </div>

            <div class="input-group">
                <label>Stock Quantity</label>
                <input type="number" name="p_stock" placeholder="0" required>
            </div>

            <button type="submit" name="save_product" class="btn-save">Save Product</button>
        </form>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

</body>
</html>
