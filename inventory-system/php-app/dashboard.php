<?php
session_start();

// 🔐 Login check
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ✅ FETCH DATA FROM PYTHON API (ONLY SOURCE)
$url = "https://python-api-whbs.onrender.com/products";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// fallback kung naay error
if (!$data) {
    $data = [];
}

// total count
$total_on_display = count($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBISM - Dashboard</title>
    <style>
        body { font-family: Arial; margin: 0; background: #f4f7f6; }
        nav { background: #2c3e50; padding: 15px; color: white; display: flex; justify-content: space-between; }
        nav a { color: white; text-decoration: none; margin: 0 10px; }
        .container { padding: 30px; }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 200px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #3498db;
            color: white;
        }
    </style>
</head>
<body>

<nav>
    <div><b>CLOUD INVENTORY SYSTEM</b></div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="sales.php">Sales</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">
    <h2>Admin Dashboard</h2>

    <div class="card">
        <b>Total Products</b><br>
        <?php echo $total_on_display; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
        <?php
        if (!empty($data)) {
            foreach ($data as $row) {

                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Product_Name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Category']) . "</td>";
                echo "<td>" . $row['Stock_Quantity'] . "</td>";
                echo "<td>₱" . number_format($row['Unit_Price'], 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>NO DATA FROM API</td></tr>";
        }
        ?>
        </tbody>
    </table>

</div>

</body>
</html>
