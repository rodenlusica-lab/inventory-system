<?php
session_start();

// 🔐 LOGIN CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ERROR REPORTING
ini_set('display_errors', 1);
error_reporting(E_ALL);

// CONNECT DATABASE
require_once 'db_config.php';

// ============================
// 🔁 FETCH FROM PYTHON API (SAFE)
// ============================
$url = "https://python-api-whbs.onrender.com/products";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$response = curl_exec($ch);

// ✅ FIX: dili na mo-crash kung naay error
if (curl_errno($ch)) {
    $data = []; // fallback
} else {
    $data = json_decode($response, true);
}

curl_close($ch);

// ============================
// 💾 INSERT DATA (ONLY IF NAAY API DATA)
// ============================
if (!empty($data) && is_array($data)) {
    foreach ($data as $row) {

        $name = $conn->real_escape_string($row['Product_Name'] ?? '');
        $category = $conn->real_escape_string($row['Category'] ?? 'N/A');
        $stock = (int)($row['Stock_Quantity'] ?? 0);
        $price = (float)($row['Unit_Price'] ?? 0);
        $status = $conn->real_escape_string($row['Status'] ?? 'Available');

        if ($name == '') continue;

        // prevent duplicate
        $check = $conn->query("SELECT Product_ID FROM products WHERE Product_Name='$name'");

        if ($check && $check->num_rows == 0) {
            $pid = uniqid();

            $conn->query("
                INSERT INTO products (Product_ID, Product_Name, Category, Stock_Quantity, Unit_Price, Status)
                VALUES ('$pid', '$name', '$category', $stock, $price, '$status')
            ");
        }
    }
}

// ============================
// 📊 FETCH FROM DATABASE (MAIN DISPLAY)
// ============================
$result = $conn->query("SELECT * FROM products ORDER BY Product_Name ASC");

if (!$result) {
    die("SQL ERROR: " . $conn->error);
}

$total_on_display = $result->num_rows;
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<style>
body { font-family: Arial; margin: 0; background: #f4f7f6; }
nav { background: #2c3e50; padding: 15px; color: white; display: flex; justify-content: space-between; }
nav a { color: white; text-decoration: none; margin: 0 10px; }

.container { padding: 30px; }

.card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 220px;
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
        <a href="add_product.php">Add Product</a>
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
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Product_Name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Category']) . "</td>";
        echo "<td>" . (int)$row['Stock_Quantity'] . "</td>";
        echo "<td>₱" . number_format($row['Unit_Price'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>NO DATA</td></tr>";
}
?>
</tbody>
</table>

</div>

</body>
</html>
