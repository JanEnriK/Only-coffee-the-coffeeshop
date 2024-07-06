<?php
session_start();
if (!isset($_SESSION['position']) && !isset($_SESSION['username'])  && !isset($_SESSION['employeeID'])) {
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
$dbname = "coffeeshop_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

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

// Sa add,edit,delete button
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sa Add action to
    if (isset($_POST['submit_add'])) {
        $newItem = $_POST['new_item'];
        $newType = $_POST['new_type'];
        $newQuantity = $_POST['new_quantity'];
        $newUnit = $_POST['new_unit'];

        $sqlAdd = "INSERT INTO tblinventory (inventory_item, item_type ,quantity, unit) VALUES (:newItem, :newType, :newQuantity, :newUnit)";
        $statementAdd = $pdo->prepare($sqlAdd);
        $statementAdd->bindParam(':newItem', $newItem);
        $statementAdd->bindParam(':newType', $newType);
        $statementAdd->bindParam(':newQuantity', $newQuantity);
        $statementAdd->bindParam(':newUnit', $newUnit);
        $statementAdd->execute();

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
        header("Location: inventory.php");
    }

    // Ito sa edit action
    if (isset($_POST['submit_edit'])) {
        try {
            $editItemId = $_POST['edit_item_id'];
            $editedItem = $_POST['edited_item'];
            $editedType = $_POST['edited_type'];
            $editedQuantity = $_POST['edited_quantity'];
            $editedUnit = $_POST['edited_unit'];


            $sqlEdit = "UPDATE tblinventory SET inventory_item = :editedItem, item_type = :editedType, quantity = :editedQuantity, unit = :editedUnit  WHERE inventory_id = :editItemId";
            $statementEdit = $pdo->prepare($sqlEdit);
            $statementEdit->bindParam(':editItemId', $editItemId);
            $statementEdit->bindParam(':editedItem', $editedItem);
            $statementEdit->bindParam(':editedType', $editedType);
            $statementEdit->bindParam(':editedQuantity', $editedQuantity);
            $statementEdit->bindParam(':editedUnit', $editedUnit);
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

            header("Location: inventory.php");
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
            header("Location: inventory.php");
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

            header("Location: inventory.php");
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

            header("Location: inventory.php");
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
            header("Location: inventory.php");
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

            header("Location: inventory.php");
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
                    WHERE quantity > 0 AND quantity <= 10
                ) AS subquery";

$statementLowStock = $pdo->prepare($sqlLowStock);
$statementLowStock->execute();
if ($statementTotalProducts->rowCount() == 0) {
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
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

        .containertab {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            flex-wrap: wrap;
            text-align: center;
        }

        .stock-container {
            width: 18%;
            padding: 15px;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .total-products-container,
        .low-stock-container,
        .zero-stock-container,
        .most-stock-container,
        .out-of-stock-container {
            width: 18%;
            margin: 10px;
            padding: 5px;
            border-radius: 8px;
        }

        .total-products-container {
            background-color: #5e8fbf;
            color: #fff;
        }

        .low-stock-container {
            background-color: #ff6347;
            color: #fff;
        }

        .zero-stock-container {
            background-color: #1e90ff;
            color: #fff;
        }

        .out-of-stock-container {
            background-color: #ff0000;
            color: #fff;
        }

        .most-stock-container {
            background-color: #4caf50;
            color: #fff;
        }

        table {
            width: 70%;
            border-collapse: collapse;
            border: #333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            color: #333;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #4caf50;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }


        .button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button.add-button {
            background-color: #4CAF50;
            color: white;
            margin-right: 5px;
        }

        .button.edit-button {
            background-color: #008CBA;
            color: white;
        }

        .button.delete-button {
            background-color: #FF6347;
            color: white;
        }

        /* Style sa edit form */
        .edit-form {
            display: none;
            text-align: center;
        }

        .edit-form input {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .edit-form select {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .edit-form button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background-color: #008CBA;
            color: white;
            cursor: pointer;
        }

        .action-buttons {
            display: flex;
        }

        .button-form {
            display: table-row;
            text-align: center;
            vertical-align: middle;
        }

        /*STYLE FORDA OVER LAY FORM */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            overflow: auto;
            box-sizing: border-box;
        }

        .overlay-content {
            max-height: 100%;
            /* Adjust maximum height as needed */
            max-width: 100%;
            /* Adjust maximum width as needed */
            overflow-y: auto;
        }

        /*default table no design */
        .tableDefault,
        .tableDefault tr:nth-child(even) {
            border: none;
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            /* Add borders for demonstration; you can remove or modify this */
            padding: 8px;
            /* Add padding for better readability; you can adjust this */
            text-align: left;
            color: black;
            background-color: transparent;
            box-shadow: none;

            /* Optional: Alternate background color for headers */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // toggle row of edit inventory data 
        function toggleEditForm(formId) {
            var editForm = document.getElementById(formId);
            editForm.style.display = (editForm.style.display === 'none' || editForm.style.display === '') ? 'table-row' : 'none';
        }

        //toggle row of edit category
        function toggleEditCategoryForm(categoryId) {
            var categoryForm = document.getElementById(categoryId);
            categoryForm.style.display = (categoryForm.style.display === 'none' || categoryForm.style.display === '') ? 'table-row' : 'none';
        }

        // add inventory button

        document.addEventListener('DOMContentLoaded', function() {
            const addForm = document.getElementById('addForm');
            const overlay = document.getElementById('overlay');
            const closeFormBtn = document.getElementById('closeFormBtn');
            const body = document.body;
            // Initially hide the overlay form
            overlay.style.display = 'none';

            // Show the overlay form when the button is clicked
            addForm.addEventListener('click', function() {
                overlay.style.display = 'flex';
                body.style.overflow = 'hidden';
            });

            // Close the overlay form when the close button is clicked
            closeFormBtn.addEventListener('click', function() {
                overlay.style.display = 'none';
                body.style.overflow = 'visible';
            });
        });


        //update all invenrtory button
        document.addEventListener('DOMContentLoaded', function() {
            const updateInventory = document.getElementById('updateInventoryBtn');
            const overlayInventory = document.getElementById('updateInventory');
            const closeUpdateFormBtn = document.getElementById('closeUpdateFormBtn');
            const body = document.body;
            // Initially hide the update ingredients form
            overlayInventory.style.display = 'none';

            // Show the overlay form when the button is clicked
            updateInventory.addEventListener('click', function() {
                overlayInventory.style.display = 'flex';
                body.style.overflow = 'hidden';
            });

            // Close the overlay form when the close button is clicked
            closeUpdateFormBtn.addEventListener('click', function() {
                overlayInventory.style.display = 'none';
                body.style.overflow = 'visible';
            });
        });

        //inventory category settings

        document.addEventListener('DOMContentLoaded', function() {
            const categoryInventory = document.getElementById('categoryInventory');
            const overlayCategory = document.getElementById('inventoryCategory');
            const closeCategoryFormBtn = document.getElementById('closeCategoryForm');
            const body = document.body;
            // Initially hide the overlay form
            overlayCategory.style.display = 'none';

            // Show the overlay form when the button is clicked
            categoryInventory.addEventListener('click', function() {
                overlayCategory.style.display = 'flex';
                body.style.overflow = 'hidden';
            });

            // Close the overlay form when the close button is clicked
            closeCategoryFormBtn.addEventListener('click', function() {
                overlayCategory.style.display = 'none';
                body.style.overflow = 'visible';
            });
        });
    </script>
</head>

<body>
    <!--hidden add inventory form-->
    <div class="overlay" id="overlay">
        <div class="overlay-content">
            <div class="info-box">
                <button id="closeFormBtn" class="button delete-button">X</button>
                <h2>Add New Inventory</h2>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="new_item">Inventory Item:</label>
                        <input type="text" class="form-control" name="new_item" placeholder="Inventory Item:" required>
                    </div>
                    <div class="form-group">
                        <label for="new_type">Inventory Type:</label>
                        <select name="new_type" class="form-control" id="new_type" required>
                            <option value="" selected disabled>Inventory Type:</option>
                            <?php foreach ($categoryInventoryData as $category) : ?>
                                <option value="<?= $category['inventory_category'] ?>">
                                    <?= $category['inventory_category'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="new_quantity">Quantity: </label>
                        <input type="number" class="form-control" name="new_quantity" placeholder="Quantity: " required>
                    </div>
                    <div class="form-group">
                        <label for="new_unit">Unit: </label>
                        <input type="text" class="form-control" name="new_unit" placeholder="Unit:" required>
                    </div>
                    <button type="submit" name="submit_add" class="button add-button" style="width:100%;">Add</button>
            </div>
            </form>
        </div>
    </div>

    <!--hidden update all inventory form-->
    <div class="overlay" id="updateInventory">
        <div class="overlay-content">
            <div class="info-box">
                <button id="closeUpdateFormBtn" class="button delete-button">X</button>
                <h2>Update All Inventory</h2>
                <form method="post" action="">
                    <div>
                        <table class="tableDefault">
                            <tr class="tableDefault">
                                <th class="tableDefault">Item</th>
                                <th class="tableDefault">Current Quantity</th>
                                <th class="tableDefault">New Quantity</th>
                            </tr>
                            <?php foreach ($inventoryData as $inventoryDataRow) : ?>
                                <tr class="tableDefault">
                                    <td class="tableDefault">
                                        <?= $inventoryDataRow['inventory_item'] ?>
                                    </td>
                                    <td class="tableDefault">
                                        <?= $inventoryDataRow['quantity'] ?>
                                    </td>
                                    <td class="tableDefault">
                                        <input type="number" name="<?= "newQuantity" . $inventoryDataRow['inventory_id'] ?>" placeholder="Edit Quantity" value="<?= $inventoryDataRow['quantity'] ?>" required>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <br>
                    <button type="submit" name="submit_update_all" class="button add-button" style="width:100%;">Apply
                        Update</button>
                    <br><br>
                </form>
            </div>
        </div>
    </div>

    <!--hidden inventory category form-->
    <div class="overlay" id="inventoryCategory">
        <div class="overlay-content">
            <div class="info-box">
                <button id="closeCategoryForm" class="button delete-button">X</button>
                <h2>Inventory Categories</h2>
                <div class="form-group">
                    <table style="margin: auto;">
                        <?php foreach ($categoryInventoryData as $category) : ?>
                            <tr>

                                <td>
                                    <?= $category['inventory_category'] ?>
                                </td>
                                <td>
                                    <form method="post" action="">
                                        <input type="hidden" name="update_category_id" value="<?= $category['categoryInventory_id'] ?>">
                                        <button type="button" class="button edit-button" onclick="toggleEditCategoryForm('editCategory<?= $category['categoryInventory_id'] ?>')">✎</button>
                                        <input type="hidden" name="delete_category_id" value="<?= $category['categoryInventory_id'] ?>">
                                        <button type="submit" name="categoryDelete" class="button delete-button">✖</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="edit-form" id="editCategory<?= $category['categoryInventory_id'] ?>">
                                <td colspan="2">
                                    <form method="post" action="">
                                        <input type="hidden" name="update_category_id" value="<?= $category['categoryInventory_id'] ?>">
                                        <input type="text" name="update_inventoryCategory" value="<?= $category['inventory_category'] ?>" required>
                                        <button type="submit" name="update_category" class="button edit-button">💾</button>
                                    </form>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <h2>Add New Category</h2>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="new_category">Inventory Item:</label>
                        <input type="text" class="form-control" name="new_category" placeholder="Category Name" required>
                    </div>
                    <button type="submit" name="addCategory" class="button add-button" style="width:100%;">Add</button>
                </form>
            </div>
        </div>
    </div>


    <!--Visible Main-->
    <div class="dashboard">
        <div class="sidebar">
            <h1>Coffee Shop</h1>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="coffeeshop_info.php">CoffeeShop</a></li>
                <li><a href="accounts.php">Accounts</a></li>
                <li><a href="Orders.php">Orders</a></li>
                <li style="background-color: var(--primary-color);"><a href="Inventory.php">Inventory</a></li>
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
            <h2>Inventory
                <?php echo " (" . $_SESSION['username'] . " the " . $_SESSION['position'] . ") " ?>
            </h2>

            <div>
                <div class="containertab">
                    <div class="stock-container total-products-container">
                        <h4>Total Products</h4>
                        <p>
                            <?php echo $totalProducts; ?>
                        </p>
                    </div>
                    <div class="stock-container low-stock-container">
                        <h4>Low Stock</h4>
                        <p>
                            <?php echo !empty($lowStockData) ? $lowStockData[0]['lowStock'] : 0; ?>
                        </p>
                    </div>

                    <div class="stock-container out-of-stock-container">
                        <h4>Out of Stock</h4>
                        <p>
                            <?php echo !empty($outOfStockData) ? $outOfStockData[0]['out_of_stock'] : 0; ?>
                        </p>
                    </div>
                    <div class="stock-container most-stock-container">
                        <h4>Most Stock</h4>
                        <p>
                            <?php echo !empty($mostStockData) ? $mostStockData[0]['most_stock'] : 0; ?>
                        </p>
                    </div>
                </div>
                <br>
                <div style="display: flex; justify-content: space-between;">
                    <button type="button" class="button add-button" id="addForm">+ Add
                        Inventory</button>
                    <button type="button" class="button add-button" id="updateInventoryBtn" style="margin-left: auto;">Update All
                        Inventory</button>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Inventory Item</th>
                                <th>Item Type
                                    <button type="button" id="categoryInventory" style="background-color:transparent; border:none; padding:none;">⚙️</button>
                                </th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventoryData as $item) : ?>
                                <tr>
                                    <td>
                                        <?= $item['inventory_item'] ?>
                                    </td>
                                    <td>
                                        <?= $item['item_type'] ?>
                                    </td>
                                    <td>
                                        <?= $item['quantity'] ?>
                                    </td>
                                    <td>
                                        <?= $item['unit'] ?>
                                    </td>

                                    <td class="action-buttons">
                                        <form method="post" action="" class="button-form">
                                            <input type="hidden" name="edit_item_id" value="<?= $item['inventory_id'] ?>">
                                            <button type="button" class="button edit-button" onclick="toggleEditForm('editForm<?= $item['inventory_id'] ?>')">✎</button>
                                            <input type="hidden" name="delete_item_id" value="<?= $item['inventory_id'] ?>">
                                            <button type="submit" name="submit_delete" class="button delete-button">✖</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="edit-form" id="editForm<?= $item['inventory_id'] ?>">
                                    <td colspan="7">
                                        <form method="post" action="">
                                            <input type="hidden" name="edit_item_id" value="<?= $item['inventory_id'] ?>">
                                            <input type="text" name="edited_item" placeholder="Edit Item" value="<?= $item['inventory_item'] ?>" required>
                                            <select name="edited_type" id="edited_type" required>
                                                <?php foreach ($categoryInventoryData as $category) : ?>
                                                    <option value="<?= $category['inventory_category'] ?>" <?php echo ($item['item_type'] == $category['inventory_category']) ? 'selected' : ''; ?>>
                                                        <?= $category['inventory_category'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="number" name="edited_quantity" placeholder="Edit Quantity" value="<?= $item['quantity'] ?>" required>
                                            <input type="text" name="edited_unit" placeholder="Edit Unit" value="<?= $item['unit'] ?>" required>
                                            <button type="submit" name="submit_edit" class="button edit-button">💾</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>