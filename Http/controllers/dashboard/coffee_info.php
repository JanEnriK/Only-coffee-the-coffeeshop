<?php
include "connect.php";
//Database connection
// $servername = "localhost";
// $user = "root";
// $pass = "";
// $dbname = "coffeeshop_db";

// try {
//     $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pass);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("Database connection failed: " . $e->getMessage());
// }

// Fetch customer data
$sql = "SELECT * FROM tblcoffeeshop";
$statement = $pdo->prepare($sql);
$statement->execute();
$coffeeshopData = $statement->fetchAll(PDO::FETCH_ASSOC);

//edit coffeeshop info

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['submit_edit'])) {
        $editId = $_POST['editId'];
        $editedShopName = $_POST['editShopName'];
        $editedBranch = $_POST['editBranch'];
        $editedAddress = $_POST['editAddress'];
        $editedContact = $_POST['editContact'];
        $editedEmail = $_POST['editEmail'];
        $editedVAT = $_POST['editVAT'];

        try {
            $sqlEdit = "UPDATE tblcoffeeshop SET shopname = :editedShopName, branch = :editedBranch, address = :editedAddress, contact_no = :editedContact, email = :editedEmail, VAT = :editedVAT WHERE coffeeshopid = :editId";
            $statementEdit = $pdo->prepare($sqlEdit);
            $statementEdit->bindParam(':editId', $editId);
            $statementEdit->bindParam(':editedShopName', $editedShopName);
            $statementEdit->bindParam(':editedBranch', $editedBranch);
            $statementEdit->bindParam(':editedAddress', $editedAddress);
            $statementEdit->bindParam(':editedContact', $editedContact);
            $statementEdit->bindParam(':editedEmail', $editedEmail);
            $statementEdit->bindParam(':editedVAT', $editedVAT);
            $statementEdit->execute();

            //add user log edited coffeeshop information
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has edited coffeeshop information.';

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

            header("Location: /admin_dashboard/info");
            exit(); // Ensure to stop script execution after redirection
        } catch (PDOException $e) {
            // Handle the exception/error
            echo "Error: " . $e->getMessage();
            // You can log the error or perform additional actions based on your requirements
        }
    }
}


view('dashboard/cs_info.view.php', [
    'coffeeshopData' => $coffeeshopData,
]);
