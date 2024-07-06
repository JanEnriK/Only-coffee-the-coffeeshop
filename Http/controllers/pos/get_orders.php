<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

// Check if orderNumber and customerId are set and not empty
if (isset($_POST['orderNumber']) && isset($_POST['customerId']) && isset($_POST['status']) && !empty($_POST['orderNumber']) && !empty($_POST['customerId']) && !empty($_POST['status'])) {
    $orderNumber = $_POST['orderNumber'];
    $customerId = $_POST['customerId'];
    $status = $_POST['status'];

    try {
        // Prepare and execute the SQL query to get order details
        $sql = "SELECT * FROM tblorders JOIN tblemployees ON customer_id = employeeID JOIN tblproducts ON base_coffee_id = product_id WHERE order_number = ? AND customer_id = ? AND order_status = ?";
        $params = [$orderNumber, $customerId, $status];
        $db->query($sql, $params);

        // Assuming the query execution is successful, fetch the results
        $orderDetails = $db->find(); // Assuming find() returns the first result

        // If you need to fetch all matching orders, use get() instead
        // $orderDetails = $db->get();

        // Check if order details were found
        if ($orderDetails) {
            // Prepare and execute the SQL query to get products for this order
            // Adjust this query based on your actual database schema
            $sqlProducts = "SELECT * FROM tblorders JOIN tblemployees ON customer_id = employeeID JOIN tblproducts ON base_coffee_id = product_id WHERE order_number = ? and order_status = ?";
            $paramsProducts = [$orderNumber, $status];
            $db->query($sqlProducts, $paramsProducts);
            $products = $db->get(); // Assuming get() returns all matching results

            // Combine order details with products
            $orderDetails['products'] = $products;

            // Return the order details as JSON
            echo json_encode($orderDetails);
        } else {
            echo json_encode(array("error" => "No order found"));
        }
    } catch (PDOException $e) {
        // Log the error or handle it as needed
        error_log($e->getMessage());
        echo json_encode(array("error" => "Database error: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("error" => "Invalid request"));
}
