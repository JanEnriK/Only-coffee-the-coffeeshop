<?php
// Database connection settings
$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'coffeeshop_db';

// Create a database connection
$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
// Fetch product data from the database
$sql = "SELECT * FROM tblproducts";
$result = $mysqli->query($sql);

$products = array();

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$mysqli->close();

// Return the product data as JSON
header('Content-Type: application/json');
echo json_encode($products);
