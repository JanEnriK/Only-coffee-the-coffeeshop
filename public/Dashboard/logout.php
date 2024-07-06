<?php
// Database connection parameters
$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'coffeeshop_db';

// Create a database connection
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
session_start();


//add user log login
$DateTime = new DateTime();
$philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
$DateTime->setTimeZone($philippinesTimeZone);

$currentDateTime = $DateTime->format('Y-m-d H:i:s');
$employeeid = $_SESSION['employeeID'];
$loginfo = $_SESSION['username'] . ' has logged out.';

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


unset($_SESSION['position']);
unset($_SESSION['username']);
unset($_SESSION['employeeID']);
header("Location: Login.php");
