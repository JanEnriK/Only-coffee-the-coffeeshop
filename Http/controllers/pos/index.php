<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

// Existing queries
$discount_codes = $db->query("SELECT * from tblpromo WHERE 1")->get();
$newOrder = $db->query("SELECT MAX(order_number) as last_order FROM tblorders")->find();
$newOrder = $newOrder['last_order'] + 1;

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
