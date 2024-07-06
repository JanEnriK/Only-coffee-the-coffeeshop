<?php require "partials/head.php"; ?>
<?php require "partials/nav.php"; ?>
<?php
// Assuming you have a session variable set after a successful login
if (isset($_SESSION['loginSuccess']) && $_SESSION['loginSuccess'] === true) {
    echo '
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            window.onload = function() {
                swal("Login successful!", {
                    icon: "success",
                });
            };
        </script>
        ';
    // Reset the session variable to prevent the alert from showing up again on page reload
    $_SESSION['loginSuccess'] = false;
}
?>
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
        width: 40%;
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

<div class="dashboard">
    <div class="content">
        <h2>Coffee Shop Metrics Dashboard
            <?php echo " (" . $_SESSION['user']['email'] . " the " . $_SESSION['user']['position'] . ") " ?>
        </h2>
        <div>
            <div class="container">
                <div class="chart-container">
                    <h2 class="chart-title">Total Number of Orders</h2>
                    <canvas id="totalOrdersChart"></canvas>
                </div>
                <div class="chart-container">
                    <h2 class="chart-title">Total Accounts</h2>
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
    var totalOrders = <?= $orderData['order_ID'] ?>;
    var remainingPercentageOrders = 100 - totalOrders;

    var ordersChart = new Chart(ctxOrders, {
        type: 'bar',
        data: {
            labels: ['Orders'],
            datasets: [{
                    label: 'Orders',
                    data: [totalOrders],
                    backgroundColor: '#007BFF', // Blue color for orders
                },
                // {
                //     label: 'Remaining',
                //     data: [remainingPercentageOrders],
                //     backgroundColor: '#E0E0E0', // Light gray for the remaining percentage baguhin na lang sa susunod
                // }
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
    var totalVisitors = <?= $visitorData['customer_ID']; ?>;
    var totalAdmin = <?= $adminData['customer_ID']; ?>;

    var visitorsChart = new Chart(ctxVisitors, {
        type: 'bar',
        data: {
            labels: ['Accounts'],
            datasets: [{
                    label: 'Customers',
                    data: [totalVisitors],
                    backgroundColor: '#4CAF50', // Green for visitors
                },
                {
                    label: 'Admin',
                    data: [totalAdmin],
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
                data: <?= json_encode(array_values(array_column($salesData, 'dailySales'))); ?>,
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
    var monthlySalesData = <?= json_encode($monthlySalesData) ?>;

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
    var orderTasksData = <?= json_encode($orderTasksData) ?>;

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