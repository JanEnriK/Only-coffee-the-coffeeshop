<?php

use Core\App;
use Core\Database;
use Core\Validator;

$db = App::resolve('Core\Database');


// Parse JSON from the request body
$data = json_decode(file_get_contents('php://input'), true);

$baseCoffeeId = $data['base_coffee_id'] ?? null;

if ($baseCoffeeId && isset($_SESSION['cart'])) {
    // Find the index of the item to remove
    $indexToRemove = null;
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['base_coffee_id'] == $baseCoffeeId) {
            $indexToRemove = $index;
            break;
        }
    }

    if ($indexToRemove !== null) {
        // Remove the item from the cart
        unset($_SESSION['cart'][$indexToRemove]);
        // Optionally, re-index the array if necessary
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
