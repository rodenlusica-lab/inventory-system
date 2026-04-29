<?php 
include 'db_config.php'; 

if (isset($_POST['process_sale'])) {
    $p_id = $conn->real_escape_string($_POST['product_id']);
    $qty_ordered = (int)$_POST['qty'];

    // 🔗 Python API URL
    $url = "https://python-api-whbs.onrender.com/process-sale";

    $sql = "SELECT * FROM products WHERE Product_ID = '$p_id'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();

        $current_stock = $product['Stock_Quantity'];
        $product_name  = $product['Product_Name'];
        $price         = $product['Unit_Price'];

        if ($current_stock >= $qty_ordered) {

            // 🔥 CALL PYTHON API (OOP PROCESS)
            $data = [
                "product" => $product_name,
                "qty" => $qty_ordered
            ];

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);
            curl_close($ch);

            $result_api = json_decode($response, true);

            // ❗ check kung successful ang API
            if (!isset($result_api['total'])) {
                echo "Error sa Python API: " . $response;
                exit();
            }

            // ✅ total gikan Python (OOP)
            $total = $result_api['total'];

            // update stock
            $new_qty = $current_stock - $qty_ordered;

            // save record
            $save_sql = "INSERT INTO sales_records (product_name, quantity, total_price, sale_date) 
                         VALUES ('$product_name', $qty_ordered, $total, NOW())";
            
            $update_stock = "UPDATE products SET Stock_Quantity = $new_qty WHERE Product_ID = '$p_id'";
            
            if ($conn->query($update_stock) && $conn->query($save_sql)) {
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
