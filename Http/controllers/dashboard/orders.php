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


// complete order button 
if (isset($_POST['finish_order'])) {
    $orderitemID = $_POST['finish_order'];
    $productID = $_POST['product_id'];

    $itemDeductSql = "SELECT * FROM tblproducts_inventory WHERE products_id = :productid";
    $itemDeductStmt = $pdo->prepare($itemDeductSql);
    $itemDeductStmt->bindParam(':productid', $productID, PDO::PARAM_INT);
    $itemDeductStmt->execute();
    $itemDeduct = $itemDeductStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($itemDeduct as $deduct) {
        //getquantity of the order
        $deductMultiSql = "SELECT * FROM tblorderitem WHERE orderitem_id = :orderitemid";
        $deductMultiStmt = $pdo->prepare($deductMultiSql);
        $deductMultiStmt->bindParam(':orderitemid', $orderitemID, PDO::PARAM_INT);
        $deductMultiStmt->execute();
        $deductMulti = $deductMultiStmt->fetch(PDO::FETCH_ASSOC);

        // Deduct the quantity from the inventory
        $sqlDeduct = "UPDATE tblinventory SET quantity = quantity - (:quantity * :quantityOrder) WHERE inventory_id = :inventoryID";
        $statementDeduct = $pdo->prepare($sqlDeduct);
        $statementDeduct->bindParam(':quantity', $deduct['quantity'], PDO::PARAM_INT);
        $statementDeduct->bindParam(':quantityOrder', $deductMulti['quantity'], PDO::PARAM_INT);
        $statementDeduct->bindParam(':inventoryID', $deduct['inventory_id']);
        $statementDeduct->execute();

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
        header('Location: /admin_dashboard/orders'); // Use Location: to specify the redirect location
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
        header('Location: /admin_dashboard/orders'); // Use Location: to specify the redirect location
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
        header('Location: /admin_dashboard/orders'); // Use Location: to specify the redirect location
        exit(); // Exit to prevent further execution
    } else {
        // Handle the update errors
        echo "Error: " . $conn->error;
    }
}

view('dashboard/orders.view.php');
