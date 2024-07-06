<?php

use Core\App;
use Core\Database;
use Core\Validator;

$db = App::resolve('Core\Database');

// Function to generate a unique order number
function generateUniqueOrderNumber($db)
{
  // Retrieve the highest order number currently in use
  $lastOrder = $db->query("SELECT order_number as last_order, order_datetime FROM tblorders ORDER BY order_id DESC LIMIT 1")->find();

  // Check if there were any orders before
  if ($lastOrder['last_order'] !== null) {
    // Convert the datetime of the last order to a date
    $lastOrderDate = date('Y-m-d', strtotime($lastOrder['order_datetime']));
    // Get today's date
    date_default_timezone_set('Asia/Manila');
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

//insert to orderstable
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if the necessary POST data is set
  $paymentMethod = $_POST['paymentMethod'];

  if ($paymentMethod == 'online') {
    $errors = [];

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["proofOfPayment"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = @getimagesize($_FILES["proofOfPayment"]["tmp_name"]);

    if ($check !== false) {
      $uploadOk = 1;
    } else {
      $errors['body'] = "File is not an image.";
      $uploadOk = 0;
      echo $errors['body'];
    }

    if ($_FILES["proofOfPayment"]["size"] > 50000000) {
      $errors['body'] = "Sorry, your file is too large.";
      $uploadOk = 0;
      echo $errors['body'];
    }

    // Allow certain file formats
    if (
      $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
      && $imageFileType != "gif"
    ) {
      $errors['body'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
      $uploadOk = 0;
      echo $errors['body'];
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1) {
      if (move_uploaded_file($_FILES["proofOfPayment"]["tmp_name"], $target_file)) {
        $upload_file = basename($_FILES["proofOfPayment"]["name"]);
        //if paymentmenthod is online
        $order_number = generateUniqueOrderNumber($db);

        $discountPercent = 0;
        if (isset($_SESSION['discount'])) {
          $discountPercent = $_SESSION['discount'];
        }

        foreach ($_SESSION['cart'] as $item) {
          $_POST = $item;

          $errors = [];

          if (empty($errors)) {
            // Generate a unique order number for each item in the cart
            $db->query("INSERT INTO tblorders(order_type, quantity, base_coffee_id, customer_id, order_number, order_status, payment_proof, discount) VALUES(:order_type, :quantity,:base_coffee_id, :customer_id, :order_number, :order_status, :payment_proof, :discount)", ['order_type' => $_POST['order_type'], 'quantity' => $_POST['quantity'], 'base_coffee_id' => $_POST['base_coffee_id'], 'customer_id' => $_SESSION['user']['id'], 'order_number' => $order_number, 'order_status' => "pending", 'payment_proof' => $upload_file, 'discount' => $discountPercent]);
          }
        }
      }
    } else {
      $_SESSION['payment_error'] = $errors;
      view('/menu', [
        'errors' => $errors['body']
      ]);
      redirect("/menu");
      exit();
    }
  } else {
    //if paymentmenthod is cash
    $order_number = generateUniqueOrderNumber($db);

    $discountPercent = 0;
    if (isset($_SESSION['discount'])) {
      $discountPercent = $_SESSION['discount'];
    }

    foreach ($_SESSION['cart'] as $item) {
      $_POST = $item;

      $errors = [];

      if (empty($errors)) {
        // Generate a unique order number for each item in the cart
        $db->query("INSERT INTO tblorders(order_type, quantity, base_coffee_id, customer_id, order_number, order_status, discount) VALUES(:order_type, :quantity,:base_coffee_id, :customer_id, :order_number, :order_status, :discount)", ['order_type' => $_POST['order_type'], 'quantity' => $_POST['quantity'], 'base_coffee_id' => $_POST['base_coffee_id'], 'customer_id' => $_SESSION['user']['id'], 'order_number' => $order_number, 'order_status' => "notpayed", 'discount' => $discountPercent]);
      }
    }
  }
}



$_SESSION['cart'] = [];
$_SESSION['discount'] = [];
// Assuming you want to store the last order number submitted
$_SESSION['orderSubmited']['ordernumber'] = $order_number;

header('location: /menu');
die();
