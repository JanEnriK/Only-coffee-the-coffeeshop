<?php 

use Core\App;
use Core\Database;
use Core\Response;

$db = App::resolve('Core\Database');


$product = $db->query("SELECT * FROM tblproducts WHERE product_id = :id", ['id' => $_GET['id']])->findOrFail();

view('product_show.view.php', [
    'product' => $product,
  ]);