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

if (isset($_GET['get_inventory_data'])) {
    try {
        $filter = isset($_GET['filterValue']) ? $_GET['filterValue'] : '';

        $query = "SELECT * FROM tblinventory WHERE 1";

        switch ($filter) {
            case 'low':
                $query .= " AND quantity < 10 ORDER BY quantity ASC";
                break;
            case 'high':
                $query .= " AND quantity >= 10 ORDER BY quantity DESC";
                break;
            default:
                break;
        }

        $stmt = $pdo->prepare($query);

        $stmt->execute();
        $inventoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($inventoryData);
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
