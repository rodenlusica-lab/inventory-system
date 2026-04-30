<?php
include 'db_config.php';
session_start();

$result = null;
$error = "";

if (isset($_POST['process_sale'])) {

    $p_id = $_POST['product_id'];
    $qty = (int)$_POST['qty'];

    $url = "http://127.0.0.1:10000/process-sale";
    $data = json_encode([
        "product_id" => $p_id,
        "qty" => $qty
    ]);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
    } else {
        $result = json_decode($response, true);

        if (isset($result['error'])) {
            $error = $result['error'];
            $result = null;
        }
    }

    curl_close($ch);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Process Sale</title>
<style>
body { font-family: Arial; background:#f4f7f6; padding:50px; }
.box { background:white; padding:30px; max-width:400px; margin:auto; border-radius:8px; }
input, button { width:100%; padding:10px; margin:10px 0; }
button { background:#27ae60; color:white; border:none; }
.result { background:#ecf0f1; padding:15px; margin-top:15px; border-radius:6px; }
.error { color:red; margin-top:10px; }
</style>
</head>

<body>

<div class="box">
<h2>Process Sale</h2>

<form method="POST">

<form id="saleForm">
  <label>Select Product:</label>
  <select name="product_id" id="product_id" required>
    <option value="">-- Choose Product --</option>
    <?php
      $res = $conn->query("SELECT * FROM products ORDER BY Product_Name ASC");
      while ($row = $res->fetch_assoc()) {
        echo "<option value='{$row['Product_ID']}'>
              {$row['Product_Name']} | Stock: {$row['Stock_Quantity']} | ₱{$row['Unit_Price']}
              </option>";
      }
    ?>
  </select>

  <label>Quantity:</label>
  <input type="number" name="qty" id="qty" min="1" required>

  <button type="submit">Process</button>
</form>

<div id="resultBox"></div>
<div id="errorBox" style="color:red;"></div>
</form>

<!-- ERROR -->
<?php if (!empty($error)) { ?>
    <div class="error"><?php echo $error; ?></div>
<?php } ?>

<!-- RESULT -->
<?php if (!empty($result)) { ?>
<div class="result">
    <p><b>Product:</b> <?php echo $result['product']; ?></p>
    <p><b>Price:</b> ₱<?php echo $result['price']; ?></p>
    <p><b>Qty:</b> <?php echo $result['qty']; ?></p>
    <p><b>Total:</b> ₱<?php echo $result['total']; ?></p>
    <p><b>Remaining Stock:</b> <?php echo $result['remaining_stock']; ?></p>
</div>
<?php } ?>

</div>

</body>
</html>
