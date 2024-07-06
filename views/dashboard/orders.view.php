<?php require "partials/head.php";
include "connect.php";
?>

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
        <div class="sidebar">
            <h1>Coffee Shop</h1>
            <?php if ($_SESSION['position'] == "barista") : ?>
                <ul>
                    <li><a href="Orders.php">Orders</a></li>
                </ul>
            <?php else : ?>
                <?php require "partials/nav.php"; ?>
            <?php endif; ?>
        </div>

        <!--ORDERS TAB-->
        <div class="content">
            <h2>Orders
                <?php echo " (" . $_SESSION['user']['email'] . " the " . $_SESSION['user']['position'] . ") " ?>
            </h2>
            <div><!--SHOW ORDERS THAT ARE ACTIVE-->
                <form method="post" action="/admin_dashboard/orders">
                    <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="6" style="background-color: #222e5d;">
                                    <h3>Active Orders</h3>
                                </th>
                            </tr>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer User Name</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            // Database connection
                            // $servername = "localhost";
                            // $user = "root";
                            // $pass = "";
                            // $dbname = "coffeeshop_db";


                            // // Create a database connection
                            // $conn = new mysqli($servername, $user, $pass, $dbname);

                            // if ($conn->connect_error) {
                            //     die("Connection failed: " . $conn->connect_error);
                            // }

                            //for pdo
                            try {
                                $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pass);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                die("Database connection failed: " . $e->getMessage());
                            }

                            $sql_ordersActive = "SELECT oi.orderitem_id, oi.orderid, oi.productid,e.username, p.product_name, oi.quantity,oi.orderid,oi.status FROM tblorderitem oi JOIN tblproducts p ON oi.productid = p.product_id JOIN tblorders o ON oi.orderid = o.order_number JOIN tblemployees e ON o.customer_id = e.employeeID WHERE oi.status = 'active' and o.order_status = 'payed' and oi.orderitem_id IN (SELECT orderitem_id from tblorderitem) GROUP BY oi.orderitem_id;";

                            $result_ordersActive = $conn->query($sql_ordersActive);
                            while ($row = $result_ordersActive->fetch_assoc()) : ?>
                                <tr>
                                    <td>
                                        <?php echo $row['orderid']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['username']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['product_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['quantity']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['status']; ?>
                                    </td>
                                    <!-- hidden input for recieving productid inventoryid -->
                                    <input type="hidden" name="product_id" value="<?= $row['productid'] ?>">
                                    <td>
                                        <button type="submit" name="finish_order" value="<?php echo $row['orderitem_id']; ?>" onclick="return confirm('Are you sure you finish this order?');">Finish</button>
                                        <button type="submit" name="ended_order" value="<?php echo $row['orderitem_id']; ?>" onclick="return confirm('Are you sure you want to archive this order?');">End</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            </div>

            <!--SHOW ORDERS THAT ARE ENDED-->
            <div>
                <form method="post" action="/admin_dashboard/orders">
                    <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="6" style="background-color: #800000;">
                                    <h3>Ended Orders</h3>
                                </th>
                            </tr>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer User Name</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_ordersEnded = "SELECT oi.orderitem_id, oi.orderid, oi.productid,e.username, p.product_name, oi.quantity,oi.orderid,oi.status FROM tblorderitem oi JOIN tblproducts p ON oi.productid = p.product_id JOIN tblorders o ON oi.orderid = o.order_number JOIN tblemployees e ON o.customer_id = e.employeeID WHERE oi.status = 'ended' and o.order_status = 'payed' and oi.orderitem_id IN (SELECT orderitem_id from tblorderitem) GROUP BY oi.orderitem_id;
                            ";
                            $result_ordersEnded = $conn->query($sql_ordersEnded);
                            while ($row = $result_ordersEnded->fetch_assoc()) : ?>
                                <tr>
                                    <td>
                                        <?php echo $row['orderid']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['username']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['product_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['quantity']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['status']; ?>
                                    </td>
                                    <td>
                                        <button type="submit" name="unarchive_order" value="<?php echo $row['orderitem_id']; ?>" onclick="return confirm('Are you sure you want to return this order to active?');">Un-archive</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </form>
            </div>

            <!--SHOW ORDERS THAT ARE COMPLETED-->
            <div>
                <form method="post" action="/admin_dashboard/orders">
                    <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th colspan="5" style="background-color: #008000;">
                                    <h3>Completed Orders</h3>
                                </th>
                            </tr>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer User Name</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_ordersComplete = "SELECT oi.orderitem_id, oi.orderid, oi.productid,e.username, p.product_name, oi.quantity,oi.orderid,oi.status FROM tblorderitem oi JOIN tblproducts p ON oi.productid = p.product_id JOIN tblorders o ON oi.orderid = o.order_number JOIN tblemployees e ON o.customer_id = e.employeeID WHERE oi.status = 'completed' and o.order_status = 'payed' and oi.orderitem_id IN (SELECT orderitem_id from tblorderitem) GROUP BY oi.orderitem_id;
                            ";
                            $result_ordersComplete = $conn->query($sql_ordersComplete);
                            while ($row = $result_ordersComplete->fetch_assoc()) : ?>
                                <tr>
                                    <td>
                                        <?php echo $row['orderid']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['username']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['product_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['quantity']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['status']; ?>
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