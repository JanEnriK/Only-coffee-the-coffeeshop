<?php
session_start();
require_once 'connect.php'; // Adjust the path to your database connection script

$code = isset($_GET['code']) ? $_GET['code'] : '';
$response = ['valid' => false, 'value' => null];

if ($code) {
    $stmt = $pdo->prepare("SELECT value FROM tblpromo WHERE promocode =?");
    $stmt->execute([$code]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $response['valid'] = true;
        $response['value'] = $result['value']; // Assuming 'value' is the column name holding the discount value
        $_SESSION['discount'] = $result['value']; // Store the discount value in the session
    } else {
        $response['message'] = 'There is no discount code like this.';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}
