<?php
include 'connect.php';

$orderNumber = $_GET['orderNumber'];
$orderDate = $_GET['orderDate'];
// Convert the string to a timestamp
$orderDate = strtotime($orderDate);
// Now pass the timestamp to date()
$orderDate = date('Y-m-d', $orderDate);

$sql = "SELECT p.product_name, o.quantity 
        FROM tblorders o
        JOIN tblproducts p ON o.base_coffee_id = product_id   
        WHERE o.order_number =? AND DATE(o.order_datetime) =?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $orderNumber, $orderDate);

$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = ['name' => $row['product_name'], 'quantity' => $row['quantity']];
}
header('Content-Type: application/json');
echo json_encode($items);
