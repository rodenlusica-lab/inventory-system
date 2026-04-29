<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_config.php'; 

$error = "";

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ✅ Correct query
    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);

    // 🔥 IMPORTANT CHECK
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        $_SESSION['user_id'] = $user['id']; 
        
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CBISM</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { height: 100vh; display: flex; justify-content: center; align-items: center; background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://t3.ftcdn.net/jpg/11/98/32/02/360_F_1198320200_nSxbhnk6JUCB6j9EsxZvS0o9yXgvoFCu.jpg'); background-size: cover; }
        .login-form { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(15px); padding: 50px 40px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5); width: 100%; max-width: 420px; text-align: center; }
        h2 { color: #ffffff; font-size: 22px; margin-bottom: 30px; text-transform: uppercase; }
        .input-group { margin-bottom: 25px; text-align: left; }
        .input-group label { display: block; margin-bottom: 8px; font-size: 14px; color: #f1f1f1; }
        .input-group input { width: 100%; padding: 12px 15px; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px; outline: none; color: #fff; }
        .btn-login { width: 100%; padding: 14px; background: #00d2ff; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; text-transform: uppercase; }
        .error-msg { color: #ff4d4d; margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>

    <form class="login-form" method="POST" action="">
        <h2>Inventory & Sales Management</h2>
        
        <?php if($error != "") { echo "<p class='error-msg'>$error</p>"; } ?>

        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" name="login_btn" class="btn-login">Sign In</button>
    </form>

</body>
</html>
