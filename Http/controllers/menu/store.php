<?php

use Core\App;
use Core\Database;
use Core\Validator;

$db = App::resolve('Core\Database');

// dd($_POST);

$errors = [];

// if (! Validator::checkbox($category)) {
//   $errors['category'] = "A body of no more than 50 characters is required.";
// }

// if (! empty($errors)) {
//   return view('menu.view.php', [
//     'errors' => $errors,
//   ]);
// }

// dd($_SESSION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if the necessary POST data is set
  if (isset($_POST['order_type'], $_POST['base_coffee_id'], $_POST['base_coffee'])) {
    $orderType = $_POST['order_type'];
    $baseCoffeeId = $_POST['base_coffee_id'];
    $baseCoffeeName = $_POST['base_coffee'];

    // Initialize the cart if it's not already set
    if (!isset($_SESSION['cart'])) {
      $_SESSION['cart'] = array();
    }

    $found = false;
    $showAlert = false; // Explicitly declare showAlert here
    // Loop through the cart to find the product
    foreach ($_SESSION['cart'] as &$item) {
      // Query the database for the product details including SKU
      $prod = $db->query("SELECT product_id, 
                          product_name,
                          product_description, 
                          price,
                          SKU,
                          CONCAT(UCASE(SUBSTRING(category, 1, 1)), LOWER(SUBSTRING(category, 2))) AS category, 
                          image 
                          FROM tblproducts WHERE product_id = $item[base_coffee_id]")->get();

      if ($item['base_coffee_id'] == $baseCoffeeId) {
        // Check if the current quantity plus one exceeds the SKU
        if (($item['quantity'] + 1) > $prod[0]['SKU']) {
          // Show an alert that the product limit has been reached
          $found = true;
          $showAlert = true; // Ensure showAlert is set to true here
          break;
        }
        // If the product exists and the quantity does not exceed the SKU, increase the quantity by one
        $item['quantity'] += 1;
        $found = true;
        break;
      }
    }

    // If the product is not found, add it to the cart with quantity 1
    if (!$found) {
      $_POST['quantity'] = 1; // Add quantity to the POST data
      $_SESSION['cart'][] = $_POST;
    }
    if ($showAlert) {
      $_SESSION['alert_message'] = 'the order limit has been reached for that product.';
    }
  }
}

header('location: /menu');
exit; // Use exit instead of die() for better practice