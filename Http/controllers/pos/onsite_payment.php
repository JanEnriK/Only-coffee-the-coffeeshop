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
    $db->query("INSERT INTO tblorders(order_type, quantity, base_coffee_id, customer_id, order_number, order_status) VALUES(:order_type, :quantity, :base_coffee_id, :customer_id, :order_number, :order_status)", [
        'order_type' => 'take-out',
        'quantity' => $quantity,
        'base_coffee_id' => $productId,
        'customer_id' => $employeeID,
        'order_number' => $order_number,
        'order_status' => "payed"
    ]);
}

if ($paymentMethod === "cash") {
    // Insert the payment details into the database (CASH)
    $db->query("INSERT INTO tblpayment(amountpayed, paymenttype, customerid, orderNumber) VALUES(:total_amount, :payment_type, :customer_id, :order_number)", [
        'total_amount' => $totalAmount,
        'payment_type' => "cash",
        'customer_id' => $employeeID,
        'order_number' => $order_number,
    ]);
    //pass the order to order items for preparation
    $orderedItems = $db->query("SELECT * FROM tblorders JOIN tblproducts ON base_coffee_id = product_id WHERE order_status = 'payed' AND order_number = ?;", [$order_number])->get();
    foreach ($orderedItems as $items) {
        $db->query("INSERT INTO tblorderitem(quantity, status, orderid, productid) VALUES(:quantity, :status, :orderid, :productid)", [
            'quantity' => $items['quantity'],
            'status' => "active",
            'orderid' => $order_number,
            'productid' => $items['product_id'],
        ]);
    }
} else {
    // Insert the payment details into the database (ONLINE)
    $db->query("INSERT INTO tblpayment(amountpayed, paymenttype, customerid, orderNumber,reference_no) VALUES(:total_amount, :payment_type, :customer_id, :order_number, :reference_no)", [
        'total_amount' => $totalAmount,
        'payment_type' => "online",
        'customer_id' => $employeeID,
        'order_number' => $order_number,
        'reference_no' => $paymentOnline,
    ]);

    //pass the order to order items for preparation
    $orderedItems = $db->query("SELECT * FROM tblorders JOIN tblproducts ON base_coffee_id = product_id WHERE order_status = 'payed' AND order_number = ?;", [$order_number])->get();
    foreach ($orderedItems as $items) {
        $db->query("INSERT INTO tblorderitem(quantity, status, orderid, productid) VALUES(:quantity, :status, :orderid, :productid)", [
            'quantity' => $items['quantity'],
            'status' => "active",
            'orderid' => $order_number,
            'productid' => $items['product_id'],
        ]);
    }
}


$_SESSION['orderSubmited']['ordernumber'] = $order_number;
header('Location: /pos_frontend');
exit;
