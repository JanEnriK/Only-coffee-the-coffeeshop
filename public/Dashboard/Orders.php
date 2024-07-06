<?php
session_start();
if (!isset($_SESSION['position']) && !isset($_SESSION['username']) && !isset($_SESSION['employeeID'])) {
    header("Location: Login.php");
    exit(); //Ensures that the script stops executing after redirection
} elseif ($_SESSION['position'] != "admin" && $_SESSION['position'] != "barista") {
    header("Location: Login.php");

    exit();
}
$servername = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "coffeeshop_db";

// Create a database connection
$conn = new mysqli($servername, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//for pdo
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


// complete order button 
if (isset($_POST['finish_order'])) {
    $orderitemID = $_POST['finish_order'];

    // Construct the SQL query for updating the record
    $update_sql = "UPDATE tblorderitem
                   SET status = 'completed'
                   WHERE tblorderitem.orderitem_id= $orderitemID";

    if ($conn->query($update_sql) === TRUE) {
        //add user log [complete an order]
        $DateTime = new DateTime();
        $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
        $DateTime->setTimeZone($philippinesTimeZone);

        $currentDateTime = $DateTime->format('Y-m-d H:i:s');
        $employeeid = $_SESSION['employeeID'];
        $loginfo = $_SESSION['username'] . ' has completed an order.';

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
        header('Location: ' . $_SERVER['PHP_SELF']); // Use Location: to specify the redirect location
        exit(); // Exit to prevent further execution
    } else {
        // Handle the update errors
        echo "Error: " . $conn->error;
    }
}

// ended order button 
if (isset($_POST['ended_order'])) {
    $orderitemID = $_POST['ended_order'];

    // Construct the SQL query for updating the record
    $endorder_sql = "UPDATE tblorderitem
                   SET status = 'ended'
                   WHERE tblorderitem.orderitem_id = $orderitemID";

    if ($conn->query($endorder_sql) === TRUE) {

        //add user log [archived an order]
        $DateTime = new DateTime();
        $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
        $DateTime->setTimeZone($philippinesTimeZone);

        $currentDateTime = $DateTime->format('Y-m-d H:i:s');
        $employeeid = $_SESSION['employeeID'];
        $loginfo = $_SESSION['username'] . ' has archived an order.';

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
        header('Location: ' . $_SERVER['PHP_SELF']); // Use Location: to specify the redirect location
        exit(); // Exit to prevent further execution
    } else {
        // Handle the update errors
        echo "Error: " . $conn->error;
    }
}


// Unarchive button 
if (isset($_POST['unarchive_order'])) {
    $orderitemID = $_POST['unarchive_order'];

    // Construct the SQL query for updating the record
    $update_sql = "UPDATE tblorderitem
                   SET status = 'active'
                   WHERE tblorderitem.orderitem_id = $orderitemID";

    if ($conn->query($update_sql) === TRUE) {
        //add user log [unarchived an order]
        $DateTime = new DateTime();
        $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
        $DateTime->setTimeZone($philippinesTimeZone);

        $currentDateTime = $DateTime->format('Y-m-d H:i:s');
        $employeeid = $_SESSION['employeeID'];
        $loginfo = $_SESSION['username'] . ' has unarchived an order.';

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
        header('Location: ' . $_SERVER['PHP_SELF']); // Use Location: to specify the redirect location
        exit(); // Exit to prevent further execution
    } else {
        // Handle the update errors
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Orders</title>

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
    </style>
</head>

<body>
    <div class="dashboard">
        <div class="sidebar">
            <h1>Coffee Shop</h1>
            <?php if ($_SESSION['position'] == "barista") : ?>
                <ul>
                    <li><a href="Orders.php">Orders</a></li>
                </ul>
            <?php else : ?>
                <ul>
                    <li><a href="dashboard.php">Home</a></li>
                    <li><a href="coffeeshop_info.php">CoffeeShop</a></li>
                    <li><a href="accounts.php">Accounts</a></li>
                    <li style="background-color: var(--primary-color);"><a href="Orders.php">Orders</a></li>
                    <li><a href="Inventory.php">Inventory</a></li>
                    <li><a href="Products.php">Products</a></li>
                    <li><a href="Staff.php">Staff</a></li>
                    <li><a href="Reports.php">Reports</a></li>
                    <li><a href="../POS front-end/Main.php">POS</a></li>
                </ul>

            <?php endif; ?>
        </div>
        <div class="top-right">
            <a href="logout.php" class="login-button">Logout</a>
        </div>

        <!--ORDERS TAB-->
        <div class="content">
            <h2>Orders
                <?php echo " (" . $_SESSION['username'] . " the " . $_SESSION['position'] . ") " ?>
            </h2>
            <div><!--SHOW ORDERS THAT ARE ACTIVE-->
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="6" style="background-color: blue;">
                                    <h3>Active Orders</h3>
                                </th>
                            </tr>
                            <tr>
                                <th>Customer Name</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Order Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_ordersActive = "SELECT
                            oi.orderitem_id,
                            c.customername,
                            p.product_name,
                            oi.quantity AS total_quantity,
                            o.order_type,
                            oi.status AS order_status
                        FROM
                            tblorderitem oi
                        JOIN
                            tblorders o ON oi.orderid = o.order_id
                        JOIN
                            tblproducts p ON oi.productid = p.product_id
                        JOIN
                            tblcustomers c ON o.customer_id = c.customerid
                        WHERE
                            oi.status = 'active'
                        GROUP BY
                            c.customerid, c.customername, p.product_name, o.order_type, oi.status";

                            $result_ordersActive = $conn->query($sql_ordersActive);
                            while ($row = $result_ordersActive->fetch_assoc()) : ?>
                                <tr>
                                    <td>
                                        <?php echo $row['customername']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['product_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['total_quantity']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['order_type']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['order_status']; ?>
                                    </td>
                                    <td>
                                        <button type="submit" name="finish_order" value="<?php echo $row['orderitem_id']; ?>">Finish</button>
                                        <button type="submit" name="ended_order" value="<?php echo $row['orderitem_id']; ?>">End</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <!--SHOW ORDERS THAT ARE COMPLETED-->
            <div>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="5" style="background-color: green;">
                                    <h3>Completed Orders</h3>
                                </th>
                            </tr>
                            <tr>
                                <th>Customer Name</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Order Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_ordersComplete = "SELECT
                            oi.orderitem_id,
                            c.customername,
                            p.product_name,
                            oi.quantity AS total_quantity,
                            o.order_type,
                            oi.status AS order_status
                        FROM
                            tblorderitem oi
                        JOIN
                            tblorders o ON oi.orderid = o.order_id
                        JOIN
                            tblproducts p ON oi.productid = p.product_id
                        JOIN
                            tblcustomers c ON o.customer_id = c.customerid
                        WHERE
                            oi.status = 'completed'
                        GROUP BY
                            c.customerid, c.customername, p.product_name, o.order_type, oi.status
                            ";
                            $result_ordersComplete = $conn->query($sql_ordersComplete);
                            while ($row = $result_ordersComplete->fetch_assoc()) : ?>
                                <tr>
                                    <td>
                                        <?php echo $row['customername']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['product_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['total_quantity']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['order_type']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['order_status']; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <!--SHOW ORDERS THAT ARE ENDED-->
            <div>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="6" style="background-color: red;">
                                    <h3>Archived Orders</h3>
                                </th>
                            </tr>
                            <tr>
                                <th>Customer Name</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Order Type</th>
                                <th>Status</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_ordersEnded = "SELECT
                            oi.orderitem_id,
                            c.customername,
                            p.product_name,
                            oi.quantity AS total_quantity,
                            o.order_type,
                            oi.status AS order_status
                        FROM
                            tblorderitem oi
                        JOIN
                            tblorders o ON oi.orderid = o.order_id
                        JOIN
                            tblproducts p ON oi.productid = p.product_id
                        JOIN
                            tblcustomers c ON o.customer_id = c.customerid
                        WHERE
                            oi.status = 'ended'
                        GROUP BY
                            c.customerid, c.customername, p.product_name, o.order_type, oi.status
                            ";
                            $result_ordersEnded = $conn->query($sql_ordersEnded);
                            while ($row = $result_ordersEnded->fetch_assoc()) : ?>
                                <tr>
                                    <td>
                                        <?php echo $row['customername']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['product_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['total_quantity']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['order_type']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['order_status']; ?>
                                    </td>
                                    <td>
                                        <button type="submit" name="unarchive_order" value="<?php echo $row['orderitem_id']; ?>">Un-archive</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            </div>
            <br><br>
        </div>

    </div>
</body>

</html>