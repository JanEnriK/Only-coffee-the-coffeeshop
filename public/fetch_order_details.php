<?php
include 'connect.php';

if (!isset($_GET['orderNumber'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Order number is required']);
    exit;
}
if (!isset($_GET['orderDate'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Order date is required']);
    exit;
}

$orderNumber = $_GET['orderNumber'];
$orderNumber = mysqli_real_escape_string($conn, $orderNumber);
$orderDate = $_GET['orderDate'];
$orderDate = date('Y-m-d', strtotime($orderDate));

// Prepare and execute the first query
// Prepare and execute the first query
$query = "SELECT tblproducts.product_name, tblorders.quantity, tblproducts.price, tblorders.discount, tblorders.order_status, tblorders.order_datetime 
         FROM tblorders 
         JOIN tblproducts ON tblorders.base_coffee_id = tblproducts.product_id 
         WHERE tblorders.order_number =? AND DATE(tblorders.order_datetime) = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $orderNumber, $orderDate); // Corrected line
mysqli_stmt_execute($stmt);

// Bind the results to variables
mysqli_stmt_bind_result($stmt, $productName, $quantity, $price, $discount, $order_status, $order_datetime);

// Initialize an array to hold the order details
$orderDetails = [];

// Fetch all results from the first query before moving on
while (mysqli_stmt_fetch($stmt)) {
    $orderDetails[] = [
        'product_name' => $productName,
        'quantity' => $quantity,
        'price' => $price,
        'discount' => $discount,
        'order_status' => $order_status,
        'order_datetime' => $order_datetime,
    ];
}

// Close the first statement after fetching all results
mysqli_stmt_close($stmt);

// Now, it's safe to prepare and execute the second query for VAT
$VATquery = "SELECT VAT from tblcoffeeshop WHERE 1";
$VATstmt = mysqli_prepare($conn, $VATquery);
mysqli_stmt_execute($VATstmt);
mysqli_stmt_bind_result($VATstmt, $VAT);
mysqli_stmt_fetch($VATstmt); // Fetch the VAT value
$VAT = $VAT; // Store the fetched VAT value for use in the order details

// Add VAT to each order detail
foreach ($orderDetails as &$orderDetail) {
    $orderDetail['VAT'] = $VAT; // Add VAT to each order detail
}
unset($orderDetail); // Break the reference with unset

// Check if any details were found
if (empty($orderDetails)) {
    http_response_code(404);
    echo json_encode(['error' => 'Order details not found']);
    exit;
}

// Set the content type and output the JSON-encoded details
header('Content-Type: application/json');
echo json_encode($orderDetails);

// Close the VAT statement
mysqli_stmt_close($VATstmt);
