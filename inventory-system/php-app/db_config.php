<?php
// Gikan sa imong InfinityFree 'MySQL Connection Details'
$servername = "sql211.infinityfree.com"; 
$username = "if0_41549894";             
$dbname = "if0_41549894_cbism_db";      

// Ang MySQL Password nimo: I-click ang 'eye' icon sa image_80fe64.png para makita
$password = "lusicafamily"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>