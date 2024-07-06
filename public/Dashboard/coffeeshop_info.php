<?php
session_start();
if (!isset($_SESSION['position']) && !isset($_SESSION['username']) && !isset($_SESSION['employeeID'])) {
    header("Location: Login.php");
    exit(); //Ensures that the script stops executing after redirection
} elseif ($_SESSION['position'] != "admin") {
    header("Location: Login.php");

    exit();
}

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

        try {
            $sqlEdit = "UPDATE tblcoffeeshop SET shopname = :editedShopName, branch = :editedBranch, address = :editedAddress, contact_no = :editedContact, email = :editedEmail WHERE coffeeshopid = :editId";
            $statementEdit = $pdo->prepare($sqlEdit);
            $statementEdit->bindParam(':editId', $editId);
            $statementEdit->bindParam(':editedShopName', $editedShopName);
            $statementEdit->bindParam(':editedBranch', $editedBranch);
            $statementEdit->bindParam(':editedAddress', $editedAddress);
            $statementEdit->bindParam(':editedContact', $editedContact);
            $statementEdit->bindParam(':editedEmail', $editedEmail);

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

            header("Location: coffeeshop_info.php");
            exit(); // Ensure to stop script execution after redirection
        } catch (PDOException $e) {
            // Handle the exception/error
            echo "Error: " . $e->getMessage();
            // You can log the error or perform additional actions based on your requirements
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Information</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F5F5DC;
        }

        table {
            width: 100%;
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

        /*edit button style*/
        .button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button.edit-button {
            background-color: #008CBA;
            color: white;
        }

        .button.delete-button {
            background-color: #FF6347;
            color: white;
        }

        .button.add-button {
            background-color: #4CAF50;
            color: white;
            margin-right: 5px;
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
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            overflow: auto;
            box-sizing: border-box;
        }

        .overlay-content {
            max-height: 100%;
            /* Adjust maximum height as needed */
            max-width: 100%;
            /* Adjust maximum width as needed */
            overflow-y: auto;
        }
    </style>
    <script>
        // toggle edit coffeeshop form
        document.addEventListener('DOMContentLoaded', function() {
            const editForm = document.getElementById('editInfo');
            const overlay = document.getElementById('editOverlay');
            const closeFormBtn = document.getElementById('closeFormBtn');
            const body = document.body;
            // Initially hide the overlay form
            overlay.style.display = 'none';

            // Show the overlay form when the button is clicked
            editForm.addEventListener('click', function() {
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
    <!-- form edit coffeeshop overlay-->
    <div class="overlay" id="editOverlay">
        <div class="overlay-content">
            <div class="info-box">
                <button id="closeFormBtn" class="button delete-button">X</button>
                <h2>Add New Product</h2>
                <?php foreach ($coffeeshopData as $coffeeshop) : ?>
                    <form method="post" action="">
                        <input type="hidden" class="form-control" name="editId" value="<?= $coffeeshop['coffeeshopid'] ?>">
                        <div class="form-group">
                            <label for="new_product">CoffeeShop Name:</label>
                            <input type="text" class="form-control" name="editShopName" value="<?= $coffeeshop['shopname'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="new_productDescription">Branch:</label>
                            <input type="text" class="form-control" name="editBranch" value="<?= $coffeeshop['branch'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="new_price">Address: </label>
                            <input type="text" class="form-control" name="editAddress" value="<?= $coffeeshop['address'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="new_category">Contact Number: </label>
                            <input type="number" class="form-control" name="editContact" value="<?= $coffeeshop['contact_no'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="new_category">Email: </label>
                            <input type="email" class="form-control" name="editEmail" value="<?= $coffeeshop['email'] ?>" required>
                        </div>
                        <button type="submit" name="submit_edit" class="button edit-button" style="width:100%;">💾Save</button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="dashboard">
        <div class="sidebar">
            <h1>Coffee Shop</h1>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li style="background-color: var(--primary-color);"><a href="coffeeshop_info.php">CoffeeShop</a></li>
                <li><a href="accounts.php">Accounts</a></li>
                <li><a href="Orders.php">Orders</a></li>
                <li><a href="Inventory.php">Inventory</a></li>
                <li><a href="Products.php">Products</a></li>
                <li><a href="Staff.php">Staff</a></li>
                <li><a href="Reports.php">Reports</a></li>
                <li><a href="../POS front-end/Main.php">POS</a></li>
            </ul>
        </div>
        <div class="top-right">
            <a href="logout.php" class="login-button">Logout</a>
        </div>
        <div class="content">
            <h2>CoffeeShop Information
                <?php echo " (" . $_SESSION['username'] . " the " . $_SESSION['position'] . ") " ?>
            </h2>
            <?php foreach ($coffeeshopData as $coffeeshop) : ?>
                <div class="info-box" style="width:inherit; height:inherit; margin-top:5em;">
                    <div style="display: flex; justify-content: space-between;">
                        <button type="button" class="button edit-button" id="editInfo">✎Edit</button>
                    </div>
                    <div>
                        <h4><b>CoffeeShop Name:</b>
                            <p style="color:#333; display: inline;">
                                <?php echo $coffeeshop['shopname']; ?>
                            </p>
                        </h4>
                    </div>
                    <div>
                        <h4><b>Branch:</b>
                            <p style="color:#333; display: inline;">
                                <?php echo $coffeeshop['branch']; ?>
                            </p>
                        </h4>
                    </div>
                    <div>
                        <h4><b>Address:</b>
                            <p style="color:#333; display: inline;">
                                <?php echo $coffeeshop['address']; ?>
                            </p>
                        </h4>
                    </div>
                    <div>
                        <h4><b>Contact Number:</b>
                            <p style="color:#333; display: inline;">
                                <?php echo $coffeeshop['contact_no']; ?>
                            </p>
                        </h4>
                    </div>
                    <div>
                        <h4><b>Email:</b>
                            <p style="color:#333; display: inline;">
                                <?php echo $coffeeshop['email']; ?>
                            </p>
                        </h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>

</html>