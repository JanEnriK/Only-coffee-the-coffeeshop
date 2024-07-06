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
$sql = "SELECT * FROM tblcustomers";
$statement = $pdo->prepare($sql);
$statement->execute();
$customerData = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
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
    </style>
</head>

<body>
    <div class="dashboard">
        <div class="sidebar">
            <h1>Coffee Shop</h1>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="coffeeshop_info.php">CoffeeShop</a></li>
                <li style="background-color: var(--primary-color);"><a href="accounts.php">Accounts</a></li>
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
            <h2>Customer Information
                <?php echo " (" . $_SESSION['username'] . " the " . $_SESSION['position'] . ") " ?>
            </h2>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customerData as $customer) : ?>
                            <tr>
                                <td>
                                    <?= $customer['customername'] ?>
                                </td>
                                <td>
                                    <?= $customer['contactnumber'] ?>
                                </td>
                                <td>
                                    <?= $customer['email'] ?>
                                </td>
                                <td>
                                    <?= $customer['address'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>