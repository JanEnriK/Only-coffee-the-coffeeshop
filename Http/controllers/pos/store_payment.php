<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');
//process approve and decline from online payment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'approve') {
            $totalAmount = $_POST['totalAmount'];
            $customerId = $_POST['customerId'];
            $orderNumber = $_POST['orderNumber'];
            $paymentOnline = $_POST['inputReferenceNumber'];
            // Insert the payment details into the database (ONLINE)
            $db->query("INSERT INTO tblpayment(amountpayed, paymenttype, customerid, orderNumber, reference_no) VALUES(:total_amount, :payment_type, :customer_id, :order_number,:reference_no)", [
                'total_amount' => $totalAmount,
                'payment_type' => "online",
                'customer_id' => $customerId,
                'order_number' => $orderNumber,
                'reference_no' => $paymentOnline,
            ]);
            //update status of the order
            $db->query("UPDATE `tblorders` SET `order_status` = 'payed' WHERE order_number = ?  AND order_status = 'pending'", [$orderNumber]);

            //pass the order to order items for preparation
            date_default_timezone_set('Asia/Manila');
            $date = date("Y-m-d");  // Format the date correctly
            $orderedItems = $db->query(
                "SELECT * FROM tblorders 
                            JOIN tblproducts ON base_coffee_id = product_id 
                            WHERE order_status = 'payed' 
                            AND order_number = ? 
                            AND order_datetime LIKE ?",
                [$orderNumber, '%' . $date . '%']
            )->get();
            foreach ($orderedItems as $items) {
                $db->query("INSERT INTO tblorderitem(quantity, status, orderid, productid, customerid) VALUES(:quantity, :status, :orderid, :productid, :customerid)", [
                    'quantity' => $items['quantity'],
                    'status' => "active",
                    'orderid' => $orderNumber,
                    'productid' => $items['product_id'],
                    'customerid' => $customerId,
                ]);
            }
            $_SESSION['orderSubmited']['ordernumber'] = $orderNumber;
        } elseif ($_POST['action'] == 'decline') {
            $orderNumber = $_POST['orderNumber'];
            //update status of the order
            $db->query("UPDATE `tblorders` SET `order_status` = 'declined' WHERE order_number = ? AND order_status = 'pending'", [$orderNumber]);
            $_SESSION['orderDeclined']['ordernumber'] = $orderNumber;
        }
    } else {
        // Retrieve the form data for paying onsite
        $totalAmount = $_POST['totalAmount'];
        $customerId = $_POST['customerId'];
        $orderNumber = $_POST['orderNumber'];
        if (isset($_POST['amountPaid'])) {
            $paymentCash = $_POST['amountPaid'];
        } else {
            $paymentOnline = $_POST['referenceNumber'];
        }

        // Check which payment method was selected
        if (!empty($paymentCash)) {
            // Insert the payment details into the database (CASH)
            $db->query("INSERT INTO tblpayment(amountpayed, paymenttype, customerid, orderNumber) VALUES(:total_amount, :payment_type, :customer_id, :order_number)", [
                'total_amount' => $totalAmount,
                'payment_type' => "cash",
                'customer_id' => $customerId,
                'order_number' => $orderNumber,
            ]);
            //update status of the order
            $db->query("UPDATE `tblorders` SET `order_status` = 'payed' WHERE order_number = ?  AND order_status = 'notpayed'", [$orderNumber]);
            //pass the order to order items for preparation
            date_default_timezone_set('Asia/Manila');
            $date = date("Y-m-d");  // Format the date correctly
            $orderedItems = $db->query(
                "SELECT * FROM tblorders 
                            JOIN tblproducts ON base_coffee_id = product_id 
                            WHERE order_status = 'payed' 
                            AND order_number = ? 
                            AND order_datetime LIKE ?",
                [$orderNumber, '%' . $date . '%']
            )->get();
            foreach ($orderedItems as $items) {
                $db->query("INSERT INTO tblorderitem(quantity, status, orderid, productid,customerid) VALUES(:quantity, :status, :orderid, :productid,:customerid)", [
                    'quantity' => $items['quantity'],
                    'status' => "active",
                    'orderid' => $orderNumber,
                    'productid' => $items['product_id'],
                    'customerid' => $customerId,
                ]);
            }
        } elseif (!empty($paymentOnline)) {
            // Insert the payment details into the database (ONLINE)
            $db->query("INSERT INTO tblpayment(amountpayed, paymenttype, customerid, orderNumber, reference_no) VALUES(:total_amount, :payment_type, :customer_id, :order_number,:reference_no)", [
                'total_amount' => $totalAmount,
                'payment_type' => "online",
                'customer_id' => $customerId,
                'order_number' => $orderNumber,
                'reference_no' => $paymentOnline,
            ]);
            //update status of the order
            $db->query("UPDATE `tblorders` SET `order_status` = 'payed' WHERE order_number = ?  AND order_status = 'notpayed'", [$orderNumber]);

            //pass the order to order items for preparation
            date_default_timezone_set('Asia/Manila');
            $date = date("Y-m-d");  // Format the date correctly
            $orderedItems = $db->query(
                "SELECT * FROM tblorders 
                            JOIN tblproducts ON base_coffee_id = product_id 
                            WHERE order_status = 'payed' 
                            AND order_number = ? 
                            AND order_datetime LIKE ?",
                [$orderNumber, '%' . $date . '%']
            )->get();

            foreach ($orderedItems as $items) {
                $db->query("INSERT INTO tblorderitem(quantity, status, orderid, productid,customerid) VALUES(:quantity, :status, :orderid, :productid,:customerid)", [
                    'quantity' => $items['quantity'],
                    'status' => "active",
                    'orderid' => $orderNumber,
                    'productid' => $items['product_id'],
                    'customerid' => $customerId,
                ]);
            }
        } else {
            echo "No transaction detected";
        }

        // Assuming you want to store the last order number submitted
        $_SESSION['orderSubmited']['ordernumber'] = $orderNumber;
    }
}

// Redirect to a success page or back to the order details page
header('Location: /pos_frontend/online_orders');
exit;
