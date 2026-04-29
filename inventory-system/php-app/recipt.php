<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            background: #f4f4f4; 
            display: flex; 
            justify-content: center; 
            align-items: flex-start;
            padding-top: 50px; 
            margin: 0;
        }
        .receipt { 
            background: white; 
            padding: 25px; 
            width: 320px; 
            border: 1px solid #ddd; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            border-radius: 4px;
        }
        .center { text-align: center; }
        .line { border-top: 1px dashed #000; margin: 15px 0; }
        .total-box { background: #f9f9f9; padding: 10px; border-radius: 4px; }
        .btn-dash { 
            display: block; 
            width: 100%; 
            padding: 12px; 
            background: #007bff; 
            color: white; 
            text-align: center; 
            text-decoration: none; 
            border-radius: 5px; 
            margin-top: 25px; 
            font-family: Arial, sans-serif; 
            font-weight: bold;
            box-sizing: border-box;
        }
        .btn-dash:hover { background: #0056b3; }
    </style>
</head>
<body>

<div class="receipt">
    <h2 class="center" style="margin-bottom: 5px;">CBISM STORE</h2>
    <p class="center" style="margin-top: 0; font-size: 14px;">Official Receipt</p>
    
    <div class="line"></div>
    
    <?php 
    // Safety check para dili mo-error kung walay data
    $name  = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : "N/A";
    $qty   = isset($_GET['qty']) ? htmlspecialchars($_GET['qty']) : "0";
    $total = isset($_GET['total']) ? number_format($_GET['total'], 2) : "0.00";
    ?>

    <p><strong>Item:</strong> <?php echo $name; ?></p>
    <p><strong>Qty :</strong> x<?php echo $qty; ?></p>
    
    <div class="line"></div>
    
    <div class="total-box">
        <h3 class="center" style="margin: 0;">TOTAL: ₱<?php echo $total; ?></h3>
    </div>
    
    <div class="line"></div>
    
    <p class="center" style="font-size: 13px;">*** Transaction Successful ***</p>
    <p class="center" style="font-size: 12px;">Thank you for your purchase!</p>

    <a href="dashboard.php" class="btn-dash">Back to Dashboard</a>
</div>

</body>
</html>