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
$dbname = "coffeeshop";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch data for the graphs
// Total number of orders
$sqlOrders = "SELECT COUNT(*) AS order_ID FROM tblorders";
$statementOrders = $pdo->prepare($sqlOrders);
$statementOrders->execute();
$orderData = $statementOrders->fetch(PDO::FETCH_ASSOC);

// Total visitors
$sqlVisitors = "SELECT COUNT(*) AS customer_ID FROM tblcustomers";
$statementVisitors = $pdo->prepare($sqlVisitors);
$statementVisitors->execute();
$visitorData = $statementVisitors->fetch(PDO::FETCH_ASSOC);

// Daily Sales data
$sqlSales = "SELECT DAYOFWEEK(p.order_datetime) AS dayOfWeek, SUM(p.amountpayed) AS dailySales 
             FROM tblpayment p 
             GROUP BY dayOfWeek";
$statementSales = $pdo->prepare($sqlSales);
$statementSales->execute();
$salesData = $statementSales->fetchAll(PDO::FETCH_ASSOC);

// Order task statuses
$sqlOrderTasks = "SELECT status, COUNT(*) AS order_datetime FROM tblorderitem GROUP BY status";
$statementOrderTasks = $pdo->prepare($sqlOrderTasks);
$statementOrderTasks->execute();
$orderTasksData = $statementOrderTasks->fetchAll(PDO::FETCH_ASSOC);

// Monthly Sales data
$sqlMonthlySales = "SELECT DATE_FORMAT(order_datetime, '%Y-%m') AS saleMonth, SUM(amountpayed) AS monthlySales FROM tblpayment GROUP BY DATE_FORMAT(order_datetime, '%Y-%m')";
$statementMonthlySales = $pdo->prepare($sqlMonthlySales);
$statementMonthlySales->execute();
$monthlySalesData = $statementMonthlySales->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="./Dashboard/dashboard.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F5F5DC;
        }

        .container {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
        }

        .chart-container {
            width: 30%;
            /* width */
            margin: auto;
            /* margin sa chart */
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            margin-top: 3%;
        }

        .chart-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <div class="sidebar">
            <h1>Coffee Shop</h1>
            <ul>
                <li style="background-color: var(--primary-color);"><a href="dashboard.php">Home</a></li>
                <li><a href="coffeeshop_info.php">CoffeeShop</a></li>
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
            <h2>Coffee Shop Metrics Dashboard
                <?php echo " (" . $_SESSION['username'] . " the " . $_SESSION['position'] . ") " ?>
            </h2>
            <div>
                <div class="container">
                    <div class="chart-container">
                        <h2 class="chart-title">Total Number of Orders</h2>
                        <canvas id="totalOrdersChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2 class="chart-title">Total Visitors</h2>
                        <canvas id="totalVisitorsChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2 class="chart-title">Daily Sales</h2>
                        <canvas id="dailySalesChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2 class="chart-title">Monthly Sales</h2>
                        <canvas id="monthlySalesChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2 class="chart-title">Order Task Status</h2>
                        <canvas id="orderTasksChart"></canvas>
                    </div>
                    <br>
                </div>
                <br><br>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctxOrders = document.getElementById('totalOrdersChart').getContext('2d');
        var totalOrders = <?php echo $orderData['order_ID']; ?>;
        var remainingPercentageOrders = 100 - totalOrders;

        var ordersChart = new Chart(ctxOrders, {
            type: 'bar',
            data: {
                labels: ['Orders'],
                datasets: [
                    {
                        label: 'Orders',
                        data: [totalOrders],
                        backgroundColor: '#007BFF', // Blue color for orders
                    },
                    {
                        label: 'Remaining',
                        data: [remainingPercentageOrders],
                        backgroundColor: '#E0E0E0', // Light gray for the remaining percentage baguhin na lang sa susunod
                    }
                ]
            },
            options: {
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                },
                title: {
                    display: true,
                    text: 'Total Orders',
                }
            }
        });

        var ctxVisitors = document.getElementById('totalVisitorsChart').getContext('2d');
        var totalVisitors = <?php echo $visitorData['customer_ID']; ?>;
        var remainingPercentage = 100 - totalVisitors;

        var visitorsChart = new Chart(ctxVisitors, {
            type: 'bar',
            data: {
                labels: ['Visitors'],
                datasets: [
                    {
                        label: 'Visitors',
                        data: [totalVisitors],
                        backgroundColor: '#4CAF50', // Green for visitors
                    },
                    {
                        label: 'Remaining',
                        data: [remainingPercentage],
                        backgroundColor: '#E0E0E0', // Light gray for the remaining percentage baguhin na lang sa susunod
                    }
                ]
            },
            options: {
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                },
                title: {
                    display: true,
                    text: 'Total Visitors',
                }
            }
        });

        var ctxSales = document.getElementById('dailySalesChart').getContext('2d');
        var salesChart = new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                datasets: [{
                    label: 'Daily Sales',
                    data: <?php echo json_encode(array_values(array_column($salesData, 'dailySales'))); ?>,
                    fill: false,
                    borderColor: 'rgba(33, 150, 243, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Daily Sales'
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Day of the Week'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Sales'
                        }
                    }
                }
            }
        });
        var ctxMonthlySales = document.getElementById('monthlySalesChart').getContext('2d');
        var monthlySalesData = <?php echo json_encode($monthlySalesData); ?>;

        var monthlySalesChart = new Chart(ctxMonthlySales, {
            type: 'line',
            data: {
                labels: monthlySalesData.map(item => item.saleMonth),
                datasets: [{
                    label: 'Monthly Sales',
                    data: monthlySalesData.map(item => item.monthlySales),
                    backgroundColor: 'rgba(75, 192, 192, 0.7)', // Teal color for a professional look sheesh
                    borderColor: 'rgba(75, 192, 192, 1)', // Darker teal for the border
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true
                    }
                },
                title: {
                    display: true,
                    text: 'Monthly Sales'
                }
            }
        });

        var ctxOrderTasks = document.getElementById('orderTasksChart').getContext('2d');
        var orderTasksData = <?php echo json_encode($orderTasksData); ?>;

        var backgroundColors = orderTasksData.map(item => {
            switch (item.status) {
                case 'ended':
                    return 'rgba(255, 0, 0, 0.7)'; // Red for ended status
                case 'completed':
                    return 'rgba(0, 255, 0, 0.7)'; // Green for completed status
                case 'active':
                    return 'rgba(0, 0, 255, 0.7)'; // Blue for active status
                default:
                    return 'rgba(54, 162, 235, 0.7)';
            }
        });

        var orderTasksChart = new Chart(ctxOrderTasks, {
            type: 'pie',
            data: {
                labels: orderTasksData.map(item => item.status),
                datasets: [{
                    label: 'Order Task Status',
                    data: orderTasksData.map(item => item.order_datetime),
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(color => color.replace(/[^,]+(?=\))/, '1')),
                    borderWidth: 1
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Order Task Status'
                }
            }
        });
    </script>




</body>

</html>