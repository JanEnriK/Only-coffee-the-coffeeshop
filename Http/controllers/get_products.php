<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

$sql = "SELECT product_id, product_name, product_description, price,status, CONCAT(UCASE(SUBSTRING(category, 1, 1)), LOWER(SUBSTRING(category, 2))) AS category, image FROM tblproducts";

$result = $db->query($sql)->get();

$products = [];

foreach ($result as $product) {
    $products[] = $product;
}

// Return the product data as JSON
header('Content-Type: application/json');
echo json_encode($products);
exit();
