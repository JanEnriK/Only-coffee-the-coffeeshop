<?php

// Database connection
$servername = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "coffeeshop_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Sales report 
if (isset($_GET['get_sales_data'])) {
    try {
        $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
        $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

        $query = "SELECT * FROM tblpayment WHERE 1";

        // switch ($filter) {
        //     case 'cash':
        //         $query .= " AND paymenttype = 'Cash'";
        //         break;
        //     case 'card':
        //         $query .= " AND paymenttype = 'Card'";
        //         break;

        //     default:
        //         break;
        // }

        if ($startDate !== null && $endDate !== null) {
            $query .= " AND DATE(order_datetime) BETWEEN :start_date AND :end_date";
        }

        $stmt = $pdo->prepare($query);

        if ($startDate !== null && $endDate !== null) {
            $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
            $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
        }

        $stmt->execute();
        $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($salesData);
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
