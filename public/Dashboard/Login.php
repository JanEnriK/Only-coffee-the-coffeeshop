<?php

// Database connection parameters
$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'coffeeshop';

// Create a database connection
try {
  $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
  // Set the PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}
session_start();


if ($_SERVER['REQUEST_METHOD'] == "POST") {
  // Retrieve values from the form
  $enteredUsername = $_POST['username'];
  $enteredPassword = $_POST['password'];


  // Perform SQL query to validate user
  $query = "SELECT * FROM tblemployees WHERE username = '$enteredUsername' AND password = '$enteredPassword' AND position = 'admin'";
  $resultprep = $pdo->prepare($query);
  $resultprep->execute();

  $querybarista = "SELECT * FROM tblemployees WHERE username = '$enteredUsername' AND password = '$enteredPassword' AND position = 'barista'";
  $resultbaristaprep = $pdo->query($querybarista);
  $resultbaristaprep->execute();

  $querycashier = "SELECT * FROM tblemployees WHERE username = '$enteredUsername' AND password = '$enteredPassword' AND position = 'cashier'";
  $resultcashierprep = $pdo->query($querycashier);
  $resultcashierprep->execute();

  if ($resultprep->rowCount() > 0) {
    // Authentication successful
    $result1 = $resultprep->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['employeeID'] = $result1[0]['employeeID'];
    $_SESSION['position'] = $result1[0]['position']; // Store user information in a session variable
    $_SESSION['username'] = $result1[0]['username'];

    //add user log login
    $DateTime = new DateTime();
    $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
    $DateTime->setTimeZone($philippinesTimeZone);

    $currentDateTime = $DateTime->format('Y-m-d H:i:s');
    $employeeid = $_SESSION['employeeID'];
    $loginfo = $_SESSION['username'] . ' has logged in.';

    try {
      $sqlLogAdd = "INSERT INTO tbluserlogs (log_datetime, loginfo, employeeid) VALUES (:currentDateTime, :loginfo, :employeeid)";
      $statementLogAdd = $pdo->prepare($sqlLogAdd);
      $statementLogAdd->bindParam(':loginfo', $loginfo);
      $statementLogAdd->bindParam(':employeeid', $employeeid);
      $statementLogAdd->bindParam(':currentDateTime', $currentDateTime);
      $statementLogAdd->execute();
    } catch (PDOException $e) {
      // Handle the exception/error
      echo "Error: " . $e->getMessage();
    }


    // Redirect to the dashboard or any other page
    header("Location: dashboard.php");
    exit;

  } else if ($resultbaristaprep->rowCount() > 0) {
    $resultbarista1 = $resultbaristaprep->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['employeeID'] = $resultbarista1[0]['employeeID'];
    $_SESSION['position'] = $resultbarista1[0]['position']; // Store user information in a session variable
    $_SESSION['username'] = $resultbarista1[0]['username'];

    //add user log login
    $DateTime = new DateTime();
    $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
    $DateTime->setTimeZone($philippinesTimeZone);

    $currentDateTime = $DateTime->format('Y-m-d H:i:s');
    $employeeid = $_SESSION['employeeID'];
    $loginfo = $_SESSION['username'] . ' has logged in.';

    try {
      $sqlLogAdd = "INSERT INTO tbluserlogs (log_datetime, loginfo, employeeid) VALUES (:currentDateTime, :loginfo, :employeeid)";
      $statementLogAdd = $pdo->prepare($sqlLogAdd);
      $statementLogAdd->bindParam(':loginfo', $loginfo);
      $statementLogAdd->bindParam(':employeeid', $employeeid);
      $statementLogAdd->bindParam(':currentDateTime', $currentDateTime);
      $statementLogAdd->execute();
    } catch (PDOException $e) {
      // Handle the exception/error
      echo "Error: " . $e->getMessage();
    }
    // Redirect to the Orders
    header("Location: Orders.php");
    exit;

  } else if ($resultcashierprep->rowCount() > 0) {
    $resultcashier1 = $resultcashierprep->fetchAll(PDO::FETCH_ASSOC);
    $_SESSION['employeeID'] = $resultcashier1[0]['employeeID'];
    $_SESSION['position'] = $resultcashier1[0]['position']; // Store user information in a session variable
    $_SESSION['username'] = $resultcashier1[0]['username'];

    //add user log login
    $DateTime = new DateTime();
    $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
    $DateTime->setTimeZone($philippinesTimeZone);

    $currentDateTime = $DateTime->format('Y-m-d H:i:s');
    $employeeid = $_SESSION['employeeID'];
    $loginfo = $_SESSION['username'] . ' has logged in.';

    try {
      $sqlLogAdd = "INSERT INTO tbluserlogs (log_datetime, loginfo, employeeid) VALUES (:currentDateTime, :loginfo, :employeeid)";
      $statementLogAdd = $pdo->prepare($sqlLogAdd);
      $statementLogAdd->bindParam(':loginfo', $loginfo);
      $statementLogAdd->bindParam(':employeeid', $employeeid);
      $statementLogAdd->bindParam(':currentDateTime', $currentDateTime);
      $statementLogAdd->execute();
    } catch (PDOException $e) {
      // Handle the exception/error
      echo "Error: " . $e->getMessage();
    }

    // Redirect to the POS 
    header("Location: ../POS front-end/Main.php");
    exit;

  } else {
    // Authentication failed
    $error_message = "Invalid username or password"; // Set an error message
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Coffee Shop Login</title>
  <link rel="stylesheet" href="login.css" />
  <script>
    function togglePassLogin() {
      var x = document.getElementById("password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }
  </script>
</head>

<body>
  <div class="wrapper">
    <div class="heading">
      <h2>Welcome!</h2>
      <p>Admin Dashboard</p>
    </div>
    <form method="POST" action="login.php">
      <div class="input-group">
        <input type="text" id="username" class="input-field" name="username" placeholder="Username" />
      </div>

      <div class="input-group">
        <input type="password" id="password" class="input-field" name="password" placeholder="Password" />
        <input type="checkbox" style="margin:0 0 1em 1em;" class="custome-checkbox" onclick="togglePassLogin()"> show
        password
      </div>

      <div class="input-group">
        <button type="submit"><i class="fa-solid fa-arrow-right">Login</i></button>
      </div>
    </form>

    <?php
    if (isset($error_message)) {
      echo '<p>' . $error_message . '</p>';
    }
    ?>
    </form>
  </div>
  </div>
</body>

</html>