<?php
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "expense";
$conn = mysqli_connect($servername, $username, $password, $db_name);
if (!$conn) { die("Database connection failed: " . mysqli_connect_error()); }
mysqli_set_charset($conn, 'utf8mb4');
