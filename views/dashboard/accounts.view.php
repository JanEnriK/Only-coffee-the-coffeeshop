<?php
include "connect.php";
// Database connection

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
$sql = "SELECT * FROM tblemployees WHERE position = 'guest'";
$statement = $pdo->prepare($sql);
$statement->execute();
$customerData = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require "partials/head.php"; ?>
<?php require "partials/nav.php"; ?>

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
        background-color: #2473c0;
        color: #fff;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
</style>
</head>

<body>
    <div class="dashboard">
        <div class="top-right">
            <a href="logout.php" class="login-button">Logout</a>
        </div>
        <div class="content">
            <h2>Customer Information
                <?php echo " (" . $_SESSION['user']['email'] . " the " . $_SESSION['user']['position'] . ") " ?>
            </h2>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customerData as $customer) : ?>
                            <tr>
                                <td>
                                    <?= $customer['firstname'] ?>
                                </td>
                                <td>
                                    <?= $customer['lastname'] ?>
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