<?php

$servername = "localhost";
$username = "u462586686_tums_admin";
$password = "Tums@1234567890";
$database = "u462586686_TUMSDB"; //taskaUNIMAS

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
