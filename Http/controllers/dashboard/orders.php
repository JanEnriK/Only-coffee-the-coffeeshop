<?php
include "connect.php";

//for pdo
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

//fetch data from tblproducts
$sql = "SELECT * FROM tblproducts";
$statement = $pdo->prepare($sql);
$statement->execute();
$productsData = $statement->fetchAll(PDO::FETCH_ASSOC);

//cancel past orders in the active that is not today
function cancelPastOrders($pdo)
{
    $sqlToCancel = "SELECT * FROM tblorderitem WHERE status = 'active'";
    $statementToCancel = $pdo->prepare($sqlToCancel);
    $statementToCancel->execute();
    $cancelList = $statementToCancel->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cancelList as $order) {
        // Convert the datetime of the order to a date
        $OrderDate = date('Y-m-d', strtotime($order['date_time']));
        // Get today's date
        date_default_timezone_set('Asia/Manila');
        $today = date('Y-m-d');
        if ($OrderDate != $today) {
            // Prepare the UPDATE statement
            $sqlUpdate = "UPDATE tblorderitem SET status = 'ended' WHERE orderitem_id = :orderitemid";
            $statementUpdate = $pdo->prepare($sqlUpdate);
            $statementUpdate->bindParam(':orderitemid', $order['orderitem_id']);
            $statementUpdate->execute();
            error_log("an item is canceled");
        }
    }
}
cancelPastOrders($pdo);

function removeZeroQuantity($pdo)
{
    $sqlInventoryItems = "SELECT * FROM tblinventoryitems";
    $statementInventoryItems = $pdo->prepare($sqlInventoryItems);
    $statementInventoryItems->execute();
    $inventoryitemsData = $statementInventoryItems->fetchAll(PDO::FETCH_ASSOC);

    foreach ($inventoryitemsData as $data) {
        if ($data['quantity'] <= 0) {
            $deleteSql = "DELETE FROM tblinventoryitems WHERE tblinventoryitems_id = :inventory_id";
            $deleteStatement = $pdo->prepare($deleteSql);
            $deleteStatement->bindParam(':inventory_id', $data['tblinventoryitems_id']);
            $deleteStatement->execute();
        }
    }
}
removeZeroQuantity($pdo);


//remove expired items
function removeExpired($pdo)
{
    $sqlInventoryItems = "SELECT * FROM tblinventoryitems";
    $statementInventoryItems = $pdo->prepare($sqlInventoryItems);
    $statementInventoryItems->execute();
    $inventoryitemsData = $statementInventoryItems->fetchAll(PDO::FETCH_ASSOC);

    $today = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $today = $today->format('Y-m-d');

    foreach ($inventoryitemsData as $data) {
        $expirationDate = $data['expiration_date'];
        if ($expirationDate <= $today) {
            // The item is expired, delete it
            $deleteSql = "DELETE FROM tblinventoryitems WHERE tblinventoryitems_id = :inventory_id AND expiration_date = :expiration_date";
            $deleteStatement = $pdo->prepare($deleteSql);
            $deleteStatement->bindParam(':inventory_id', $data['tblinventoryitems_id']);
            $deleteStatement->bindParam(':expiration_date', $data['expiration_date']);
            $deleteStatement->execute();

            $sqlInventory = "SELECT inventory_item, unit FROM tblinventory WHERE inventory_id = :inventory_id";
            $statementInventory = $pdo->prepare($sqlInventory);
            $statementInventory->bindParam(':inventory_id', $data['inventory_id']);
            $statementInventory->execute();
            $inventoryData = $statementInventory->fetch(PDO::FETCH_ASSOC);

            $reason = "auto deduct " . $data['quantity'] . " for expired " . $inventoryData['inventory_item'];
            $recordType = "Expiration Auto Deduct";
            $quantityNegative = '-' . $data['quantity'];

            $sqlInsertReport = "INSERT INTO tblinventoryreport (inventory_item, inventory_id, quantity, unit, record_type, reason) VALUES (:inventoryItem, :inventory_id, :quantity, :unit, :record_type, :reason)";
            $statementInsertReport = $pdo->prepare($sqlInsertReport);
            $statementInsertReport->bindParam(':inventoryItem',  $inventoryData['inventory_item']);
            $statementInsertReport->bindParam(':inventory_id', $data['inventory_id']);
            $statementInsertReport->bindParam(':quantity', $quantityNegative);
            $statementInsertReport->bindParam(':unit', $inventoryData['unit']);
            $statementInsertReport->bindParam(':reason', $reason);
            $statementInsertReport->bindParam(':record_type', $recordType);
            $statementInsertReport->execute();
        }
    }
}
removeExpired($pdo);


//function for recalculating inventory quantity
function recalculateInventoryQuantity($pdo)
{
    // Fetch data from tblinventory
    $sql = "SELECT * FROM tblinventory";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $inventoryData = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($inventoryData as $inventory) {
        $inventoryQuantity = 0;

        $sqlInventoryItems = "SELECT * FROM tblinventoryitems WHERE inventory_id = :inventory_id";
        $statementInventoryItems = $pdo->prepare($sqlInventoryItems);
        $statementInventoryItems->bindParam(':inventory_id', $inventory['inventory_id']);
        $statementInventoryItems->execute();
        $inventoryitemsData = $statementInventoryItems->fetchAll(PDO::FETCH_ASSOC);

        foreach ($inventoryitemsData as $quantity) {
            $inventoryQuantity = $inventoryQuantity + $quantity['quantity'];
        }

        $sqlChangeQuantity = "UPDATE tblinventory SET quantity = :inventoryQuantity WHERE inventory_id = :inventory_id";
        $changeQuantity = $pdo->prepare($sqlChangeQuantity);
        $changeQuantity->bindParam(':inventoryQuantity', $inventoryQuantity);
        $changeQuantity->bindParam(':inventory_id', $inventory['inventory_id']);
        $changeQuantity->execute();
    }
}

recalculateInventoryQuantity($pdo);

function recalculateStatus($pdo, $productsData)
{
    calculateSKU($pdo, $productsData);

    // Fetch data from tblinventory
    $sql = "SELECT * FROM tblinventory";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $inventoryData = $statement->fetchAll(PDO::FETCH_ASSOC);

    updateInventoryStatus($pdo, $inventoryData);

    foreach ($productsData as $productRow) {
        $sql = "SELECT PI.*, I.*,P.*, I.quantity as inventoryQuantity
        FROM
        tblproducts_inventory PI
        JOIN
        tblproducts P ON PI.products_id = P.product_Id
        JOIN
        tblinventory I ON PI.inventory_id = I.inventory_id
        WHERE
        PI.products_id = :productId
        ";
        $checkInventory = $pdo->prepare($sql);
        $checkInventory->bindParam(':productId', $productRow['product_id']);
        $checkInventory->execute();
        $productInventoryData = $checkInventory->fetchAll(PDO::FETCH_ASSOC);

        if ($checkInventory->rowCount() === 0) {
            $sqlChangeNull = "UPDATE tblproducts SET status = NULL WHERE tblproducts.product_id = :productId";
            $changeNull = $pdo->prepare($sqlChangeNull);
            $changeNull->bindParam(':productId', $productRow['product_id']);
            $changeNull->execute();
        } else {
            $hasLowStock = false;

            foreach ($productInventoryData as $setIngredients) {
                if ($setIngredients['inventoryQuantity'] <= $setIngredients['reorder_point']) {
                    $hasLowStock = true;
                }
            }
            if ($productRow['SKU'] > 0 && $hasLowStock === false) {
                $sqlChangeAvailable = "UPDATE tblproducts SET status = 'Available' WHERE tblproducts.product_id = :productId;";
                $changeAvailable = $pdo->prepare($sqlChangeAvailable);
                $changeAvailable->bindParam(':productId', $productRow['product_id']);
                $changeAvailable->execute();
            } else {
                $sqlChangeNotAvailable = "UPDATE tblproducts SET status = 'Not Available' WHERE tblproducts.product_id = :productId;";
                $changeNotAvailable = $pdo->prepare($sqlChangeNotAvailable);
                $changeNotAvailable->bindParam(':productId', $productRow['product_id']);
                $changeNotAvailable->execute();
            }
        }
    }
}
function calculateSKU($pdo, $productsData)
{
    foreach ($productsData as $products) {
        $sql = "SELECT I.quantity as inventoryQuantity, PI.quantity as ingredientQuantity
        FROM
        tblproducts_inventory PI
        JOIN
        tblproducts P ON PI.products_id = P.product_Id
        JOIN
        tblinventory I ON PI.inventory_id = I.inventory_id
        WHERE
        PI.products_id = :productId
        ";
        $checkInventory = $pdo->prepare($sql);
        $checkInventory->bindParam(':productId', $products['product_id']);
        $checkInventory->execute();
        $productInventoryData = $checkInventory->fetchAll(PDO::FETCH_ASSOC);

        // Initialize $productSKU to a very high number to ensure any valid division will be lower
        $productSKU = PHP_INT_MAX;

        foreach ($productInventoryData as $ingredients) {
            // Ensure both keys exist and avoid division by zero
            if (
                isset($ingredients['inventoryQuantity'], $ingredients['ingredientQuantity']) &&
                $ingredients['ingredientQuantity'] != 0
            ) {
                $divisionResult = floor($ingredients['inventoryQuantity'] / $ingredients['ingredientQuantity']);
                // Update $productSKU if the current division result is lower than the previous minimum
                if ($divisionResult < $productSKU) {
                    $productSKU = $divisionResult;
                }
            }
        }

        // If no valid division was found (all were zero), set $productSKU to a special value or leave it unchanged
        // For example, setting it to 0 or another placeholder value
        if ($productSKU == PHP_INT_MAX) {
            $productSKU = 0; // Or any other value indicating no valid SKU could be calculated
        }

        $sqlUpdateSKU = "UPDATE tblproducts SET SKU = :productSKU WHERE product_id = :productId";
        $updateSKU = $pdo->prepare($sqlUpdateSKU);
        $updateSKU->bindParam(':productSKU', $productSKU);
        $updateSKU->bindParam(':productId', $products['product_id']);
        $updateSKU->execute();
    }
}

// Fetch data from tblinventory
$sql = "SELECT * FROM tblinventory";
$statement = $pdo->prepare($sql);
$statement->execute();
$inventoryData = $statement->fetchAll(PDO::FETCH_ASSOC);

function updateInventoryStatus($pdo, $inventoryData)
{
    foreach ($inventoryData as $inventory) {
        if ($inventory['quantity'] > $inventory['reorder_point']) {
            $sqlUpdateStatus = "UPDATE tblinventory SET status = 'In Stock' WHERE inventory_id = :inventoryId";
            $updateStatus = $pdo->prepare($sqlUpdateStatus);
            $updateStatus->bindParam(':inventoryId', $inventory['inventory_id']);
            $updateStatus->execute();
        } else {
            $sqlUpdateStatus = "UPDATE tblinventory SET status = 'Low Stock' WHERE inventory_id = :inventoryId";
            $updateStatus = $pdo->prepare($sqlUpdateStatus);
            $updateStatus->bindParam(':inventoryId', $inventory['inventory_id']);
            $updateStatus->execute();
        }
    }
}


// // complete order button 
// if (isset($_POST['finish_order'])) {
//     $orderitemID = $_POST['finish_order'];
//     $productID = $_POST['product_id'];

//     $itemDeductSql = "SELECT * FROM tblproducts_inventory WHERE products_id = :productid";
//     $itemDeductStmt = $pdo->prepare($itemDeductSql);
//     $itemDeductStmt->bindParam(':productid', $productID, PDO::PARAM_INT);
//     $itemDeductStmt->execute();
//     $itemDeduct = $itemDeductStmt->fetchAll(PDO::FETCH_ASSOC);

//     foreach ($itemDeduct as $deduct) {
//         //getquantity of the order
//         $deductMultiSql = "SELECT * FROM tblorderitem WHERE orderitem_id = :orderitemid";
//         $deductMultiStmt = $pdo->prepare($deductMultiSql);
//         $deductMultiStmt->bindParam(':orderitemid', $orderitemID, PDO::PARAM_INT);
//         $deductMultiStmt->execute();
//         $deductMulti = $deductMultiStmt->fetch(PDO::FETCH_ASSOC);

//         // Deduct the quantity from the inventory
//         $sqlDeduct = "UPDATE tblinventory SET quantity = quantity - (:quantity * :quantityOrder) WHERE inventory_id = :inventoryID";
//         $statementDeduct = $pdo->prepare($sqlDeduct);
//         $statementDeduct->bindParam(':quantity', $deduct['quantity'], PDO::PARAM_INT);
//         $statementDeduct->bindParam(':quantityOrder', $deductMulti['quantity'], PDO::PARAM_INT);
//         $statementDeduct->bindParam(':inventoryID', $deduct['inventory_id']);
//         $statementDeduct->execute();

//         //recalculate product sku and availability
//         recalculateStatus($pdo, $productsData);

//         //fetch product details
//         $getProductSql = "SELECT * FROM tblproducts WHERE product_id = :productid";
//         $getProductStmt = $pdo->prepare($getProductSql);
//         $getProductStmt->bindParam(':productid', $deduct['products_id'], PDO::PARAM_INT);
//         $getProductStmt->execute();
//         $fetchProduct = $getProductStmt->fetch(PDO::FETCH_ASSOC);

//         //fetch inventory details
//         $getInventorySql = "SELECT * FROM tblinventory WHERE inventory_id = :inventoryid";
//         $getInventoryStmt = $pdo->prepare($getInventorySql);
//         $getInventoryStmt->bindParam(':inventoryid', $deduct['inventory_id'], PDO::PARAM_INT);
//         $getInventoryStmt->execute();
//         $fetchInventory = $getInventoryStmt->fetch(PDO::FETCH_ASSOC);

//         // Convert the quantity to a string and prepend "-", set the reason for inventory deduct
//         $quantityString = "-" . $deduct['quantity'] * $deductMulti['quantity'];
//         $reason = "inventory deduct for preparing " . $deductMulti['quantity'] . " " . $fetchProduct['product_name'];

//         // Insert data into tblInventoryReports"
//         $recordType = "Product Preparation Deduct";
//         $sqlInsertReport = "INSERT INTO tblinventoryreport (inventory_item, inventory_id, quantity, unit, record_type, reason) VALUES (:inventoryItem, :itemID, :quantityString, :unit, :record_type, :reason)";
//         $statementInsertReport = $pdo->prepare($sqlInsertReport);
//         $statementInsertReport->bindParam(':inventoryItem', $fetchInventory['inventory_item']);
//         $statementInsertReport->bindParam(':itemID', $deduct['inventory_id']);
//         $statementInsertReport->bindParam(':quantityString', $quantityString); // Bind the modified quantity string
//         $statementInsertReport->bindParam(':unit', $fetchInventory['unit']); // Bind the unit directly
//         $statementInsertReport->bindParam(':record_type', $recordType);
//         $statementInsertReport->bindParam(':reason', $reason);
//         $statementInsertReport->execute();
//     }

//     // Construct the SQL query for updating the record
//     $update_sql = "UPDATE tblorderitem
//                    SET status = 'completed'
//                    WHERE tblorderitem.orderitem_id= $orderitemID";
//     if ($conn->query($update_sql) === TRUE) {

//         //add user log [complete an order]
//         $DateTime = new DateTime();
//         $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
//         $DateTime->setTimeZone($philippinesTimeZone);

//         $currentDateTime = $DateTime->format('Y-m-d H:i:s');
//         $employeeid = $_SESSION['employeeID'];
//         $loginfo = $_SESSION['username'] . ' has completed an order.';

//         try {
//             $sqlLogAdd = "INSERT INTO tbluserlogs (log_datetime, loginfo, employeeid) VALUES (:currentDateTime, :loginfo, :employeeid)";
//             $statementLogAdd = $pdo->prepare($sqlLogAdd);
//             $statementLogAdd->bindParam(':loginfo', $loginfo);
//             $statementLogAdd->bindParam(':employeeid', $employeeid);
//             $statementLogAdd->bindParam(':currentDateTime', $currentDateTime);
//             $statementLogAdd->execute();
//         } catch (PDOException $e) {
//             // Handle the exception/error
//             echo "Error: " . $e->getMessage();
//         }

//         // Handle a successful update (you can redirect or show a success message)
//         header('Location: /admin_dashboard/orders'); // Use Location: to specify the redirect location
//         exit(); // Exit to prevent further execution
//     } else {
//         // Handle the update errors
//         echo "Error: " . $conn->error;
//     }
// }

// // ended order button 
// if (isset($_POST['ended_order'])) {
//     $orderitemID = $_POST['ended_order'];

//     // Construct the SQL query for updating the record
//     $endorder_sql = "UPDATE tblorderitem
//                    SET status = 'ended'
//                    WHERE tblorderitem.orderitem_id = $orderitemID";

//     if ($conn->query($endorder_sql) === TRUE) {

//         //add user log [archived an order]
//         $DateTime = new DateTime();
//         $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
//         $DateTime->setTimeZone($philippinesTimeZone);

//         $currentDateTime = $DateTime->format('Y-m-d H:i:s');
//         $employeeid = $_SESSION['employeeID'];
//         $loginfo = $_SESSION['username'] . ' has archived an order.';

//         try {
//             $sqlLogAdd = "INSERT INTO tbluserlogs (log_datetime, loginfo, employeeid) VALUES (:currentDateTime, :loginfo, :employeeid)";
//             $statementLogAdd = $pdo->prepare($sqlLogAdd);
//             $statementLogAdd->bindParam(':loginfo', $loginfo);
//             $statementLogAdd->bindParam(':employeeid', $employeeid);
//             $statementLogAdd->bindParam(':currentDateTime', $currentDateTime);
//             $statementLogAdd->execute();
//         } catch (PDOException $e) {
//             // Handle the exception/error
//             echo "Error: " . $e->getMessage();
//         }

//         // Handle a successful update (you can redirect or show a success message)
//         header('Location: /admin_dashboard/orders'); // Use Location: to specify the redirect location
//         exit(); // Exit to prevent further execution
//     } else {
//         // Handle the update errors
//         echo "Error: " . $conn->error;
//     }
// }


// // Unarchive button 
// if (isset($_POST['unarchive_order'])) {
//     $orderitemID = $_POST['unarchive_order'];

//     // Construct the SQL query for updating the record
//     $update_sql = "UPDATE tblorderitem
//                    SET status = 'active'
//                    WHERE tblorderitem.orderitem_id = $orderitemID";

//     if ($conn->query($update_sql) === TRUE) {
//         //add user log [unarchived an order]
//         $DateTime = new DateTime();
//         $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
//         $DateTime->setTimeZone($philippinesTimeZone);

//         $currentDateTime = $DateTime->format('Y-m-d H:i:s');
//         $employeeid = $_SESSION['employeeID'];
//         $loginfo = $_SESSION['username'] . ' has unarchived an order.';

//         try {
//             $sqlLogAdd = "INSERT INTO tbluserlogs (log_datetime, loginfo, employeeid) VALUES (:currentDateTime, :loginfo, :employeeid)";
//             $statementLogAdd = $pdo->prepare($sqlLogAdd);
//             $statementLogAdd->bindParam(':loginfo', $loginfo);
//             $statementLogAdd->bindParam(':employeeid', $employeeid);
//             $statementLogAdd->bindParam(':currentDateTime', $currentDateTime);
//             $statementLogAdd->execute();
//         } catch (PDOException $e) {
//             // Handle the exception/error
//             echo "Error: " . $e->getMessage();
//         }
//         // Handle a successful update (you can redirect or show a success message)
//         header('Location: /admin_dashboard/orders'); // Use Location: to specify the redirect location
//         exit(); // Exit to prevent further execution
//     } else {
//         // Handle the update errors
//         echo "Error: " . $conn->error;
//     }
// }



if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Use a regex pattern that captures 'decline' or 'finish' without the trailing underscore
    preg_match('/(decline|finish|reactivate)_(\d+)_([\d\-:\s]+)/', $action, $matches);

    // Check if the expected keys exist in the $matches array
    if (isset($matches[1], $matches[2], $matches[3])) {
        $actionProcess = $matches[1]; // 'decline' or 'finish'
        $orderNumber = $matches[2]; // Order number
        $orderDate = $matches[3]; // Order date
        $orderDate = strtotime($orderDate);
        $orderDate = date('Y-m-d', $orderDate);
    } else {
        // Handle the case where the regex did not match as expected
        error_log("Regex did not match expected pattern.");
    }
    error_log("actionProcess: $actionProcess");
    error_log("orderNumber: $orderNumber");
    error_log("orderDate: $orderDate");


    $orderItemsSql = "SELECT * FROM tblorderitem WHERE orderid = :orderNumber AND DATE(date_time) = :orderDate";
    $orderItemsStmt = $pdo->prepare($orderItemsSql);
    $orderItemsStmt->bindParam(':orderNumber', $orderNumber, PDO::PARAM_INT);
    $orderItemsStmt->bindParam(':orderDate', $orderDate);
    $orderItemsStmt->execute();
    $orderItems = $orderItemsStmt->fetchAll(PDO::FETCH_ASSOC);
    //finish the order
    if ($actionProcess == 'finish') {
        foreach ($orderItems as $items) {
            $itemDeductSql = "SELECT * FROM tblproducts_inventory WHERE products_id = :productid";
            $itemDeductStmt = $pdo->prepare($itemDeductSql);
            $itemDeductStmt->bindParam(':productid', $items['productid'], PDO::PARAM_INT);
            $itemDeductStmt->execute();
            $itemDeduct = $itemDeductStmt->fetchAll(PDO::FETCH_ASSOC);

            //remove expired and zero records
            removeExpired($pdo);
            removeZeroQuantity($pdo);
            recalculateInventoryQuantity($pdo);

            foreach ($itemDeduct as $deduct) {
                //getquantity of the order
                $deductMultiSql = "SELECT * FROM tblorderitem WHERE orderitem_id = :orderitemid";
                $deductMultiStmt = $pdo->prepare($deductMultiSql);
                $deductMultiStmt->bindParam(':orderitemid', $items['orderitem_id'], PDO::PARAM_INT);
                $deductMultiStmt->execute();
                $deductMulti = $deductMultiStmt->fetch(PDO::FETCH_ASSOC);

                //deduct quantity on tblinventoryitems prioritizing items that are going to expire soon
                // Fetch inventoryitems with expiration_date ascending
                $sqlInventoryItems = "SELECT * FROM tblinventoryitems WHERE inventory_id = :inventory_id ORDER BY expiration_date ASC, quantity ASC";
                $statementInventoryItems = $pdo->prepare($sqlInventoryItems);
                $statementInventoryItems->bindParam(':inventory_id', $deduct['inventory_id']);
                $statementInventoryItems->execute();
                $inventoryitemsData = $statementInventoryItems->fetchAll(PDO::FETCH_ASSOC);

                $quantity = $deduct['quantity'] * $deductMulti['quantity'];

                foreach ($inventoryitemsData as $data) {
                    if ($quantity <= 0) {
                        break; // Exit the loop if there's nothing left to deduct
                    }

                    // Calculate the deduction amount for the current item
                    $deductionAmount = min($data['quantity'], $quantity);

                    // Update the quantity for the current inventory item
                    $sqlDeduct = "UPDATE tblinventoryitems SET quantity = quantity - :deductionAmount WHERE tblinventoryitems_id = :inventoryID";
                    $statementDeduct = $pdo->prepare($sqlDeduct);
                    $statementDeduct->bindParam(':deductionAmount', $deductionAmount, PDO::PARAM_INT);
                    $statementDeduct->bindParam(':inventoryID', $data['tblinventoryitems_id']);
                    $statementDeduct->execute();
                    // Update the remaining quantity to deduct
                    $quantity -= $deductionAmount;
                }



                // Deduct the quantity from the inventory
                // $sqlDeduct = "UPDATE tblinventory SET quantity = quantity - (:quantity * :quantityOrder) WHERE inventory_id = :inventoryID";
                // $statementDeduct = $pdo->prepare($sqlDeduct);
                // $statementDeduct->bindParam(':quantity', $deduct['quantity'], PDO::PARAM_INT);
                // $statementDeduct->bindParam(':quantityOrder', $deductMulti['quantity'], PDO::PARAM_INT);
                // $statementDeduct->bindParam(':inventoryID', $deduct['inventory_id']);
                // $statementDeduct->execute();

                //INSERT HERE THE FUNCTION THAT RE-COMPUTES TBLINVENTORY QUANTITY BASED ON TBLINVENTORYITEMS
                recalculateInventoryQuantity($pdo);

                //recalculate product sku and availability
                recalculateStatus($pdo, $productsData);

                //fetch product details
                $getProductSql = "SELECT * FROM tblproducts WHERE product_id = :productid";
                $getProductStmt = $pdo->prepare($getProductSql);
                $getProductStmt->bindParam(':productid', $deduct['products_id'], PDO::PARAM_INT);
                $getProductStmt->execute();
                $fetchProduct = $getProductStmt->fetch(PDO::FETCH_ASSOC);

                //fetch inventory details
                $getInventorySql = "SELECT * FROM tblinventory WHERE inventory_id = :inventoryid";
                $getInventoryStmt = $pdo->prepare($getInventorySql);
                $getInventoryStmt->bindParam(':inventoryid', $deduct['inventory_id'], PDO::PARAM_INT);
                $getInventoryStmt->execute();
                $fetchInventory = $getInventoryStmt->fetch(PDO::FETCH_ASSOC);

                // Convert the quantity to a string and prepend "-", set the reason for inventory deduct
                $quantityString = "-" . $deduct['quantity'] * $deductMulti['quantity'];
                $reason = "inventory deduct for preparing " . $deductMulti['quantity'] . " " . $fetchProduct['product_name'];

                // Insert data into tblInventoryReports"
                $recordType = "Product Preparation Deduct";
                $sqlInsertReport = "INSERT INTO tblinventoryreport (inventory_item, inventory_id, quantity, unit, record_type, reason) VALUES (:inventoryItem, :itemID, :quantityString, :unit, :record_type, :reason)";
                $statementInsertReport = $pdo->prepare($sqlInsertReport);
                $statementInsertReport->bindParam(':inventoryItem', $fetchInventory['inventory_item']);
                $statementInsertReport->bindParam(':itemID', $deduct['inventory_id']);
                $statementInsertReport->bindParam(':quantityString', $quantityString); // Bind the modified quantity string
                $statementInsertReport->bindParam(':unit', $fetchInventory['unit']); // Bind the unit directly
                $statementInsertReport->bindParam(':record_type', $recordType);
                $statementInsertReport->bindParam(':reason', $reason);
                $statementInsertReport->execute();
            }

            $sqlEnd = "UPDATE tblorderitem SET status = 'completed'  WHERE orderitem_id = :orderitem_id";
            $statementEnd = $pdo->prepare($sqlEnd);
            $statementEnd->bindParam(':orderitem_id', $items['orderitem_id']);
            $statementEnd->execute();
        }
        header('Location: /admin_dashboard/orders');
        exit();
    } else { // cancel orderitems inside the order
        foreach ($orderItems as $items) {
            $sqlEnd = "UPDATE tblorderitem SET status = 'ended'  WHERE orderitem_id = :orderitem_id";
            $statementEnd = $pdo->prepare($sqlEnd);
            $statementEnd->bindParam(':orderitem_id', $items['orderitem_id']);
            $statementEnd->execute();
        }
        header('Location: /admin_dashboard/orders');
        exit();
    }
} else if (isset($_POST['reactivate'])) { //reactivate canceled orders
    $reactivate = $_POST['reactivate'];

    preg_match('/finish|reactivate_(\d+)_([\d\-:\s]+)/', $reactivate, $matches);
    $orderNumber = $matches[1];
    $orderDate = $matches[2];
    $orderDate = strtotime($orderDate);
    $orderDate = date('Y-m-d', $orderDate);

    $orderItemsSql = "SELECT * FROM tblorderitem WHERE orderid = :orderNumber AND DATE(date_time) = :orderDate";
    $orderItemsStmt = $pdo->prepare($orderItemsSql);
    $orderItemsStmt->bindParam(':orderNumber', $orderNumber, PDO::PARAM_INT);
    $orderItemsStmt->bindParam(':orderDate', $orderDate);
    $orderItemsStmt->execute();
    $orderItems = $orderItemsStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orderItems as $items) {
        $sqlEnd = "UPDATE tblorderitem SET status = 'active'  WHERE orderitem_id = :orderitem_id";
        $statementEnd = $pdo->prepare($sqlEnd);
        $statementEnd->bindParam(':orderitem_id', $items['orderitem_id']);
        $statementEnd->execute();
    }
    header('Location: /admin_dashboard/orders');
    exit();
}

view('dashboard/orders.view.php');
