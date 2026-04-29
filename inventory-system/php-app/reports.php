<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Kon wala naka-login, pabalika sa login page
    exit();
}
?>
<?php include 'db_config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports - CBISM</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; background-color: #f4f7f6; }
        nav { background: #2c3e50; padding: 15px; margin-bottom: 20px; border-radius: 5px; display: flex; align-items: center; }
        nav a { color: white; text-decoration: none; margin-right: 20px; font-weight: bold; }
        nav a:hover { color: #3498db; }
        .container { background: white; padding: 25px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; margin-top: 0; }
        .report-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .box { border: 1px solid #eee; padding: 20px; border-radius: 8px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .full-width { grid-column: span 2; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px; }
        th, td { border-bottom: 1px solid #eee; padding: 12px; text-align: left; }
        th { background-color: #3498db; color: white; text-transform: uppercase; letter-spacing: 1px; }
        .low-stock { color: #e74c3c; font-weight: bold; background: #fdeaea; padding: 4px 8px; border-radius: 4px; }
        .badge-delete { color: white; background: #e74c3c; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .badge-add { color: white; background: #27ae60; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>

    <nav>
        <a href="dashboard.php">Inventory Dashboard</a>
        <a href="sales.php">Process Sale</a>
        <a href="reports.php">Reports Summary</a>
    </nav>

    <div class="container">
        <h1>Cloud System Reports</h1>
        <p>Dinhi nimo makita ang mga resibo, activity logs, ug stock analysis gikan sa Cloud.</p>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <div class="report-grid">
            
            <div class="box full-width">
                <h3>📑 Recent Sales Receipts (Cloud Records)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Qty</th>
                            <th>Total Price</th>
                            <th>Date/Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sales = $conn->query("SELECT * FROM sales_records ORDER BY sale_date DESC LIMIT 10");
                        if($sales && $sales->num_rows > 0) {
                            while($s = $sales->fetch_assoc()){
                                echo "<tr>
                                        <td>#".$s['id']."</td>
                                        <td>".htmlspecialchars($s['product_name'])."</td>
                                        <td>x".$s['quantity']."</td>
                                        <td>₱".number_format($s['total_price'], 2)."</td>
                                        <td>".$s['sale_date']."</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center;'>No receipts found in cloud.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="box full-width" style="border-top: 4px solid #e67e22;">
                <h3>🛠️ Inventory Activity Logs</h3>
                <p style="font-size: 13px; color: #666;">Monitoring sa mga gi-Add ug gi-Delete nga mga produkto.</p>
                <table>
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Product Name</th>
                            <th>Details</th>
                            <th>Date/Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Pagkuha sa data gikan sa activity_logs table nga imong gi-create
                        $logs = $conn->query("SELECT * FROM activity_logs ORDER BY log_date DESC LIMIT 10");
                        if($logs && $logs->num_rows > 0) {
                            while($l = $logs->fetch_assoc()){
                                $badge = ($l['action_type'] == 'DELETE') ? 'badge-delete' : 'badge-add';
                                echo "<tr>
                                        <td><span class='$badge'>".$l['action_type']."</span></td>
                                        <td>".htmlspecialchars($l['product_name'])."</td>
                                        <td>".$l['details']."</td>
                                        <td>".$l['log_date']."</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center;'>Walay nakit-an nga activity logs.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <h3>⚠️ Low Stock Alert</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Current Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Gamit ang Stock_Quantity base sa imong database structure
                        $res = $conn->query("SELECT * FROM products WHERE Stock_Quantity <= 10 ORDER BY Stock_Quantity ASC LIMIT 5");
                        if($res && $res->num_rows > 0) {
                            while($row = $res->fetch_assoc()){
                                echo "<tr>
                                        <td>".htmlspecialchars($row['Product_Name'])."</td>
                                        <td><span class='low-stock'>".$row['Stock_Quantity']."</span></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2' style='text-align:center;'>All stocks are healthy.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <h3>🏆 Top Selling Products</h3>
                <p style="font-size: 13px; color: #666;">Top items base sa Cloud Sales Receipts:</p>
                <ul style="padding-left: 20px; line-height: 2;">
                    <?php
                    $best = $conn->query("SELECT product_name, SUM(quantity) as total_sold FROM sales_records GROUP BY product_name ORDER BY total_sold DESC LIMIT 5");
                    if($best && $best->num_rows > 0) {
                        while($b = $best->fetch_assoc()){
                            echo "<li><b>".htmlspecialchars($b['product_name'])."</b> - Nahalin: <span style='color: #27ae60; font-weight:bold;'>".$b['total_sold']." units</span></li>";
                        }
                    } else {
                        echo "<li>No sales analysis yet.</li>";
                    }
                    ?>
                </ul>
            </div>

        </div>
    </div>

</body>
</html>