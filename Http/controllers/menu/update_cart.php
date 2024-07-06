<?php

// Ensure the Content-Type header is set to application/json
header('Content-Type: application/json');

use Core\App;
use Core\Database;
use Core\Validator;

$db = App::resolve('Core\Database');

// Parse JSON from the request body
$data = json_decode(file_get_contents('php://input'), true);

$baseCoffeeId = $data['base_coffee_id'] ?? null; // Use null coalescing operator to avoid undefined index notice
$newQuantity = $data['quantity'] ?? null; // Use null coalescing operator to avoid undefined index notice

// Debugging: Log the received POST data
error_log("Received POST data: base_coffee_id: " . $baseCoffeeId . ", quantity: " . $newQuantity);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemFound = false; // Flag to track if the item was found

    // Iterate over each item in the cart
    foreach ($_SESSION['cart'] as $index => $item) {
        // Check if the current item's base_coffee_id matches the one we're looking for
        if ($item['base_coffee_id'] == $baseCoffeeId) {
            // Update the quantity of the found item
            $_SESSION['cart'][$index]['quantity'] = $newQuantity;
            $itemFound = true; // Set the flag to true since we found the item
            break; // Exit the loop as we've found and updated the item
        }
    }

    if ($itemFound) {
        // Debugging: Log the successful update
        error_log("Cart updated successfully for base_coffee_id: " . $baseCoffeeId);
        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
    } else {
        // Debugging: Log the item not found in cart
        error_log("Item not found in cart for base_coffee_id: " . $baseCoffeeId);
        echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
    }
}
