<?php
include 'connect.php';

$orderNumber = $_GET['orderNumber'];
$orderNumber = mysqli_real_escape_string($conn, $orderNumber);
$orderDate = $_GET['orderDate'];
$orderDate = date('Y-m-d', strtotime($orderDate));

// Prepare and execute the first query
$query = "SELECT *
         FROM tblorderitem  
         WHERE orderid =? AND DATE(date_time) = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $orderNumber, $orderDate); // Corrected line
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$statusFetch = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode($statusFetch);
