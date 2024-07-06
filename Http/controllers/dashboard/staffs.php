<?php
include "connect.php";
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "coffeeshop_db";

// // Create a database connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// try {
//     $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("Database connection failed: " . $e->getMessage());
// }


// CREATE operation
if (isset($_POST['create'])) {
    $firstname = $_POST['create_firstname'];
    $lastname = $_POST['create_lastname'];
    $email = $_POST['create_email'];
    $position = $_POST['create_position'];
    $username = $_POST['create_username'];
    $password = $_POST['create_password'];
    $hpassword = password_hash($password, PASSWORD_BCRYPT);

    // You can add validation and sanitization here

    $hiredate = $_POST['create_hiredate']; // Current date and time

    $insert_sql = "INSERT INTO tblemployees (firstname, lastname, email, position, hiredate, username, password)
                VALUES ('$firstname', '$lastname','$email', '$position', '$hiredate', '$username', '$hpassword')";

    if ($conn->query($insert_sql) === TRUE) {
        // Handle a successful insertion (you can redirect or show a success message)
        echo "Record created successfully!";

        //add user log [add new employee]
        $DateTime = new DateTime();
        $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
        $DateTime->setTimeZone($philippinesTimeZone);

        $currentDateTime = $DateTime->format('Y-m-d H:i:s');
        $employeeid = $_SESSION['user']['id'];
        $loginfo = $_SESSION['user']['email'] . ' has added a new employee.';

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
        header('Location: /admin_dashboard/staffs'); // Redirect to the same page to refresh the data
    } else {
        // Handle the insertion error
        echo "Error: " . $conn->error;
    }
}

// READ operation
$sql = "SELECT * FROM tblemployees";
$result = $conn->query($sql);

if (isset($_POST['edit'])) {

    $employeeID = $_POST['edit'];

    // Handle the edit operation - Display the editable fields
    $firstname = isset($_POST['firstname_' . $employeeID]) ? $_POST['firstname_' . $employeeID] : '';
    $lastname = isset($_POST['lastname_' . $employeeID]) ? $_POST['lastname_' . $employeeID] : '';
    $position = isset($_POST['position_' . $employeeID]) ? $_POST['position_' . $employeeID] : '';
    $username = isset($_POST['username_' . $employeeID]) ? $_POST['username_' . $employeeID] : '';
    $password = isset($_POST['password_' . $employeeID]) ? $_POST['password_' . $employeeID] : '';

    // Retrieve the existing hiredate
    $result = $conn->query("SELECT * FROM tblemployees WHERE employeeID = $employeeID ORDER BY hiredate ASC");
    $row = $result->fetch_assoc();
    $existingHireDate = $row['hiredate'];
}

if (isset($_POST['save'])) {
    // Handle the save operation - Save the changes
    $employeeID = $_POST['save'];
    $firstname = $_POST['firstname_' . $employeeID];
    $lastname = $_POST['lastname_' . $employeeID];
    $email = $_POST['email_' . $employeeID];
    $position = $_POST['position_' . $employeeID];
    $hiredate = $_POST['hiredate_' . $employeeID];
    $username = $_POST['username_' . $employeeID];
    // $password = $_POST['password_' . $employeeID];
    // $hpassword = password_hash($password, PASSWORD_BCRYPT);

    // Construct the SQL query for updating the record
    $update_sql = "UPDATE tblemployees 
                 SET firstname = '$firstname',
                     lastname = '$lastname',
                     email = '$email',
                     position = '$position',
                     hiredate = '$hiredate',
                     username = '$username'
                    --  password = '$password'
                 WHERE employeeID = $employeeID";

    if ($conn->query($update_sql) === TRUE) {

        //add user log [update employee]
        $DateTime = new DateTime();
        $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
        $DateTime->setTimeZone($philippinesTimeZone);

        $currentDateTime = $DateTime->format('Y-m-d H:i:s');
        $employeeid = $_SESSION['user']['id'];
        $loginfo = $_SESSION['user']['email'] . ' has edited an employee information.';

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

        // Handle a successful update (you can redirect or show a success message)
        echo "Record updated successfully!";
        header('Location: /admin_dashboard/staffs');
    } else {
        // Handle the update error
        echo "Error: " . $conn->error;
    }
}



// DELETE operation
if (isset($_POST['delete'])) {
    $employeeID = $_POST['delete'];

    $deletefeedback_sql = "DELETE FROM tblfeedback WHERE customerid = $employeeID";
    $deletelogs_sql = "DELETE FROM tbluserlogs WHERE employeeid = $employeeID";
    $delete_sql = "DELETE FROM tblemployees WHERE employeeID = $employeeID";

    if ($conn->query($deletelogs_sql) && $conn->query($deletefeedback_sql) && $conn->query($delete_sql)) {

        //add user log [delete a employee]
        $DateTime = new DateTime();
        $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
        $DateTime->setTimeZone($philippinesTimeZone);

        $currentDateTime = $DateTime->format('Y-m-d H:i:s');
        $employeeid = $_SESSION['user']['id'];
        $loginfo = $_SESSION['user']['email'] . ' has deleted a employee.';

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

        header('Location: /admin_dashboard/staffs'); // Refresh the page
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();

view('dashboard/staffs.view.php');
