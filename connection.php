

<?php

$servername = "localhost";
$username = "root";
$password = ""; 
$database = "election";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

$conn->set_charset("utf8mb4");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>