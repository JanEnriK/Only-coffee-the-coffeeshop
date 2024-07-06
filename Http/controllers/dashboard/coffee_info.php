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
        // Fetch all form inputs
        $editId = $_POST['editId'];
        $editedShopName = $_POST['editShopName'];
        $editedBranch = $_POST['editBranch'];
        $editedAddress = $_POST['editAddress'];
        $editedContact = $_POST['editContact'];
        $editedEmail = $_POST['editEmail'];
        $editedVAT = $_POST['editVAT'];
        $editedWDST = $_POST['editWDST'];
        $editedWDET = $_POST['editWDET'];
        $editedWEST = $_POST['editWEST'];
        $editedWEET = $_POST['editWEET'];
        $editedDateEstablished = $_POST['editDateEstablished'];
        $editedTagline = $_POST['editTagline'];

        $editedVision = [];

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'editVision') === 0) {
                $index = substr($key, 10); // Extract the index after 'editVision'
                $visionValue = htmlspecialchars($value); // Sanitize input if needed
                // Process or store $visionValue as needed
                $editedVision[$index] = $visionValue;
            }
        }
        $editedVisionJSON = json_encode($editedVision);



        // Handle file upload for logo
        if ($_FILES['editLogo']['error'] == UPLOAD_ERR_OK) {
            // Process file upload as needed
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["editLogo"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validate file
            $check = @getimagesize($_FILES["editLogo"]["tmp_name"]);
            if ($check === false) {
                $errors['body'] = "File is not an image.";
                $uploadOk = 0;
            }
            if ($_FILES["editLogo"]["size"] > 50000000) {
                $errors['body'] = "Sorry, your file is too large.";
                $uploadOk = 0;
            }
            if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                $errors['body'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Move uploaded file if no errors
            if ($uploadOk) {
                if (move_uploaded_file($_FILES["editLogo"]["tmp_name"], $target_file)) {
                    $editedLogo = basename($_FILES["editLogo"]["name"]);
                } else {
                    $errors['body'] = "Error uploading file.";
                }
            }
        } else {
            $editedLogo = null; // Handle no new logo upload
        }


        try {
            // Update SQL statement
            if (isset($editedLogo)) {
                $sqlEdit = "UPDATE tblcoffeeshop SET tagline = :editedTagline, date_established = :editedDateEstablished, logo = :logo, shopname = :editedShopName, branch = :editedBranch, address = :editedAddress, contact_no = :editedContact, email = :editedEmail, VAT = :editedVAT, weekday_start_time = :editedWDST, weekday_end_time = :editedWDET, weekend_start_time = :editedWEST, weekend_end_time = :editedWEET, vision = :editedVision WHERE coffeeshopid = :editId";
            } else {
                $sqlEdit = "UPDATE tblcoffeeshop SET tagline = :editedTagline, date_established = :editedDateEstablished, shopname = :editedShopName, branch = :editedBranch, address = :editedAddress, contact_no = :editedContact, email = :editedEmail, VAT = :editedVAT, weekday_start_time = :editedWDST, weekday_end_time = :editedWDET, weekend_start_time = :editedWEST, weekend_end_time = :editedWEET, vision = :editedVision WHERE coffeeshopid = :editId";
            }
            $statementEdit = $pdo->prepare($sqlEdit);
            $statementEdit->bindParam(':editId', $editId);
            $statementEdit->bindParam(':editedShopName', $editedShopName);
            $statementEdit->bindParam(':editedBranch', $editedBranch);
            $statementEdit->bindParam(':editedAddress', $editedAddress);
            $statementEdit->bindParam(':editedContact', $editedContact);
            $statementEdit->bindParam(':editedEmail', $editedEmail);
            $statementEdit->bindParam(':editedVAT', $editedVAT);
            $statementEdit->bindParam(':editedWDST', $editedWDST);
            $statementEdit->bindParam(':editedWDET', $editedWDET);
            $statementEdit->bindParam(':editedWEST', $editedWEST);
            $statementEdit->bindParam(':editedWEET', $editedWEET);
            if (isset($editedLogo)) {
                $statementEdit->bindParam(':logo', $editedLogo);
            }
            $statementEdit->bindParam(':editedDateEstablished', $editedDateEstablished);
            $statementEdit->bindParam(':editedTagline', $editedTagline);
            $statementEdit->bindParam(':editedVision', $editedVisionJSON);
            $statementEdit->execute();

            // // Add user log for editing coffee shop information
            // $DateTime = new DateTime();
            // $philippinesTimeZone = new DateTimeZone('Asia/Manila');
            // $DateTime->setTimeZone($philippinesTimeZone);
            // $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            // $employeeid = $_SESSION['employeeID'];
            // $loginfo = $_SESSION['username'] . ' has edited coffee shop information.';

            // $sqlLogAdd = "INSERT INTO tbluserlogs (log_datetime, loginfo, employeeid) VALUES (:currentDateTime, :loginfo, :employeeid)";
            // $statementLogAdd = $pdo->prepare($sqlLogAdd);
            // $statementLogAdd->bindParam(':currentDateTime', $currentDateTime);
            // $statementLogAdd->bindParam(':loginfo', $loginfo);
            // $statementLogAdd->bindParam(':employeeid', $employeeid);
            // $statementLogAdd->execute();

            // Redirect after successful update
            header("Location: /admin_dashboard/info");
            exit(); // Stop script execution after redirection
        } catch (PDOException $e) {
            // Handle database errors
            echo "Error: " . $e->getMessage();
        }
    }
}



view('dashboard/cs_info.view.php', [
    'coffeeshopData' => $coffeeshopData,
]);
