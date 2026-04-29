<?php
session_start();
include 'db_config.php'; 

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Para sa demo login
    if ($email == "admin@gmail.com" && $password == "admin123") {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php"); // Siguruha nga naa ni nga file
        exit();
    } else {
        echo "<script>alert('Sayop ang Email o Password!'); window.location='login.php';</script>";
    }
}
?>