<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

// Existing queries
$discount_codes = $db->query("SELECT * from tblpromo WHERE 1")->get();

$lastOrder = $db->query("SELECT order_number as last_order, order_datetime FROM tblorders ORDER BY order_id DESC LIMIT 1")->find();
if ($lastOrder['last_order'] !== null) {
    // Convert the datetime of the last order to a date
    $lastOrderDate = date('Y-m-d', strtotime($lastOrder['order_datetime']));
    // Get today's date
    date_default_timezone_set('Asia/Manila');
    $today = date('Y-m-d');

    // If today's date is different from the last order date, reset the order number to 101
    if ($lastOrderDate != $today) {
        $newOrder = 101;
    } else {
        // Increment the last order number by 1
        $newOrder = $lastOrder['last_order'] + 1;
    }
} else {
    // If there are no orders yet, start from 101
    $newOrder = 101;
}

// New query to fetch VAT percentage
$vatResult = $db->query("SELECT VAT as VAT from tblcoffeeshop WHERE 1")->find();

// Ensure to check if the result exists and convert the VAT percentage to float
if ($vatResult === null || empty($vatResult)) {
    // Handle the case where no VAT percentage is found
    // This could involve setting a default value or handling an error
    $vatPercentage = 0; // Defaulting to 0 as an example
} else {
    $vatPercentage = (float)$vatResult['VAT']; // Convert to float
}

// Pass all necessary variables to the view
view('pos/index.view.php', [
    'discount_codes' => $discount_codes,
    'newOrder' => $newOrder,
    'vatPercentage' => $vatPercentage, // Pass the VAT percentage to the view
]);
