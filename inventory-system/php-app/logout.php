<?php
session_start();
session_unset();  // I-clear ang tanang session variables
session_destroy(); // I-destroy ang session
header("Location: index.php"); // Balik sa login page
exit();
?>