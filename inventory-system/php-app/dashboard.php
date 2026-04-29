<?php
// 1. Usa ra gyud ka session_start()
session_start();

// 2. Security Check (DAPAT NAA NI SA PINAKATAAS)
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// 3. Error Reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'classes/Database.php';
require_once 'classes/Product.php';
$db = new Database();

// 4. Default Session Limit
if (!isset($_SESSION['view_limit'])) {
    $_SESSION['view_limit'] = 60;
}

// 5. Logic sa View Limit (Delete/Add)
if (isset($_GET['deleted']) && $_GET['deleted'] == 'true') {
    $_SESSION['view_limit'] = max(0, $_SESSION['view_limit'] - 1);
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['added']) && $_GET['added'] == 'true') {
    $_SESSION['view_limit'] = $_SESSION['view_limit'] + 1;
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['reset'])) {
    $_SESSION['view_limit'] = 60;
    header("Location: dashboard.php");
    exit();
}

// 6. Query Products
$current_limit = $_SESSION['view_limit'];
$sql = "SELECT * FROM products ORDER BY Date_Received DESC, Product_ID DESC LIMIT $current_limit";
$result = $db->fetchAll($sql);
$total_on_display = ($result) ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBISM - Admin Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f4f7f6; }
        nav { background: #2c3e50; padding: 15px; color: white; display: flex; justify-content: space-between; align-items: center; }
        nav a { color: white; text-decoration: none; margin: 0 15px; font-weight: 500; }
        .container { padding: 30px; }
        .btn-sales { background: #27ae60; padding: 10px 20px; border-radius: 5px; color: white !important; }
        .summary-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 25px; display: inline-block; border-left: 5px solid #3498db; }
        .summary-card h3 { margin: 0; color: #7f8c8d; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .summary-card p { margin: 5px 0 0; font-size: 32px; font-weight: bold; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #3498db; color: white; text-transform: uppercase; font-size: 14px; }
        .edit-link { color: #3498db; text-decoration: none; font-weight: bold; margin-right: 10px; }
        .delete-link { color: #e74c3c; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <nav>
        <div class="logo"><strong>CLOUD BASED INVENTORY AND SALES MANAGEMENT SYSTEM</strong></div>
        <div class="menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="add_product.php">Add Product</a>
            <a href="reports.php">Reports</a>
            <a href="sales.php" class="btn-sales">🛒 Process Sales</a>
            <a href="logout.php" style="background-color: #ff4d4d; padding: 5px 10px; color: white; text-decoration: none; border-radius: 5px;">Logout</a>
        </div>
    </nav>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <div class="summary-card">
            <h3>TOTAL DISPLAYED ITEMS</h3>
            <p><?php echo $total_on_display; ?> Products</p> 
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $p = new Product($row);
                        echo "<tr>";
                            echo "<td>" . htmlspecialchars($p->name) . "</td>"; 
                            echo "<td>" . htmlspecialchars($p->category) . "</td>"; 
                            echo "<td>" . $p->stock . "</td>"; 
                            echo "<td>" . $p->getFormattedPrice() . "</td>"; 
                            echo "<td>" . $p->getStatusBadge() . "</td>";
                            echo "<td>
                                    <a href='edit_product.php?id=" . urlencode($row['Product_ID']) . "' class='edit-link'>✏️ Edit</a>
                                    <a href='delete_product.php?id=" . urlencode($row['Product_ID']) . "' class='delete-link' onclick=\"return confirm('Delete this item?')\">🗑️ Delete</a>
                                  </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>Empty.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <p><a href="dashboard.php?reset=1" style="color: #7f8c8d; font-size: 12px;">Reset View to 60</a></p>
    </div>
</body>
</html>