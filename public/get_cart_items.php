<?php
include "connect.php";

session_start();
header('Content-Type: application/json');

// Assuming you have started the session and have a $pdo connection
$cartItems = [];
foreach ($_SESSION['cart'] as $item) {
    $id = $item['base_coffee_id'];
    $sql = $pdo->prepare("SELECT * FROM tblproducts WHERE product_id =?");
    $sql->bindParam(1, $id, PDO::PARAM_INT);
    $sql->execute();
    $prod = $sql->fetch(PDO::FETCH_ASSOC);
    if (!empty($prod)) {
        $cartItems[$item['base_coffee_id']] = [
            'base_coffee_id' => $item['base_coffee_id'],
            'base_coffee' => $item['base_coffee'],
            'price' => (float)$prod['price'],
            'quantity' => $item['quantity']
        ];
    }
}
echo json_encode($cartItems);
