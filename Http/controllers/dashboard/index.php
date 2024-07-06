<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

// Fetch data for the graphs
// Total number of orders
$sqlOrders = "SELECT COUNT(*) AS order_ID FROM tblorderitem";
$orderData = $db->query($sqlOrders)->find();

// Total visitors
$sqlVisitors = "SELECT COUNT(*) AS customer_ID FROM tblemployees WHERE position LIKE 'guest'; ";
$visitorData = $db->query($sqlVisitors)->find();

// Total admin
$sqlAdmin = "SELECT COUNT(*) AS customer_ID FROM tblemployees WHERE position LIKE 'admin'; ";
$adminData = $db->query($sqlAdmin)->find();

// Daily Sales data
$sqlSales = "SELECT DAYOFWEEK(p.order_datetime) AS dayOfWeek, SUM(p.amountpayed) AS dailySales 
             FROM tblpayment p 
             GROUP BY dayOfWeek";
$salesData = $db->query($sqlSales)->get();

// Order task statuses
$sqlOrderTasks = "SELECT status, COUNT(*) AS order_datetime FROM tblorderitem GROUP BY status";
$orderTasksData = $db->query($sqlOrderTasks)->get();

// Monthly Sales data
$sqlMonthlySales = "SELECT DATE_FORMAT(order_datetime, '%Y-%m') AS saleMonth, SUM(amountpayed) AS monthlySales FROM tblpayment GROUP BY DATE_FORMAT(order_datetime, '%Y-%m')";
$monthlySalesData = $db->query($sqlMonthlySales)->get();

view('dashboard/index.view.php', [
    'orderData' => $orderData,
    'visitorData' => $visitorData,
    'adminData' => $adminData,
    'salesData' => $salesData,
    'orderTasksData' => $orderTasksData,
    'monthlySalesData' => $monthlySalesData,
]);
