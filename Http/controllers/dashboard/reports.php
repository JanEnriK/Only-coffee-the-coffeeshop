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

    $filter = isset($_GET['filterValue']) ? trim($_GET['filterValue']) : '';

    $salesQuery = "SELECT *, emp.username as emp_username, cust.username as cust_username 
                    FROM tblpayment
                    JOIN tblemployees AS emp ON tblpayment.employee_id = emp.employeeID 
                    JOIN tblemployees AS cust ON tblpayment.customerid = cust.employeeID
                    WHERE 1";
    $params = [];

    if ($startDate !== null && $endDate !== null) {
      $salesQuery .= " AND DATE(order_datetime) BETWEEN :start_date AND :end_date";
    }

    if ($filter !== '') {
      // Add filter to the query with parameter binding
      $salesQuery = $salesQuery . " AND paymenttype LIKE :filter";
      $params[':filter'] = "%" . $filter . "%";
    }

    $salesQuery .= " ORDER BY order_datetime DESC";
    $stmt = $pdo->prepare($salesQuery);

    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }

    if ($startDate !== null && $endDate !== null) {
      $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
      $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
    }

    $stmt->execute();
    $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Sales Data: " . json_encode($salesData));
    echo json_encode($salesData);

    exit();
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

// Inventory report 
if (isset($_GET['get_inventory_data'])) {
  try {
    // Get the filter value from the query parameter and trim any extra whitespace
    $filter = isset($_GET['filterValue']) ? trim($_GET['filterValue']) : '';
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;
    $recordType = isset($_GET['recordType']) ? trim($_GET['recordType']) : '';

    // Base query
    $inventoryQuery = "SELECT * FROM tblinventoryreport
                         JOIN tblemployees ON employee_id = employeeID
                         WHERE 1";
    $params = [];

    // Check if there is a date range
    if ($startDate !== null && $endDate !== null) {
      $inventoryQuery .= " AND DATE(datetime) BETWEEN :start_date AND :end_date";
      $params[':start_date'] = $startDate;
      $params[':end_date'] = $endDate;
    }

    // Check if there is a filter value
    if ($filter !== '') {
      $inventoryQuery .= " AND inventory_item LIKE :filter";
      $params[':filter'] = "%" . $filter . "%";
    }

    // Check if there is a record type
    if ($recordType !== '') {
      $inventoryQuery .= " AND record_type LIKE :recordType";
      $params[':recordType'] = "%" . $recordType . "%";
    }

    $inventoryQuery .= " ORDER BY datetime DESC";

    // Debug: Log the full query
    error_log("SQL Query: " . $inventoryQuery);
    error_log("SQL Params: " . json_encode($params));

    // Prepare the query
    $stmt = $pdo->prepare($inventoryQuery);

    // Bind parameters if any
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }

    // Execute the query
    $stmt->execute();
    $inventoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Log the result count
    error_log("Result count: " . count($inventoryData));

    // Return the data as JSON
    echo json_encode($inventoryData);
    exit();
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    error_log("error: " . $e->getMessage());
  }
}




// Feedback report 
if (isset($_GET['get_feedback_data'])) {
  try {
    $startDate = isset($_GET['feedbackStartDate']) ? $_GET['feedbackStartDate'] : null;
    $endDate = isset($_GET['feedbackEndDate']) ? $_GET['feedbackEndDate'] : null;

    $query = "SELECT * FROM tblfeedback
              JOIN tblemployees ON customerid = employeeID
              WHERE 1";

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

$sqlItems = "SELECT DISTINCT inventory_item FROM tblinventory";
$statementItems = $pdo->prepare($sqlItems);
$statementItems->execute();
$items = $statementItems->fetchAll(PDO::FETCH_ASSOC);

view('dashboard/reports.view.php', [
  'Admin' => $Admin,
  'items' => $items,
]);
