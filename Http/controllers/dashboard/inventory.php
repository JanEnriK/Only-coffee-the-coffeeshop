<?php
include "connect.php";
//fetch data for tblproducts
$sql = "SELECT * FROM tblproducts";
$statement = $pdo->prepare($sql);
$statement->execute();
$productsData = $statement->fetchAll(PDO::FETCH_ASSOC);

//remove zero quantity records
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
    error_log("date today: $today");

    foreach ($inventoryitemsData as $data) {
        $expirationDate = $data['expiration_date'];
        error_log("expiration: " . $expirationDate);
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

updateInventoryStatus($pdo, $inventoryData);

// Fetch data from tblinventory
$sql = "SELECT * FROM tblinventory";
$statement = $pdo->prepare($sql);
$statement->execute();
$inventoryData = $statement->fetchAll(PDO::FETCH_ASSOC);


// Fetch data from tblcategory_inventory
$sqlCategoryInventory = "SELECT * FROM tblcategory_inventory";
$categoryInventoryStatement = $pdo->prepare($sqlCategoryInventory);
$categoryInventoryStatement->execute();
$categoryInventoryData = $categoryInventoryStatement->fetchAll(PDO::FETCH_ASSOC);

//adding supply for a inventory
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_add_item'])) {
        // Retrieve form data
        $inventoryItem = $_POST['supply_item'];
        $inventoryID = $_POST['item_id_increase'];
        $quantity = $_POST['new_quantity'];
        $expiration = $_POST['new_expiration'];
        $reason = "added supply for " . $inventoryItem;

        // Fetch the unit from the tblinventory table
        $sqlFetchUnit = "SELECT unit FROM tblinventory WHERE inventory_id = :inventory_id";
        $statementFetchUnit = $pdo->prepare($sqlFetchUnit);
        $statementFetchUnit->bindParam(':inventory_id', $inventoryID);
        $statementFetchUnit->execute();
        $unitResult = $statementFetchUnit->fetch(PDO::FETCH_ASSOC);
        $unit = $unitResult['unit']; // Assuming 'unit' is the correct column name

        // Add the quantity to the inventory
        // $sqlAdd = "UPDATE tblinventory SET quantity = quantity + :quantity WHERE inventory_id = :inventoryID";
        // $statementAdd = $pdo->prepare($sqlAdd);
        // $statementAdd->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        // $statementAdd->bindParam(':inventoryID', $inventoryID);
        // $statementAdd->execute();

        //insert to tblinventoryitems
        $sqlInventoryItems = "INSERT INTO tblinventoryitems (quantity, expiration_date, inventory_id) VALUES (:quantity, :expiration_date, :inventory_id)";
        $statementInventoryItems = $pdo->prepare($sqlInventoryItems);
        $statementInventoryItems->bindParam(':quantity', $quantity);
        $statementInventoryItems->bindParam(':expiration_date', $expiration);
        $statementInventoryItems->bindParam(':inventory_id', $inventoryID);
        $statementInventoryItems->execute();

        //INSERT HERE THE FUNCTION THAT RE-COMPUTES TBLINVENTORY QUANTITY BASED ON TBLINVENTORYITEMS
        recalculateInventoryQuantity($pdo);

        //recalculate product sku and availability
        recalculateStatus($pdo, $productsData);

        // Insert data into tblInventoryReports
        $recordType = "Adding Supply";
        $sqlInsertReport = "INSERT INTO tblinventoryreport (inventory_item, inventory_id, quantity, unit, record_type, reason) VALUES (:inventoryItem, :inventory_id, :quantity, :unit, :record_type, :reason)";
        $statementInsertReport = $pdo->prepare($sqlInsertReport);
        $statementInsertReport->bindParam(':inventoryItem', $inventoryItem);
        $statementInsertReport->bindParam(':inventory_id', $inventoryID);
        $statementInsertReport->bindParam(':quantity', $quantity);
        $statementInsertReport->bindParam(':unit', $unit);
        $statementInsertReport->bindParam(':reason', $reason);
        $statementInsertReport->bindParam(':record_type', $recordType);
        $statementInsertReport->execute();

        // Redirect back to the inventory page after filing pilferage
        header("Location: /admin_dashboard/inventory");
        exit(); // Ensure that code stops executing after redirection
    }
}

// filing a pilferage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_file'])) {
        // Retrieve form data
        $inventoryItem = $_POST['new_item'];
        $itemID = $_POST['item_id'];
        $quantity = $_POST['new_quantity'];
        $reason = $_POST['reason'];
        $quantityString = "-" . $quantity;

        //remove expired and zero quantity
        removeExpired($pdo);
        removeZeroQuantity($pdo);
        recalculateInventoryQuantity($pdo);

        // Fetch the unit from the tblinventory table
        $sqlFetchUnit = "SELECT unit FROM tblinventory WHERE inventory_id = :inventory_id";
        $statementFetchUnit = $pdo->prepare($sqlFetchUnit);
        $statementFetchUnit->bindParam(':inventory_id', $itemID);
        $statementFetchUnit->execute();
        $unitResult = $statementFetchUnit->fetch(PDO::FETCH_ASSOC);
        $unit = $unitResult['unit'];

        // Fetch inventoryitems with expiration_date ascending
        $sqlInventoryItems = "SELECT * FROM tblinventoryitems WHERE inventory_id = :inventory_id ORDER BY expiration_date ASC, quantity ASC";
        $statementInventoryItems = $pdo->prepare($sqlInventoryItems);
        $statementInventoryItems->bindParam(':inventory_id', $itemID);
        $statementInventoryItems->execute();
        $inventoryitemsData = $statementInventoryItems->fetchAll(PDO::FETCH_ASSOC);


        foreach ($inventoryitemsData as $data) {
            if ($quantity <= 0) {
                break; // Exit the loop if there's nothing left to deduct
            }

            // Calculate the deduction amount for the current item
            $deductionAmount = min($data['quantity'], $quantity);
            error_log("deduction Amount: $deductionAmount");

            // Update the quantity for the current inventory item
            $sqlDeduct = "UPDATE tblinventoryitems SET quantity = quantity - :deductionAmount WHERE tblinventoryitems_id = :inventoryID";
            $statementDeduct = $pdo->prepare($sqlDeduct);
            $statementDeduct->bindParam(':deductionAmount', $deductionAmount, PDO::PARAM_INT);
            $statementDeduct->bindParam(':inventoryID', $data['tblinventoryitems_id']);
            $statementDeduct->execute();
            // Update the remaining quantity to deduct
            $quantity -= $deductionAmount;
        }



        //INSERT HERE THE FUNCTION THAT RE-COMPUTES TBLINVENTORY QUANTITY BASED ON TBLINVENTORYITEMS
        recalculateInventoryQuantity($pdo);

        //recalculate product sku and availability
        recalculateStatus($pdo, $productsData);

        // Convert the quantity to a string and prepend "-"


        // Insert data into tblInventoryReports
        date_default_timezone_set('Asia/Manila');
        $datetime = date('Y-m-d H:i:s');
        $recordType = "Inventory Shrinkage";
        $sqlInsertReport = "INSERT INTO tblinventoryreport (inventory_item, inventory_id, quantity, unit, record_type, reason) VALUES (:inventoryItem, :itemID, :quantityString, :unit, :record_type, :reason)";
        $statementInsertReport = $pdo->prepare($sqlInsertReport);
        $statementInsertReport->bindParam(':inventoryItem', $inventoryItem);
        $statementInsertReport->bindParam(':itemID', $itemID);
        $statementInsertReport->bindParam(':quantityString', $quantityString); // Bind the modified quantity string
        $statementInsertReport->bindParam(':unit', $unit); // Bind the unit directly
        $statementInsertReport->bindParam(':record_type', $recordType);
        $statementInsertReport->bindParam(':reason', $reason);
        $statementInsertReport->execute();


        // Redirect back to the inventory page after filing pilferage
        header("Location: /admin_dashboard/inventory");
        exit(); // Ensure that code stops executing after redirection
    }
}


// Sa add,edit,delete button
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sa Add action to
    if (isset($_POST['submit_add'])) {
        $newItem = $_POST['new_item'];
        $newType = $_POST['new_type'];
        $newQuantity = $_POST['new_quantity'];
        $newExpiration = $_POST['new_expiration'];
        $newUnit = $_POST['new_unit'];
        $newReorderPoint = $_POST['new_reorder_point'];

        $sqlAdd = "INSERT INTO tblinventory (inventory_item, item_type, unit, reorder_point) VALUES (:newItem, :newType, :newUnit, :reorderPoint)";
        $statementAdd = $pdo->prepare($sqlAdd);
        $statementAdd->bindParam(':newItem', $newItem);
        $statementAdd->bindParam(':newType', $newType);
        $statementAdd->bindParam(':newUnit', $newUnit);
        $statementAdd->bindParam(':reorderPoint', $newReorderPoint);
        $statementAdd->execute();

        //get the next inventory_id in tblinventroy
        $sql = "SELECT MAX(inventory_id) AS last_inventory_id FROM tblinventory";
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $lastInventoryIDResult = $statement->fetch(PDO::FETCH_ASSOC);
        $lastInventoryID = $lastInventoryIDResult['last_inventory_id'];

        //insert to tblinventoryitems the initial supply
        $sqlInventoryItems = "INSERT INTO tblinventoryitems (quantity, expiration_date, inventory_id) VALUES (:quantity, :expiration_date, :inventory_id)";
        $statementInventoryItems = $pdo->prepare($sqlInventoryItems);
        $statementInventoryItems->bindParam(':quantity', $newQuantity);
        $statementInventoryItems->bindParam(':expiration_date', $newExpiration);
        $statementInventoryItems->bindParam(':inventory_id', $lastInventoryID);
        $statementInventoryItems->execute();

        //INSERT HERE THE FUNCTION THAT RE-COMPUTES TBLINVENTORY QUANTITY BASED ON TBLINVENTORYITEMS
        recalculateInventoryQuantity($pdo);

        //add a record for initial supply of a new inventory
        $reason = "New Inventory initial supply for " . $newItem;
        $recordType = "Initial Supply";
        $sqlInsertReport = "INSERT INTO tblinventoryreport (inventory_item, inventory_id, quantity, unit, record_type,reason) VALUES (:inventoryItem, :itemID, :quantityString, :unit, :record_type, :reason)";
        $statementInsertReport = $pdo->prepare($sqlInsertReport);
        $statementInsertReport->bindParam(':inventoryItem', $newItem);
        $statementInsertReport->bindParam(':itemID', $lastInventoryID);
        $statementInsertReport->bindParam(':quantityString', $newQuantity); // Bind the modified quantity string
        $statementInsertReport->bindParam(':unit', $newUnit); // Bind the unit directly
        $statementInsertReport->bindParam(':record_type', $recordType);
        $statementInsertReport->bindParam(':reason', $reason);
        $statementInsertReport->execute();

        //add user log [add new inventory item]
        $DateTime = new DateTime();
        $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
        $DateTime->setTimeZone($philippinesTimeZone);

        $currentDateTime = $DateTime->format('Y-m-d H:i:s');
        $employeeid = $_SESSION['employeeID'];
        $loginfo = $_SESSION['username'] . ' has added a new inventory item.';

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
        header("Location: /admin_dashboard/inventory");
    }

    // Ito sa edit action
    if (isset($_POST['submit_edit'])) {

        try {
            $editItemId = $_POST['edit_item_id'];
            $editedItem = $_POST['edited_item'];
            $editedType = $_POST['edited_type'];
            // $editedQuantity = $_POST['edited_quantity'];
            $editedUnit = $_POST['edited_unit'];
            $editedReorderPoint = $_POST['edited_reorder_point'];


            $sqlEdit = "UPDATE tblinventory SET inventory_item = :editedItem, item_type = :editedType, unit = :editedUnit, reorder_point = :editedReorderPoint  WHERE inventory_id = :editItemId";
            $statementEdit = $pdo->prepare($sqlEdit);
            $statementEdit->bindParam(':editItemId', $editItemId);
            $statementEdit->bindParam(':editedItem', $editedItem);
            $statementEdit->bindParam(':editedType', $editedType);
            // $statementEdit->bindParam(':editedQuantity', $editedQuantity);
            $statementEdit->bindParam(':editedUnit', $editedUnit);
            $statementEdit->bindParam(':editedReorderPoint', $editedReorderPoint);
            $statementEdit->execute();

            //add user log [edited an inventory item]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has edited an inventory item.';

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

            header("Location: /admin_dashboard/inventory");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Sa delete 
    if (isset($_POST['submit_delete'])) {
        try {
            $deleteItemId = $_POST['delete_item_id'];

            $sqlDelete = "DELETE FROM tblinventory WHERE inventory_id = :deleteItemId";
            $statementDelete = $pdo->prepare($sqlDelete);
            $statementDelete->bindParam(':deleteItemId', $deleteItemId);
            $statementDelete->execute();

            //add user log [delete an inventory item]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has deleted an inventory item.';

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

            // Redirect to inventory.php after successful deletion
            header("Location: /admin_dashboard/inventory");
            exit(); // Ensure that code stops executing after redirection
        } catch (PDOException $e) {
            // Handle any potential errors here
            echo "Error: " . $e->getMessage();
            // You might want to log the error or redirect to an error page
        }
    }

    // Ito sa update all inventory action
    if (isset($_POST['submit_update_all'])) {
        try {
            foreach ($inventoryData as $inventoryDataRow) {
                // Retrieve the submitted quantity for the specific inventory item
                $quantityKey = "newQuantity" . $inventoryDataRow['inventory_id'];
                $newQuantity = $_POST[$quantityKey];

                // Perform the update query for each inventory item
                $updateSql = "UPDATE tblinventory SET quantity = :newQuantity WHERE inventory_id = :inventoryId";
                $updateStatement = $pdo->prepare($updateSql);
                $updateStatement->bindParam(':newQuantity', $newQuantity);
                $updateStatement->bindParam(':inventoryId', $inventoryDataRow['inventory_id']);
                $updateStatement->execute();
            }

            //add user log [updated all inventory quantity]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has updated all inventory quantity.';

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

            header("Location: /admin_dashboard/inventory");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }


    //add edit delete sa categories
    //add category
    if (isset($_POST['addCategory'])) {
        try {
            $newCategory = $_POST['new_category'];

            $sqlAddCategory = "INSERT INTO tblcategory_inventory (inventory_category) VALUES (:newCategory)";
            $statementAddCategory = $pdo->prepare($sqlAddCategory);
            $statementAddCategory->bindParam(':newCategory', $newCategory);
            $statementAddCategory->execute();

            //add user log [added a new inventory category]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has added a new inventory category.';

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

            header("Location: /admin_dashboard/inventory");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    //delete category
    if (isset($_POST['categoryDelete'])) {
        try {
            $deleteCategoryId = $_POST['delete_category_id'];

            $sqlDeleteCategory = "DELETE FROM tblcategory_inventory WHERE categoryInventory_id = :deleteCategoryId";
            $statementDeleteCategory = $pdo->prepare($sqlDeleteCategory);
            $statementDeleteCategory->bindParam(':deleteCategoryId', $deleteCategoryId);
            $statementDeleteCategory->execute();

            //add user log [deleted an inventory category]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has deleted an inventory category.';

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

            // Redirect to inventory.php after successful deletion
            header("Location: /admin_dashboard/inventory");
            exit(); // Ensure that code stops executing after redirection
        } catch (PDOException $e) {
            // Handle any potential errors here
            echo "Error: " . $e->getMessage();
            // You might want to log the error or redirect to an error page
        }
    }

    //save edit category
    if (isset($_POST['update_category'])) {
        try {
            $editCategoryId = $_POST['update_category_id'];
            $editedCategory = $_POST['update_inventoryCategory'];


            $sqlEditCategory = "UPDATE tblcategory_inventory SET inventory_category = :editedCategory WHERE categoryInventory_id = :editCategoryId";
            $statementEditCategory = $pdo->prepare($sqlEditCategory);
            $statementEditCategory->bindParam(':editCategoryId', $editCategoryId);
            $statementEditCategory->bindParam(':editedCategory', $editedCategory);
            $statementEditCategory->execute();

            //add user log [edited an inventory category]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has edited an inventory category.';

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

            header("Location: /admin_dashboard/inventory");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}



// Fetch total products
$sqlTotalProducts = "SELECT COUNT(*) AS totalProducts FROM tblinventory";
$statementTotalProducts = $pdo->prepare($sqlTotalProducts);
$statementTotalProducts->execute();
$totalProductsData = $statementTotalProducts->fetch(PDO::FETCH_ASSOC);
if ($statementTotalProducts->rowCount() == 0) {
    $totalProducts = 0;
} else {
    $totalProducts = $totalProductsData['totalProducts'];
}
// Fetch data for Low Stock Chart
$sqlLowStock = "SELECT COUNT(*) as lowStock
                FROM (
                    SELECT * 
                    FROM tblinventory
                    WHERE status = 'Low Stock'
                ) AS subquery";

$statementLowStock = $pdo->prepare($sqlLowStock);
$statementLowStock->execute();
if ($statementLowStock->rowCount() == 0) {
    $lowStockData = 0;
} else {
    $lowStockData = $statementLowStock->fetchAll(PDO::FETCH_ASSOC);
}


// Fetch data for Out of Stock Chart
$sqlOutOfStock = "SELECT item_type, COUNT(*) as out_of_stock
                  FROM tblinventory
                  WHERE quantity <= 0
                  GROUP BY item_type";

$statementOutOfStock = $pdo->prepare($sqlOutOfStock);
$statementOutOfStock->execute();
if ($statementTotalProducts->rowCount() == 0) {
    $outOfStockData = 0;
} else {
    $outOfStockData = $statementOutOfStock->fetchAll(PDO::FETCH_ASSOC);
}


// Fetch data for Most Stock Chart
$sqlMostStock = "SELECT MAX(quantity) as most_stock
                FROM tblinventory
                GROUP BY item_type
                ORDER BY quantity DESC
                ;";
$statementMostStock = $pdo->prepare($sqlMostStock);
$statementMostStock->execute();
if ($statementTotalProducts->rowCount() == 0) {
    $mostStockData = 0;
} else {
    $mostStockData = $statementMostStock->fetchAll(PDO::FETCH_ASSOC);
}

view('dashboard/inventory.view.php', [
    'totalProducts' => $totalProducts,
    'inventoryData' => $inventoryData,
    'lowStockData' => $lowStockData,
    'outOfStockData' => $outOfStockData,
    'mostStockData' => $mostStockData,
    'categoryInventoryData' => $categoryInventoryData
]);
