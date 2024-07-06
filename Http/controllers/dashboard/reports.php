<?php
include "connect.php";
// Database connection
// $servername = "localhost";
// $user = "root";
// $pass = "";
// $dbname = "coffeeshop_db";


// try {
//   $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pass);
//   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//   die("Database connection failed: " . $e->getMessage());
// }

$query = "";

// Sales report 
if (isset($_GET['get_sales_data'])) {
  try {
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

    $salesQuery = "SELECT * FROM tblpayment WHERE 1";

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
      $salesQuery .= " AND DATE(order_datetime) BETWEEN :start_date AND :end_date";
    }

    $salesQuery .= " ORDER BY order_datetime DESC";
    $stmt = $pdo->prepare($salesQuery);

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

// Inventory report 
if (isset($_GET['get_inventory_data'])) {
  try {
    $filter = isset($_GET['filterValue']) ? $_GET['filterValue'] : '';

    $baseQuery = "SELECT * FROM tblinventoryreport WHERE 1";
    $params = [];

    if ($filter) {
      $inventoryQuery = $baseQuery . " AND inventory_item LIKE :filter ORDER BY datetime DESC";
      $params[':filter'] = '%' . $filter . '%';
    } else {
      $inventoryQuery = $baseQuery . " ORDER BY datetime DESC";
    }
    $stmt = $pdo->prepare($inventoryQuery);

    // Bind parameters if needed
    if (!empty($params)) {
      foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
      }
    }

    $stmt->execute();
    $inventoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($inventoryData);
    exit();
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

// Feedback report 
if (isset($_GET['get_feedback_data'])) {
  try {
    $startDate = isset($_GET['feedbackStartDate']) ? $_GET['feedbackStartDate'] : null;
    $endDate = isset($_GET['feedbackEndDate']) ? $_GET['feedbackEndDate'] : null;

    $query = "SELECT * FROM tblfeedback WHERE 1";

    if ($startDate !== null && $endDate !== null) {
      $query .= " AND DATE(feedback_datetime) BETWEEN :start_date AND :end_date";
    }

    $query .= " ORDER BY feedback_datetime DESC ";

    $stmt = $pdo->prepare($query);

    if ($startDate !== null && $endDate !== null) {
      $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
      $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
    }

    $stmt->execute();


    $feedbackData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($feedbackData);
    exit();
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

// Fetch userlogs data
if (isset($_GET['get_userlogs_data'])) {
  try {
    $startDate = isset($_GET['userlogStartDate']) ? $_GET['userlogStartDate'] : null;
    $endDate = isset($_GET['userlogEndDate']) ? $_GET['userlogEndDate'] : null;

    $query = "SELECT * FROM tbluserlogs WHERE 1";

    if ($startDate !== null && $endDate !== null) {
      $query .= " AND DATE(log_datetime) BETWEEN :start_date AND :end_date";
    }

    $query .= " ORDER BY log_datetime DESC";
    $stmt = $pdo->prepare($query);

    if ($startDate !== null && $endDate !== null) {
      $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
      $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
    }

    $stmt->execute();
    $feedbackData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($feedbackData);
    exit();
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}
date_default_timezone_set('Asia/Manila');
$sqlAdmin = "SELECT * FROM tblemployees WHERE employeeID = :id";
$statementAdmin = $pdo->prepare($sqlAdmin);
$statementAdmin->bindParam(':id', $_SESSION['user']['id']);
$statementAdmin->execute();
$Admin = $statementAdmin->fetch(PDO::FETCH_ASSOC);

view('dashboard/reports.view.php', [
  'Admin' => $Admin,
]);
