<?php

use Core\App;
use Core\Database;

// Define $db before using it
$db = App::resolve('Core\Database');

// Assuming you have a function to get the category from the query parameters
$category = $_GET['category'];

// Adjust your SQL query to filter by category
$products = $db->query("SELECT * FROM tblproducts WHERE category = :category", [':category' => $category])->get();

// Return the products as JSON
echo json_encode($products);
