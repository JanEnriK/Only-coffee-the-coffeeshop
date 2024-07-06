<?php
include "connect.php";

session_start();
header('Content-Type: application/json');

// Assuming you have started the session and have a $pdo connection
$proceed = true;
foreach ($_SESSION['cart'] as $item) {
    $id = $item['base_coffee_id'];
    $quantity = $item['quantity'];
    $sql = $pdo->prepare("SELECT SKU FROM tblproducts WHERE product_id = ?");
    $sql->bindParam(1, $id, PDO::PARAM_INT);
    $sql->execute();
    $prod = $sql->fetch(PDO::FETCH_ASSOC);

    if ($prod['SKU'] < $quantity) {
        $proceed = false;
        unset($_SESSION['cart']);
        break;
    }
}
echo json_encode($proceed);
