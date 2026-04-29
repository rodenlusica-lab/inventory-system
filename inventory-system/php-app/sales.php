<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$result = null;

// IF SUBMITTED
if (isset($_POST['process_sale'])) {

    $product = $_POST['product'];
    $qty = (int)$_POST['qty'];

    $url = "https://python-api-whbs.onrender.com/process-sale";

    $data = json_encode([
        "product" => $product,
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
</style>
</head>

<body>

<div class="box">
<h2>Process Sale</h2>

<form method="POST">
    <input type="text" name="product" placeholder="Product Name" required>
    <input type="number" name="qty" placeholder="Quantity" required>
    <button type="submit" name="process_sale">Process</button>
</form>

<?php if (isset($result)) { ?>
    <h3>Result:</h3>
    <p><b>Product:</b> <?php echo $result['product']; ?></p>
    <p><b>Price:</b> ₱<?php echo $result['price']; ?></p>
    <p><b>Qty:</b> <?php echo $result['qty']; ?></p>
    <p><b>Total:</b> ₱<?php echo $result['total']; ?></p>
<?php } ?>

<?php if (isset($error)) { ?>
    <p style="color:red;">Error: <?php echo $error; ?></p>
<?php } ?>

</div>

</body>
</html>
