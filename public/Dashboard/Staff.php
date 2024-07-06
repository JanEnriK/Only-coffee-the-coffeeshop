<?php
session_start();
if (!isset($_SESSION['position']) && !isset($_SESSION['username']) && !isset($_SESSION['employeeID'])) {
    header("Location: Login.php");
    exit(); //Ensures that the script stops executing after redirection
} elseif ($_SESSION['position'] != "admin") {
    header("Location: Login.php");

    exit();
}

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "coffeeshop_db";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//for pdo
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


// CREATE operation
if (isset($_POST['create'])) {
    $firstname = $_POST['create_firstname'];
    $lastname = $_POST['create_lastname'];
    $position = $_POST['create_position'];
    $username = $_POST['create_username'];
    $password = $_POST['create_password'];

    // You can add validation and sanitization here

    $hiredate = $_POST['create_hiredate']; // Current date and time

    $insert_sql = "INSERT INTO tblemployees (firstname, lastname, position, hiredate, username, password)
                VALUES ('$firstname', '$lastname', '$position', '$hiredate', '$username', '$password')";

    if ($conn->query($insert_sql) === TRUE) {
        // Handle a successful insertion (you can redirect or show a success message)
        echo "Record created successfully!";
        //add user log [add new employee]
        $DateTime = new DateTime();
        $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
        $DateTime->setTimeZone($philippinesTimeZone);

        $currentDateTime = $DateTime->format('Y-m-d H:i:s');
        $employeeid = $_SESSION['employeeID'];
        $loginfo = $_SESSION['username'] . ' has added a new employee.';

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
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to the same page to refresh the data
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
    $result = $conn->query("SELECT hiredate FROM tblemployees WHERE employeeID = $employeeID");
    $row = $result->fetch_assoc();
    $existingHireDate = $row['hiredate'];
}

if (isset($_POST['save'])) {
    // Handle the save operation - Save the changes
    $employeeID = $_POST['save'];
    $firstname = $_POST['firstname_' . $employeeID];
    $lastname = $_POST['lastname_' . $employeeID];
    $position = $_POST['position_' . $employeeID];
    $hiredate = $_POST['hiredate_' . $employeeID];
    $username = $_POST['username_' . $employeeID];
    $password = $_POST['password_' . $employeeID];

    // Construct the SQL query for updating the record
    $update_sql = "UPDATE tblemployees 
                 SET firstname = '$firstname',
                     lastname = '$lastname',
                     position = '$position',
                     hiredate = '$hiredate',
                     username = '$username',
                     password = '$password'
                 WHERE employeeID = $employeeID";

    if ($conn->query($update_sql) === TRUE) {

        //add user log [update employee]
        $DateTime = new DateTime();
        $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
        $DateTime->setTimeZone($philippinesTimeZone);

        $currentDateTime = $DateTime->format('Y-m-d H:i:s');
        $employeeid = $_SESSION['employeeID'];
        $loginfo = $_SESSION['username'] . ' has edited an employee information.';

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
        header('Location: ' . $_SERVER['PHP_SELF']);
    } else {
        // Handle the update error
        echo "Error: " . $conn->error;
    }
}



// DELETE operation
if (isset($_POST['delete'])) {
    $employeeID = $_POST['delete'];

    $delete_sql = "DELETE FROM tblemployees WHERE employeeID = $employeeID";

    if ($conn->query($delete_sql) === TRUE) {

        //add user log [delete a employee]
        $DateTime = new DateTime();
        $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
        $DateTime->setTimeZone($philippinesTimeZone);

        $currentDateTime = $DateTime->format('Y-m-d H:i:s');
        $employeeid = $_SESSION['employeeID'];
        $loginfo = $_SESSION['username'] . ' has deleted a employee.';

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

        header('Location: ' . $_SERVER['PHP_SELF']); // Refresh the page
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Staffs</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard.css" />

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F5F5DC;
        }


        table {
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            color: #333;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #4caf50;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button.add-button {
            background-color: #4CAF50;
            color: white;
        }

        .button.edit-button {
            background-color: #008CBA;
            color: white;
        }

        .button.delete-button {
            background-color: #FF6347;
            color: white;
        }

        /*STYLE FORDA OVER LAY FORM */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            /* Ensure the overlay is on top */
        }
    </style>

    <script>
        function togglePassForm() {
            var x = document.getElementById("create_password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        function togglePassEdit() {
            var x = document.getElementById("edit_pass");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const addForm = document.getElementById('addForm');
            const overlay = document.getElementById('overlay');
            const closeFormBtn = document.getElementById('closeFormBtn');
            const body = document.body;
            // Initially hide the overlay form
            overlay.style.display = 'none';

            // Show the overlay form when the button is clicked
            addForm.addEventListener('click', function() {
                overlay.style.display = 'flex';
                body.style.overflow = 'hidden';
            });

            // Close the overlay form when the close button is clicked
            closeFormBtn.addEventListener('click', function() {
                overlay.style.display = 'none';
                body.style.overflow = 'visible';
            });
        });
    </script>
</head>

<body>
    <div class="overlay" id="overlay">
        <div class="info-box">
            <button id="closeFormBtn" class="button delete-button">X</button>
            <h2>Create New Employee</h2>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group">
                    <label for="create_firstname">First Name:</label>
                    <input type="text" class="form-control" name="create_firstname" id="create_firstname" required>
                </div>
                <div class="form-group">
                    <label for="create_lastname">Last Name:</label>
                    <input type="text" class="form-control" name="create_lastname" id="create_lastname" required>
                </div>
                <div class="form-group">
                    <label for="create_position">Position:</label>
                    <select name="create_position" id="create_position" class="form-control" required>
                        <option value="" selected disabled>Position:</option>
                        <option value="admin">Admin</option>
                        <option value="cashier">Cashier</option>
                        <option value="barista">Barista</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="create_lastname">Hire Date:</label>
                    <input type="date" class="form-control" name="create_hiredate" id="create_hiredate" required>
                </div>
                <div class="form-group">
                    <label for="create_username">Username:</label>
                    <input type="text" class="form-control" name="create_username" id="create_username" required>
                </div>
                <div class="form-group">
                    <label for="create_password">Password:</label>
                    <input type="password" class="form-control" name="create_password" id="create_password" required>
                    <input type="checkbox" onclick="togglePassForm()"> show password
                </div>
                <button type="submit" name="create">Create</button>
            </form>
        </div>
    </div>

    <div class="dashboard">
        <div class="sidebar">
            <h1>Coffee Shop</h1>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="coffeeshop_info.php">CoffeeShop</a></li>
                <li><a href="accounts.php">Accounts</a></li>
                <li><a href="Orders.php">Orders</a></li>
                <li><a href="Inventory.php">Inventory</a></li>
                <li><a href="Products.php">Products</a></li>
                <li style="background-color: var(--primary-color);"><a href="Staff.php">Staff</a></li>
                <li><a href="Reports.php">Reports</a></li>
                <li><a href="../POS front-end/Main.php">POS</a></li>
            </ul>
        </div>
        <div class="top-right">
            <a href="logout.php" class="login-button">Logout</a>
        </div>
        <div class="content">
            <h2>Staff Information
                <?php echo " (" . $_SESSION['username'] . " the " . $_SESSION['position'] . ") " ?>
            </h2>
            <div>
                <button type="button" class="button add-button" id="addForm" onclick="toggleForm()">+ Add User</button>
            </div>
            <div>
                <div class="table-responsive">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <table id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Position</th>
                                    <th>Hire Date</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $servername = "127.0.0.1";
                                $username = "root";
                                $password = "";
                                $dbname = "coffeeshop_db";

                                $conn = new mysqli($servername, $username, $password, $dbname);

                                if ($conn->connect_error) {
                                    die("Connection failed: " . $conn->connect_error);
                                }
                                $sql = "SELECT * FROM tblemployees ORDER BY position";
                                $result = $conn->query($sql);

                                $conn->close();
                                while ($row = $result->fetch_assoc()) : ?>
                                    <tr>
                                        <td>
                                            <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                                <input type="text" name="firstname_<?php echo $row['employeeID']; ?>" value="<?php echo $row['firstname']; ?>" required>
                                            <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                                <?php echo $row['firstname']; ?>
                                            <?php else : ?>
                                                <?php echo $row['firstname']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                                <input type="text" name="lastname_<?php echo $row['employeeID']; ?>" value="<?php echo $row['lastname']; ?>" required>
                                            <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                                <?php echo $row['lastname']; ?>
                                            <?php else : ?>
                                                <?php echo $row['lastname']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                                <select name="position_<?php echo $row['employeeID']; ?>" id="position_<?php echo $row['employeeID']; ?>" required>
                                                    <option value="" disabled>Select Position:</option>
                                                    <option value="admin" <?php echo ($row['position'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                    <option value="cashier" <?php echo ($row['position'] == 'cashier') ? 'selected' : ''; ?>>Cashier</option>
                                                    <option value="barista" <?php echo ($row['position'] == 'barista') ? 'selected' : ''; ?>>Barista</option>
                                                </select>
                                            <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                                <?php echo $row['position']; ?>
                                            <?php else : ?>
                                                <?php echo $row['position']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                                <input type="date" name="hiredate_<?php echo $row['employeeID']; ?>" value="<?php echo $row['hiredate']; ?>" required>
                                            <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                                <?php echo $row['hiredate']; ?>
                                            <?php else : ?>
                                                <?php echo $row['hiredate']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                                <input type="text" name="username_<?php echo $row['employeeID']; ?>" value="<?php echo $row['username']; ?>" required>
                                            <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                                <?php echo $row['username']; ?>
                                            <?php else : ?>
                                                <?php echo $row['username']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                                <input type="password" name="password_<?php echo $row['employeeID']; ?>" value="<?php echo $row['password']; ?>" id="edit_pass" required>
                                                <input type="checkbox" onclick="togglePassEdit()"> show password
                                            <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                                <?php echo $row['password']; ?>
                                            <?php else : ?>
                                                <?php echo $row['password']; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                                <button type="submit" name="save" class="button edit-button" value="<?php echo $row['employeeID']; ?>">💾</button>
                                                <button type="submit" name="cancel" value="<?php echo $row['employeeID']; ?>">Cancel</button>
                                            <?php elseif ($row['position'] == "admin") : ?>
                                                <button type="submit" name="edit" class="button edit-button" value="<?php echo $row['employeeID']; ?>">✎</button>
                                            <?php else : ?>
                                                <button type="submit" name="edit" class="button edit-button" value="<?php echo $row['employeeID']; ?>">✎</button>
                                                <button type="submit" name="delete" class="button delete-button" value="<?php echo $row['employeeID']; ?>">✖</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>

</html>