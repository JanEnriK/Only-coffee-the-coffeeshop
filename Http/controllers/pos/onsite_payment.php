<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

// Retrieve the form data
$totalAmount = $_POST['total'];
$employeeID = $_SESSION['user']['id'];
$paymentCash = $_POST['cashAmount'];
$paymentOnline = $_POST['referenceNumber'];
$productIds = $_POST['productIds'];
$quantities = $_POST['quantities'];
date_default_timezone_set('Asia/Manila');
$today = new DateTime('now', new DateTimeZone('Asia/Manila'));
$today = $today->format('Y-m-d H:i:s');

$todayOrderitem = new DateTime('now', new DateTimeZone('Asia/Manila'));
$todayOrderitem = $todayOrderitem->format('Y-m-d H:i:s');



// Determine the payment method
$paymentMethod = !empty($paymentCash) ? 'cash' : (!empty($paymentOnline) ? 'online' : 'unknown');

// Get the current order number
function generateUniqueOrderNumber($db)
{
    // Retrieve the highest order number currently in use
    $lastOrder = $db->query("SELECT order_number as last_order, order_datetime FROM tblorders ORDER BY order_id DESC LIMIT 1")->find();

    // Check if there were any orders before
    if ($lastOrder['last_order'] !== null) {
        // Convert the datetime of the last order to a date
        $lastOrderDate = date('Y-m-d', strtotime($lastOrder['order_datetime']));
        date_default_timezone_set('Asia/Manila');
        // Get today's date
        $today = date('Y-m-d');

        // If today's date is different from the last order date, reset the order number to 101
        if ($lastOrderDate != $today) {
            $order_number = 101;
        } else {
            // Increment the last order number by 1
            $order_number = $lastOrder['last_order'] + 1;
        }
    } else {
        // If there are no orders yet, start from 101
        $order_number = 101;
    }

    return $order_number;
}

// Insert to tblorders
$order_number = generateUniqueOrderNumber($db);
for ($i = 0; $i < count($productIds); $i++) {
    $productId = $productIds[$i];
    $quantity = $quantities[$i];

    // Insert each order item
    $db->query("INSERT INTO tblorders(order_type, quantity, base_coffee_id, customer_id, order_number, order_status,order_datetime) VALUES(:order_type, :quantity, :base_coffee_id, :customer_id, :order_number, :order_status,:order_datetime)", [
        'order_type' => 'take-out',
        'quantity' => $quantity,
        'base_coffee_id' => $productId,
        'customer_id' => $employeeID,
        'order_number' => $order_number,
        'order_status' => "payed",
        'order_datetime' => $today,
    ]);
}

if ($paymentMethod === "cash") {
    // Insert the payment details into the database (CASH)
    $db->query("INSERT INTO tblpayment(amountpayed, paymenttype, customerid, orderNumber,order_datetime) VALUES(:total_amount, :payment_type, :customer_id, :order_number,:order_datetime)", [
        'total_amount' => $totalAmount,
        'payment_type' => "cash",
        'customer_id' => $employeeID,
        'order_number' => $order_number,
        'order_datetime' => $today,
    ]);

    date_default_timezone_set('Asia/Manila');
    // Get today's date in the format YYYY-MM-DD
    $today = date('Y-m-d');

    //pass the order to order items for preparation
    $orderedItems = $db->query("SELECT * FROM tblorders JOIN tblproducts ON base_coffee_id = product_id WHERE order_status = 'payed' AND order_number = ? and DATE(order_datetime) = ?;", [$order_number, $today])->get();
    foreach ($orderedItems as $items) {
        $db->query("INSERT INTO tblorderitem(quantity, status, orderid, productid, customerid,date_time) VALUES(:quantity, :status, :orderid, :productid,:customerid,:date_time)", [
            'quantity' => $items['quantity'],
            'status' => "active",
            'orderid' => $order_number,
            'productid' => $items['product_id'],
            'customerid' => $employeeID,
            'date_time' => $todayOrderitem,
        ]);
    }
} else {
    // Insert the payment details into the database (ONLINE)
    $db->query("INSERT INTO tblpayment(amountpayed, paymenttype, customerid, orderNumber,reference_no, order_datetime) VALUES(:total_amount, :payment_type, :customer_id, :order_number, :reference_no, :order_datetime)", [
        'total_amount' => $totalAmount,
        'payment_type' => "online",
        'customer_id' => $employeeID,
        'order_number' => $order_number,
        'reference_no' => $paymentOnline,
        'order_datetime' => $today,
    ]);

    date_default_timezone_set('Asia/Manila');
    // Get today's date in the format YYYY-MM-DD
    $today = date('Y-m-d');

    //pass the order to order items for preparation
    $orderedItems = $db->query("SELECT * FROM tblorders JOIN tblproducts ON base_coffee_id = product_id WHERE order_status = 'payed' AND order_number = ? AND DATE(order_datetime) = ?;", [$order_number, $today])->get();

    foreach ($orderedItems as $items) {
        $db->query("INSERT INTO tblorderitem(quantity, status, orderid, productid,customerid,date_time) VALUES(:quantity, :status, :orderid, :productid, :customerid,:date_time)", [
            'quantity' => $items['quantity'],
            'status' => "active",
            'orderid' => $order_number,
            'productid' => $items['product_id'],
            'customerid' => $employeeID,
            'date_time' => $todayOrderitem,
        ]);
    }
}


$_SESSION['orderSubmited']['ordernumber'] = $order_number;
header('Location: /pos_frontend');
exit;
