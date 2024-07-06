<?php


use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

$feedback = $db->query("SELECT * FROM tblfeedback JOIN tblemployees ON employeeID = customerid ORDER BY RAND()")->get();

$products = $db->query("SELECT * FROM tblproducts WHERE status = 'Available' ORDER BY RAND() LIMIT 6")->get();

$coffee = $db->query("SELECT * FROM tblcoffeeshop")->find();


view('index.view.php', [
  'feedback' => $feedback,
  'products' => $products,
  'coffee' => $coffee,
]);
