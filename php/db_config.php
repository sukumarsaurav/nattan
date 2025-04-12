<?php
// Database configuration
$servername = "193.203.184.121";
$username = "u911550082_nattan"; // Change to your MySQL username
$password = "Milk@sdk14"; // Change to your MySQL password
$dbname = "u911550082_nattan";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?> 