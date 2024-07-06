<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');
//this function cancels orders placed online that wasn't paid from old orders starting yesterday
function cancelOldOrders($db)
{
   $potentialCancelList = $db->query("SELECT * FROM tblorders WHERE order_status = 'notpayed' OR order_status = 'pending'")->get();
   foreach ($potentialCancelList as $order) {
      // Convert the datetime of the order to a date
      $OrderDate = date('Y-m-d', strtotime($order['order_datetime']));
      // Get today's date
      date_default_timezone_set('Asia/Manila');
      $today = date('Y-m-d');
      error_log("OrderDate: $OrderDate");
      error_log("Today date: $today");
      if ($OrderDate != $today) {
         // Prepare the UPDATE statement
         $db->query("UPDATE tblorders SET order_status = 'canceled' WHERE order_id =?", [$order['order_id']]);
      }
   }
}
cancelOldOrders($db);

$online_orders = $db->query("SELECT o.*, e.*, p.*, COUNT(o.order_number) AS order_number_count
FROM tblorders o
JOIN tblemployees e ON o.customer_id = e.employeeID
JOIN tblproducts p ON o.base_coffee_id = p.product_id
WHERE order_status = 'notpayed'
GROUP BY o.order_number;
")->get();

$pendingOnlineOrders = $db->query("SELECT o.*, e.*, p.*, COUNT(o.order_number) AS order_number_count
FROM tblorders o
JOIN tblemployees e ON o.customer_id = e.employeeID
JOIN tblproducts p ON o.base_coffee_id = p.product_id
WHERE order_status = 'pending'
GROUP BY o.order_number;
")->get();
// add WHERE position = 'guest' to fetch only guest 
//previous query SELECT DISTINCT * FROM tblorders JOIN tblemployees ON customer_id = employeeID WHERE 1 GROUP BY order_number;

$discount_codes = $db->query("SELECT * from tblpromo WHERE 1")->get();

// New query to fetch VAT percentage
$vatResult = $db->query("SELECT VAT as VAT from tblcoffeeshop WHERE 1")->find();

// Ensure to check if the result exists and convert the VAT percentage to float
if ($vatResult === null || empty($vatResult)) {
   // Handle the case where no VAT percentage is found
   // This could involve setting a default value or handling an error
   $vatPercentage = 0; // Defaulting to 0 as an example
} else {
   $vatPercentage = (float)$vatResult['VAT']; // Convert to float
}

view('pos/online_orders.view.php', [
   'online_orders' => $online_orders,
   'pendingOnlineOrders' => $pendingOnlineOrders,
   'discount_codes' => $discount_codes,
   'vatPercentage' => $vatPercentage
]);
