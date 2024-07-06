<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

$products = $db->query("SELECT product_id, 
                                product_name,
                                product_description, 
                                price, 
                                CONCAT(UCASE(SUBSTRING(category, 1, 1)), LOWER(SUBSTRING(category, 2))) AS category, 
                                image 
                                FROM tblproducts
                                ")->get();


$productCategories = $db->query("SELECT DISTINCT category FROM tblproducts")->get();

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

// $_SESSION['user'] = [
//     'id' => $user['id'],
//     'email' => $user['email'],
//     'role' => $user['role'],
//   ];

// dd(json_encode($_SESSION['cart']));





view('menu.view.php', [
    'products' => $products,
    'productCategories' => $productCategories,
    'vatPercentage' => $vatPercentage,
]);
