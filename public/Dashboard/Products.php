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

// Fetch products data
$sql = "SELECT * FROM tblproducts";
$statement = $pdo->prepare($sql);
$statement->execute();
$productsData = $statement->fetchAll(PDO::FETCH_ASSOC);
//Fetch inventory data
$sql = "SELECT * FROM tblinventory";
$inventoryStatement = $pdo->prepare($sql);
$inventoryStatement->execute();
$inventoryData = $inventoryStatement->fetchAll(PDO::FETCH_ASSOC);
// Fetch data from tblcategory_product
$sqlCategoryProduct = "SELECT * FROM tblcategory_product";
$categoryProductStatement = $pdo->prepare($sqlCategoryProduct);
$categoryProductStatement->execute();
$categoryProductData = $categoryProductStatement->fetchAll(PDO::FETCH_ASSOC);
// Fetch data from tblPromos
$sqlPromos = "SELECT * FROM tblpromo";
$promosStatement = $pdo->prepare($sqlPromos);
$promosStatement->execute();
$promosData = $promosStatement->fetchAll(PDO::FETCH_ASSOC);


//code that calculates whether a product is available, not available or ingredients are not set

foreach ($productsData as $productRow) {
    $sql = "SELECT *
    FROM
    tblproducts_inventory PI
    JOIN
    tblproducts P ON PI.products_id = P.product_Id
    JOIN
    tblInventory I ON PI.inventory_id = I.inventory_id
    WHERE
    PI.products_id = $productRow[product_id]
    ";
    $checkInventory = $pdo->prepare($sql);
    $checkInventory->execute();
    $productInventoryData = $checkInventory->fetchAll(PDO::FETCH_ASSOC);

    if ($checkInventory->rowCount() === 0) {
        $sqlChangeNull = "UPDATE tblproducts SET status = NULL WHERE tblproducts.product_id = $productRow[product_id]";
        $changeNull = $pdo->prepare($sqlChangeNull);
        $changeNull->execute();
    } else {
        foreach ($productInventoryData as $productInventoryRow) {
            if ($productInventoryRow['quantity'] > 0) {
                $sqlChangeAvailable = "UPDATE tblproducts SET status = 'Available' WHERE tblproducts.product_id = :productId;";
                $changeAvailable = $pdo->prepare($sqlChangeAvailable);
                $changeAvailable->bindParam(':productId', $productRow['product_id']);
                $changeAvailable->execute();
            } else {
                $sqlChangeNotAvailable = "UPDATE tblproducts SET status = 'Not Available' WHERE tblproducts.product_id = :productId;";
                $changeNotAvailable = $pdo->prepare($sqlChangeNotAvailable);
                $changeNotAvailable->bindParam(':productId', $productRow['product_id']);
                $changeNotAvailable->execute();
                break;
            }
        }
    }
}


// Sa add,edit,delete button
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_add'])) {
        // Sa Add action to
        $newProduct = $_POST['new_product'];
        $newDescription = $_POST['new_productDescription'];
        $newPrice = $_POST['new_price'];
        $newStatus = $_POST['new_status'];
        $newCategory = $_POST['new_category'];

        try {
            $sqlAdd = "INSERT INTO tblproducts (product_name, product_description ,price,  category) VALUES (:newProduct, :newDescription, :newPrice, :newCategory)";
            $statementAdd = $pdo->prepare($sqlAdd);
            $statementAdd->bindParam(':newProduct', $newProduct);
            $statementAdd->bindParam(':newDescription', $newDescription);
            $statementAdd->bindParam(':newPrice', $newPrice);
            $statementAdd->bindParam(':newCategory', $newCategory);
            $statementAdd->execute();

            //add user log [added a new product]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has added a new product.';

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

            header("Location: Products.php");
            exit(); // Ensure to stop script execution after redirection
        } catch (PDOException $e) {
            // Handle the exception/error
            echo "Error: " . $e->getMessage();
            // You can log the error or perform additional actions based on your requirements
        }
    }

    // Ito sa edit action
    if (isset($_POST['submit_edit'])) {
        $editProductId = $_POST['edit_product_id'];
        $editedProduct = $_POST['edited_product'];
        $editedDescription = $_POST['edited_description'];
        $editedPrice = $_POST['edited_price'];
        $editedCategory = $_POST['edited_category'];

        try {
            $sqlEdit = "UPDATE tblproducts SET product_name = :editedProduct, product_description = :editedDescription, price = :editedPrice, category = :editedCategory WHERE product_id = :editProductId";
            $statementEdit = $pdo->prepare($sqlEdit);
            $statementEdit->bindParam(':editProductId', $editProductId);
            $statementEdit->bindParam(':editedProduct', $editedProduct);
            $statementEdit->bindParam(':editedDescription', $editedDescription);
            $statementEdit->bindParam(':editedPrice', $editedPrice);
            $statementEdit->bindParam(':editedCategory', $editedCategory);

            $statementEdit->execute();

            //add user log [edited a product]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has edited a product.';

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

            header("Location: Products.php");
            exit(); // Ensure to stop script execution after redirection
        } catch (PDOException $e) {
            // Handle the exception/error
            echo "Error: " . $e->getMessage();
            // You can log the error or perform additional actions based on your requirements
        }
    }


    // Sa delete 
    if (isset($_POST['submit_delete'])) {
        try {
            $deleteItemId = $_POST['delete_product_id'];

            //also reset the ingredients from the product
            try {
                $deleteProductIngredients = $deleteItemId;

                $sqlDelete = "DELETE FROM tblproducts_inventory WHERE products_id = :deleteItemId";
                $statementReset = $pdo->prepare($sqlDelete);
                $statementReset->bindParam(':deleteItemId', $deleteProductIngredients);
                $statementReset->execute();

                //add user log [reset ingredients for a product]
                $DateTime = new DateTime();
                $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
                $DateTime->setTimeZone($philippinesTimeZone);

                $currentDateTime = $DateTime->format('Y-m-d H:i:s');
                $employeeid = $_SESSION['employeeID'];
                $loginfo = $_SESSION['username'] . ' has reset a product ingredients.';

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

                foreach ($productsData as $productRow) {
                    $sql = "SELECT *
                    FROM
                    tblproducts_inventory PI
                    JOIN
                    tblproducts P ON PI.products_id = P.product_Id
                    JOIN
                    tblInventory I ON PI.inventory_id = I.inventory_id
                    WHERE
                    PI.products_id = $productRow[product_id]
                    ";
                    $checkInventory = $pdo->prepare($sql);
                    $checkInventory->execute();
                    $productInventoryData = $checkInventory->fetchAll(PDO::FETCH_ASSOC);

                    if ($checkInventory->rowCount() === 0) {
                        $sqlChangeNull = "UPDATE tblproducts SET status = NULL WHERE tblproducts.product_id = $productRow[product_id]";
                        $changeNull = $pdo->prepare($sqlChangeNull);
                        $changeNull->execute();
                    } else {
                        foreach ($productInventoryData as $productInventoryRow) {
                            if ($productInventoryRow['quantity'] > 0) {
                                $sqlChangeAvailable = "UPDATE tblproducts SET status = 'Available' WHERE tblproducts.product_id = :productId;";
                                $changeAvailable = $pdo->prepare($sqlChangeAvailable);
                                $changeAvailable->bindParam(':productId', $productRow['product_id']);
                                $changeAvailable->execute();
                            } else {
                                $sqlChangeNotAvailable = "UPDATE tblproducts SET status = 'Not Available' WHERE tblproducts.product_id = :productId;";
                                $changeNotAvailable = $pdo->prepare($sqlChangeNotAvailable);
                                $changeNotAvailable->bindParam(':productId', $productRow['product_id']);
                                $changeNotAvailable->execute();
                                break;
                            }
                        }
                    }
                }

                $sqlDelete = "DELETE FROM tblproducts WHERE product_id = :deleteItemId";
                $statementDelete = $pdo->prepare($sqlDelete);
                $statementDelete->bindParam(':deleteItemId', $deleteItemId);
                $statementDelete->execute();

                //add user log [delete a product]
                $DateTime = new DateTime();
                $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
                $DateTime->setTimeZone($philippinesTimeZone);

                $currentDateTime = $DateTime->format('Y-m-d H:i:s');
                $employeeid = $_SESSION['employeeID'];
                $loginfo = $_SESSION['username'] . ' has deleted a product.';

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



                header("Location: " . $_SERVER['PHP_SELF']);
                exit(); // Ensure that code stops executing after redirection
            } catch (PDOException $e) {
                // Handle any potential errors here
                echo "Error: " . $e->getMessage();
                // You might want to log the error or redirect to an error page
            }

            // Redirect to Products.php after successful deletion
            header("Location: Products.php");
            exit(); // Ensure that code stops executing after redirection


        } catch (PDOException $e) {
            // Handle any potential errors here
            echo "Error: " . $e->getMessage();
            // You might want to log the error or redirect to an error page
        }
    }

    //insert ingredients
    if (isset($_POST['submit_insert'])) {
        $productId = $_POST['insert_product_id'];

        try {
            // Loop through posted data to get the selected values
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'select') === 0) {
                    $inventoryId = $value; // Assuming the value is the inventory_id

                    // Prepare and execute the insert query
                    $sqlInsert = "INSERT INTO tblproducts_inventory (products_id, inventory_id) VALUES (:productId, :inventoryId)";
                    $statementInsert = $pdo->prepare($sqlInsert);
                    $statementInsert->bindParam(':productId', $productId);
                    $statementInsert->bindParam(':inventoryId', $inventoryId);
                    $statementInsert->execute();

                    //add user log [insert ingredients for a product]
                    $DateTime = new DateTime();
                    $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
                    $DateTime->setTimeZone($philippinesTimeZone);

                    $currentDateTime = $DateTime->format('Y-m-d H:i:s');
                    $employeeid = $_SESSION['employeeID'];
                    $loginfo = $_SESSION['username'] . ' has inserted ingredients for a product.';

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

                    foreach ($productsData as $productRow) {
                        $sql = "SELECT *
                        FROM
                        tblproducts_inventory PI
                        JOIN
                        tblproducts P ON PI.products_id = P.product_Id
                        JOIN
                        tblInventory I ON PI.inventory_id = I.inventory_id
                        WHERE
                        PI.products_id = $productRow[product_id]
                        ";
                        $checkInventory = $pdo->prepare($sql);
                        $checkInventory->execute();
                        $productInventoryData = $checkInventory->fetchAll(PDO::FETCH_ASSOC);

                        if ($checkInventory->rowCount() === 0) {
                            $sqlChangeNull = "UPDATE tblproducts SET status = NULL WHERE tblproducts.product_id = $productRow[product_id]";
                            $changeNull = $pdo->prepare($sqlChangeNull);
                            $changeNull->execute();
                        } else {
                            foreach ($productInventoryData as $productInventoryRow) {
                                if ($productInventoryRow['quantity'] > 0) {
                                    $sqlChangeAvailable = "UPDATE tblproducts SET status = 'Available' WHERE tblproducts.product_id = :productId;";
                                    $changeAvailable = $pdo->prepare($sqlChangeAvailable);
                                    $changeAvailable->bindParam(':productId', $productRow['product_id']);
                                    $changeAvailable->execute();
                                } else {
                                    $sqlChangeNotAvailable = "UPDATE tblproducts SET status = 'Not Available' WHERE tblproducts.product_id = :productId;";
                                    $changeNotAvailable = $pdo->prepare($sqlChangeNotAvailable);
                                    $changeNotAvailable->bindParam(':productId', $productRow['product_id']);
                                    $changeNotAvailable->execute();
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit(); // Ensure to stop script execution after redirection
        } catch (PDOException $e) {
            // Handle the exception/error
            echo "Error: " . $e->getMessage();
            // You can log the error or perform additional actions based on your requirements
        }
    }

    //RESET INGREDIENTS
    if (isset($_POST['reset_ingredients'])) {
        try {
            $deleteProductIngredients = $_POST['deleteProductIngredients'];

            $sqlDelete = "DELETE FROM tblproducts_inventory WHERE products_id = :deleteItemId";
            $statementReset = $pdo->prepare($sqlDelete);
            $statementReset->bindParam(':deleteItemId', $deleteProductIngredients);
            $statementReset->execute();

            //add user log [reset ingredients for a product]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has reset a product ingredients.';

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

            foreach ($productsData as $productRow) {
                $sql = "SELECT *
                FROM
                tblproducts_inventory PI
                JOIN
                tblproducts P ON PI.products_id = P.product_Id
                JOIN
                tblInventory I ON PI.inventory_id = I.inventory_id
                WHERE
                PI.products_id = $productRow[product_id]
                ";
                $checkInventory = $pdo->prepare($sql);
                $checkInventory->execute();
                $productInventoryData = $checkInventory->fetchAll(PDO::FETCH_ASSOC);

                if ($checkInventory->rowCount() === 0) {
                    $sqlChangeNull = "UPDATE tblproducts SET status = NULL WHERE tblproducts.product_id = $productRow[product_id]";
                    $changeNull = $pdo->prepare($sqlChangeNull);
                    $changeNull->execute();
                } else {
                    foreach ($productInventoryData as $productInventoryRow) {
                        if ($productInventoryRow['quantity'] > 0) {
                            $sqlChangeAvailable = "UPDATE tblproducts SET status = 'Available' WHERE tblproducts.product_id = :productId;";
                            $changeAvailable = $pdo->prepare($sqlChangeAvailable);
                            $changeAvailable->bindParam(':productId', $productRow['product_id']);
                            $changeAvailable->execute();
                        } else {
                            $sqlChangeNotAvailable = "UPDATE tblproducts SET status = 'Not Available' WHERE tblproducts.product_id = :productId;";
                            $changeNotAvailable = $pdo->prepare($sqlChangeNotAvailable);
                            $changeNotAvailable->bindParam(':productId', $productRow['product_id']);
                            $changeNotAvailable->execute();
                            break;
                        }
                    }
                }
            }

            header("Location: " . $_SERVER['PHP_SELF']);
            exit(); // Ensure that code stops executing after redirection
        } catch (PDOException $e) {
            // Handle any potential errors here
            echo "Error: " . $e->getMessage();
            // You might want to log the error or redirect to an error page
        }
    }

    //add edit delete sa categories
    //add category
    if (isset($_POST['addCategory'])) {
        try {
            $newCategory = $_POST['new_category'];

            $sqlAddCategory = "INSERT INTO tblcategory_product (category) VALUES (:newCategory)";
            $statementAddCategory = $pdo->prepare($sqlAddCategory);
            $statementAddCategory->bindParam(':newCategory', $newCategory);
            $statementAddCategory->execute();

            //add user log [added a new product category]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has added a new product category.';

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
            header("Location: Products.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    //delete category
    if (isset($_POST['categoryDelete'])) {
        try {
            $deleteCategoryId = $_POST['delete_category_id'];

            $sqlDeleteCategory = "DELETE FROM tblcategory_product WHERE categoryProduct_id = :deleteCategoryId";
            $statementDeleteCategory = $pdo->prepare($sqlDeleteCategory);
            $statementDeleteCategory->bindParam(':deleteCategoryId', $deleteCategoryId);
            $statementDeleteCategory->execute();

            //add user log [deleted a product category]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has deleted a product category.';

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

            // Redirect to Products.php after successful deletion
            header("Location: Products.php");
            exit(); // Ensure that code stops executing after redirection
        } catch (PDOException $e) {
            // Handle any potential errors here
            echo "Error: " . $e->getMessage();
            // You might want to log the error or redirect to an error page
        }
    }

    //save edit category
    if (isset($_POST['update_product'])) {
        try {
            $editCategoryId = $_POST['update_category_id'];
            $editedCategory = $_POST['update_productCategory'];


            $sqlEditCategory = "UPDATE tblcategory_product SET category = :editedCategory WHERE categoryProduct_id = :editCategoryId";
            $statementEditCategory = $pdo->prepare($sqlEditCategory);
            $statementEditCategory->bindParam(':editCategoryId', $editCategoryId);
            $statementEditCategory->bindParam(':editedCategory', $editedCategory);
            $statementEditCategory->execute();

            //add user log [edit a product category]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has edited a product category.';

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

            header("Location: Products.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }


    //add edit delete sa promos
    //add promos
    if (isset($_POST['addPromo'])) {
        try {
            $newPromoName = $_POST['new_promoName'];
            $newPromoDesc = $_POST['new_promoDesc'];
            $newPromoCode = $_POST['new_promoCode'];
            $newPromoValue = $_POST['new_value'];
            $newPromoStartDate = $_POST['new_startDate'];
            $newPromoEndDate = $_POST['new_endDate'];

            $sqlAddPromo = "INSERT INTO tblpromo (promoname, promodesc, promocode, value, startdate, enddate) VALUES (:newPromoName, :newPromoDesc, :newPromoCode, :newPromoValue, :newPromoStartDate, :newPromoEndDate)";
            $statementAddPromo = $pdo->prepare($sqlAddPromo);
            $statementAddPromo->bindParam(':newPromoName', $newPromoName);
            $statementAddPromo->bindParam(':newPromoDesc', $newPromoDesc);
            $statementAddPromo->bindParam(':newPromoCode', $newPromoCode);
            $statementAddPromo->bindParam(':newPromoValue', $newPromoValue);
            $statementAddPromo->bindParam(':newPromoStartDate', $newPromoStartDate);
            $statementAddPromo->bindParam(':newPromoEndDate', $newPromoEndDate);
            $statementAddPromo->execute();

            //add user log [add new promo]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has added a new promo.';

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

            header("Location: Products.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    //delete promos
    if (isset($_POST['promoDelete'])) {
        try {
            $deletePromoId = $_POST['delete_promo_id'];

            $sqlDeletePromo = "DELETE FROM tblpromo WHERE promoid = :deletePromoId";
            $statementDeletePromo = $pdo->prepare($sqlDeletePromo);
            $statementDeletePromo->bindParam(':deletePromoId', $deletePromoId);
            $statementDeletePromo->execute();

            //add user log [delete a promo]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has deleted a promo.';

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

            // Redirect to Products.php after successful deletion
            header("Location: Products.php");
            exit(); // Ensure that code stops executing after redirection
        } catch (PDOException $e) {
            // Handle any potential errors here
            echo "Error: " . $e->getMessage();
            // You might want to log the error or redirect to an error page
        }
    }

    //save edit promos
    if (isset($_POST['update_promo'])) {
        try {
            $editedPromoId = $_POST['update_promo_id'];
            $editedPromoName = $_POST['update_promoName'];
            $editedPromoDesc = $_POST['update_promoDesc'];
            $editedPromoCode = $_POST['update_promoCode'];
            $editedPromoValue = $_POST['update_promoValue'];
            $editedPromoStartDate = $_POST['update_promoStartDate'];
            $editedPromoEndDate = $_POST['update_promoEndDate'];

            $sqlEditPromo = "UPDATE tblpromo SET promoname = :editedPromoName, promodesc = :editedPromoDesc, promocode = :editedPromoCode, value = :editedPromoValue, startdate = :editedPromoStartDate, enddate = :editedPromoEndDate WHERE promoid = :editedPromoId";
            $statementEditPromo = $pdo->prepare($sqlEditPromo);
            $statementEditPromo->bindParam(':editedPromoId', $editedPromoId);
            $statementEditPromo->bindParam(':editedPromoName', $editedPromoName);
            $statementEditPromo->bindParam(':editedPromoDesc', $editedPromoDesc);
            $statementEditPromo->bindParam(':editedPromoCode', $editedPromoCode);
            $statementEditPromo->bindParam(':editedPromoValue', $editedPromoValue);
            $statementEditPromo->bindParam(':editedPromoStartDate', $editedPromoStartDate);
            $statementEditPromo->bindParam(':editedPromoEndDate', $editedPromoEndDate);

            //add user log [edited a promo]
            $DateTime = new DateTime();
            $philippinesTimeZone = new DateTimeZone('Asia/Manila'); // Set to the Philippines time zone
            $DateTime->setTimeZone($philippinesTimeZone);

            $currentDateTime = $DateTime->format('Y-m-d H:i:s');
            $employeeid = $_SESSION['employeeID'];
            $loginfo = $_SESSION['username'] . ' has edited a promo.';

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

            $statementEditPromo->execute();
            header("Location: Products.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
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

        table {
            width: 100%;
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
            max-width: 300px;
            word-wrap: break-word;
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
            vertical-align: middle;
        }

        .edit-form select {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            vertical-align: middle;
        }

        .edit-form textarea {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            vertical-align: middle;
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

        textarea {
            resize: none;
        }

        .dropdown-container {
            margin-bottom: 10px;
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
        // ganon din katulad nung sa add button visible lang yung form nung edit pag pinindot naman yung edit na button 
        function toggleEditFormy(formId) {
            var editForm = document.getElementById(formId);
            editForm.style.display = (editForm.style.display === 'none' || editForm.style.display === '') ? 'table-row' : 'none';
        }
        // Insert ingredients

        function toggleIngredientForm(currentProdId) {
            var ingredientsForm = document.getElementById(currentProdId);
            ingredientsForm.style.display = (ingredientsForm.style.display === 'none' || ingredientsForm.style.display === '') ? 'flex' : 'none';
            document.body.style.overflow = 'hidden';
        }

        function closeIngredientForm(currentProdId) {
            var ingredientsForm = document.getElementById(currentProdId);
            ingredientsForm.style.display = 'none';
            document.body.style.overflow = 'visible';
        }

        //view ingredients details of the product
        function toggleIngredientsList(currentProdId) {
            var ingredientsForm = document.getElementById(currentProdId);
            ingredientsForm.style.display = (ingredientsForm.style.display === 'none' || ingredientsForm.style.display === '') ? 'flex' : 'none';
            document.body.style.overflow = 'hidden';
        }

        function closeIngredientsList(currentProdId) {
            var ingredientsForm = document.getElementById(currentProdId);
            ingredientsForm.style.display = 'none';
            document.body.style.overflow = 'visible';
        }


        //add drop down to setting ingredients
        let selectIndex = 1; // Initialize select index counter
        function addDropdown(dropDownProduct) {
            const container = document.getElementById(dropDownProduct);

            // Create a new dropdown
            const dropdownContainer = document.createElement('div');
            dropdownContainer.classList.add('dropdown-container');

            // Create a new select element
            const dropdown = document.createElement('select');
            dropdown.setAttribute('required', '');

            // Set the name for the select element
            dropdown.name = `select${selectIndex}`;

            // Increment select index for the next dropdown
            selectIndex++;

            // Create and add the default option
            const option0 = new Option('select ingredients: ', '');
            option0.setAttribute('selected', '');
            option0.setAttribute('disabled', '');
            dropdown.add(option0);

            // Add options from PHP data (assuming $inventoryData is accessible)
            <?php foreach ($inventoryData as $row) :
                echo "option = new Option('" . $row["inventory_item"] . "', '" . $row["inventory_id"] . "');";
                echo "dropdown.add(option);";
            endforeach; ?>

            // Create a remove button
            const removeButton = document.createElement('button');
            removeButton.textContent = 'Remove';
            removeButton.onclick = function() {
                container.removeChild(dropdownContainer);
            };

            // Append dropdown and remove button to the container
            dropdownContainer.appendChild(dropdown);
            dropdownContainer.appendChild(removeButton);
            container.appendChild(dropdownContainer);
        }



        // toggle add products form

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

        //product category settings

        document.addEventListener('DOMContentLoaded', function() {
            const categoryProduct = document.getElementById('categoryProduct');
            const overlayCategory = document.getElementById('productCategory');
            const closeCategoryFormBtn = document.getElementById('closeProductForm');
            const body = document.body;
            // Initially hide the overlay form
            overlayCategory.style.display = 'none';

            // Show the overlay form when the button is clicked
            categoryProduct.addEventListener('click', function() {
                overlayCategory.style.display = 'flex';
                body.style.overflow = 'hidden';
            });

            // Close the overlay form when the close button is clicked
            closeCategoryFormBtn.addEventListener('click', function() {
                overlayCategory.style.display = 'none';
                body.style.overflow = 'visible';
            });
        });

        //toggle row of edit category
        function toggleEditCategoryForm(categoryId) {
            var categoryForm = document.getElementById(categoryId);
            categoryForm.style.display = (categoryForm.style.display === 'none' || categoryForm.style.display === '') ? 'table-row' : 'none';
        }

        //toggle row of edit category
        function toggleManagePromoForm(promoId) {
            var managePromoForm = document.getElementById(promoId);
            managePromoForm.style.display = (managePromoForm.style.display === 'none' || managePromoForm.style.display === '') ? 'table-row' : 'none';
        }

        //manage promos button
        document.addEventListener('DOMContentLoaded', function() {
            const managePromos = document.getElementById('managePromosBtn');
            const overlayManagePromos = document.getElementById('managePromos');
            const closeManagePromosBtn = document.getElementById('closeManagePromosBtn');
            const body = document.body;
            // Initially hide the update ingredients form
            overlayManagePromos.style.display = 'none';

            // Show the overlay form when the button is clicked
            managePromos.addEventListener('click', function() {
                overlayManagePromos.style.display = 'flex';
                body.style.overflow = 'hidden';
            });

            // Close the overlay form when the close button is clicked
            closeManagePromosBtn.addEventListener('click', function() {
                overlayManagePromos.style.display = 'none';
                body.style.overflow = 'visible';
            });
        });
    </script>

</head>

<body>
    <!--add form overlay-->
    <div class="overlay" id="overlay">
        <div class="overlay-content">
            <div class="info-box">
                <button id="closeFormBtn" class="button delete-button">X</button>
                <h2>Add New Product</h2>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="new_product">Product Name:</label>
                        <input type="text" class="form-control" name="new_product" placeholder="Product Name:" required>
                    </div>
                    <div class="form-group">
                        <label for="new_productDescription">Product Description:</label>
                        <textarea name="new_productDescription" class="form-control" rows="4" cols="50" placeholder="Product Description:" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="new_price">Price: </label>
                        <input type="number" step="0.01" class="form-control" name="new_price" placeholder="Price: 0.00" required>
                    </div>
                    <div class="form-group">
                        <label for="new_category">Category: </label>
                        <select name="new_category" class="form-control" id="new_category" required>
                            <option value="" selected disabled>
                                Category:</option>
                            <?php foreach ($categoryProductData as $category) : ?>
                                <option value="<?= $category['category'] ?>">
                                    <?= $category['category'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="submit_add" class="button add-button" style="width:100%;">Add</button>
                </form>
            </div>
        </div>
    </div>

    <!--provide product Ingredients-->
    <?php foreach ($productsData as $products) : ?>
        <div class="overlay" id="ingredientsForm<?= $products['product_id'] ?>" style="overflow:auto;">
            <div class="overlay-content">
                <div class="info-box">
                    <button type="button" onclick="closeIngredientForm('ingredientsForm<?= $products['product_id'] ?>')" class="button delete-button">X</button>
                    <h2>Insert Ingredients for this Product</h2>

                    <div>
                        <h4><b>Product Name:</b>
                            <p style="color:#333; display: inline;">
                                <?php echo $products['product_name']; ?>
                            </p>
                        </h4>

                    </div>
                    <div>
                        <h4><b>Product Description:</b>
                            <p style="color:#333;">
                                <?php echo $products['product_description']; ?>
                            </p>
                        </h4>

                    </div>
                    <div>
                        <h4><b>Price:</b>
                            <p style="color:#333; display: inline;">₱
                                <?php echo $products['price']; ?>
                            </p>
                        </h4>

                    </div>
                    <div>
                        <h4><b>Category:</b>
                            <p style="color:#333; display: inline;">
                                <?php echo $products['category']; ?>
                            </p>
                        </h4>
                    </div>
                    <form method="post" action="">
                        <div id="dropdowns-container<?= $products['product_id'] ?>">
                            <!-- Dropdowns ingredients will be added here -->
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="insert_product_id" value="<?= $products['product_id'] ?>">
                        </div>
                        <div class="form-group">
                            <button type="button" onclick="addDropdown('dropdowns-container<?= $products['product_id'] ?>')">Add
                                Dropdown</button>
                        </div>
                        <button type="submit" name="submit_insert" class="button add-button">Insert
                            Ingredients</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!--Show and for reseting product Ingredients-->
    <?php foreach ($productsData as $products) : ?>
        <div class="overlay" id="ingredientsList<?= $products['product_id'] ?>" style="overflow:auto;">
            <div class="overlay-content">
                <div class="info-box">
                    <button type="button" onclick="closeIngredientsList('ingredientsList<?= $products['product_id'] ?>')" class="button delete-button">X</button>
                    <h2>Insert Ingredients for this Product</h2>

                    <div>
                        <h4><b>Product Name:</b>
                            <p style="color:#333; display: inline;">
                                <?php echo $products['product_name']; ?>
                            </p>
                        </h4>
                    </div>
                    <div>
                        <h4><b>Product Description:</b>
                            <p style="color:#333;">
                                <?php echo $products['product_description']; ?>
                            </p>
                        </h4>

                    </div>
                    <div>
                        <h4><b>Price:</b>
                            <p style="color:#333; display: inline;">₱
                                <?php echo $products['price']; ?>
                            </p>
                        </h4>

                    </div>
                    <div>
                        <h4><b>Category:</b>
                            <p style="color:#333; display: inline;">
                                <?php echo $products['category']; ?>
                            </p>
                        </h4>
                    </div>
                    <div>
                        <form method="post" action="">
                            <div id="dropdowns-container<?= $products['product_id'] ?>">
                                <table class="tableDefault">
                                    <?php
                                    $sql = "SELECT * FROM tblproducts_inventory PI 
                                                JOIN tblInventory I ON PI.inventory_id = I.inventory_id 
                                                WHERE products_id = $products[product_id]";
                                    $currentIngredients = $pdo->prepare($sql);
                                    $currentIngredients->execute();
                                    $currentIngredientsData = $currentIngredients->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    <tr class="tableDefault">
                                        <th class="tableDefault">Ingredients</th>
                                        <th class="tableDefault">Quantity</th>
                                    </tr>
                                    <tr class="tableDefault">
                                        <?php foreach ($currentIngredientsData as $currentIngredients) : ?>
                                            <?php if ($currentIngredients['quantity'] <= 0) : ?>
                                                <td class="tableDefault" style="background-color: rgba(255, 99, 71, 0.5);">
                                                    <?php echo $currentIngredients['inventory_item']; ?>
                                                </td>
                                                <td class="tableDefault" style="background-color: rgba(255, 99, 71, 0.5);">
                                                    <?php echo $currentIngredients['quantity']; ?>
                                                </td>
                                            <?php else : ?>
                                                <td class="tableDefault">
                                                    <?php echo $currentIngredients['inventory_item']; ?>
                                                </td>
                                                <td class="tableDefault">
                                                    <?php echo $currentIngredients['quantity']; ?>
                                                </td>
                                            <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                                </table>

                            </div>
                            <div class="form-group">
                                <input type="hidden" name="deleteProductIngredients" value="<?= $products['product_id'] ?>">
                            </div>
                            <button type="submit" name="reset_ingredients" class="button delete-button" style="width:100%;">Reset
                                Ingredients</button>
                        </form>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!--hidden product category form-->
    <div class="overlay" id="productCategory">
        <div class="overlay-content">
            <div class="info-box">
                <button id="closeProductForm" class="button delete-button">X</button>
                <h2>Product Categories</h2>
                <div class="form-group">
                    <table style="margin: auto;">
                        <?php foreach ($categoryProductData as $category) : ?>
                            <tr>
                                <td>
                                    <?= $category['category'] ?>
                                </td>
                                <td>
                                    <form method="post" action="">
                                        <input type="hidden" name="update_category_id" value="<?= $category['categoryProduct_id'] ?>">
                                        <button type="button" class="button edit-button" onclick="toggleEditCategoryForm('editCategory<?= $category['categoryProduct_id'] ?>')">✎</button>
                                        <input type="hidden" name="delete_category_id" value="<?= $category['categoryProduct_id'] ?>">
                                        <button type="submit" name="categoryDelete" class="button delete-button">✖</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="edit-form" id="editCategory<?= $category['categoryProduct_id'] ?>">
                                <td colspan="2">
                                    <form method="post" action="">
                                        <input type="hidden" name="update_category_id" value="<?= $category['categoryProduct_id'] ?>">
                                        <input type="text" name="update_productCategory" value="<?= $category['category'] ?>" required>
                                        <button type="submit" name="update_product" class="button edit-button">💾</button>
                                    </form>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
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

    <!--Manage Promos-->
    <div class="overlay" id="managePromos">
        <div class="overlay-content">
            <div class="info-box" style="width:fit-content; max-width:100%;">
                <button id="closeManagePromosBtn" class="button delete-button">X</button>
                <h2>Manage Promos</h2>
                <div class="form-group">
                    <table style="margin: auto;">
                        <tr>
                            <th>Promo Name</th>
                            <th>Promo Description</th>
                            <th>Promo Code</th>
                            <th>Value</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                        <?php foreach ($promosData as $promos) : ?>

                            <tr>

                                <td>
                                    <?= $promos['promoname'] ?>
                                </td>
                                <td>
                                    <?= $promos['promodesc'] ?>
                                </td>
                                <td>
                                    <?= $promos['promocode'] ?>
                                </td>
                                <td>
                                    <?= $promos['value'] ?>
                                </td>
                                <td>
                                    <?= $promos['startdate'] ?>
                                </td>
                                <td>
                                    <?= $promos['enddate'] ?>
                                </td>
                                <td>
                                    <form method="post" action="">
                                        <input type="hidden" name="update_promo_id" value="<?= $promos['promoid'] ?>">
                                        <button type="button" class="button edit-button" onclick="toggleEditCategoryForm('editPromo<?= $promos['promoid'] ?>')">✎</button>
                                        <input type="hidden" name="delete_promo_id" value="<?= $promos['promoid'] ?>">
                                        <button type="submit" name="promoDelete" class="button delete-button">✖</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="edit-form" id="editPromo<?= $promos['promoid'] ?>">
                                <td colspan="7">
                                    <form method="post" action="">
                                        <input style="width: auto; max-width:150px;" type="hidden" name="update_promo_id" value="<?= $promos['promoid'] ?>" required>
                                        <input style="width: auto; max-width:150px;" type="text" name="update_promoName" value="<?= $promos['promoname'] ?>" required>
                                        <textarea style="width: auto; max-width:150px;" name="update_promoDesc" cols="30" rows="3" required><?= $promos['promodesc'] ?></textarea>
                                        <input style="width: auto; max-width:150px;" type="text" name="update_promoCode" value="<?= $promos['promocode'] ?>" required>
                                        <input style="width: auto; max-width:150px;" type="number" step="0.01" name="update_promoValue" value="<?= $promos['value'] ?>" required>
                                        <input style="width: auto; max-width:150px;" type="date" name="update_promoStartDate" value="<?= $promos['startdate'] ?>" required>
                                        <input style="width: auto; max-width:150px;" type="date" name="update_promoEndDate" value="<?= $promos['enddate'] ?>" required>
                                        <button type="submit" name="update_promo" class="button edit-button">💾</button>
                                    </form>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <h2>Add New Promo</h2>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="new_promoName">Promo Name:</label>
                        <input type="text" class="form-control" name="new_promoName" placeholder="Enter promo name" required>
                        <label for="new_promoDesc">Promo Description:</label>
                        <textarea class="form-control" name="new_promoDesc" cols="30" rows="5" placeholder="Enter promo description" required></textarea>
                        <label for="new_promoCode">Promo Code:</label>
                        <input type="text" class="form-control" name="new_promoCode" placeholder="Enter promo code" required>
                        <label for="new_value">Value:</label>
                        <input type="number" step="0.01" class="form-control" name="new_value" placeholder="Enter value" required>
                        <label for="new_startDate">Start Date:</label>
                        <input type="date" class="form-control" name="new_startDate" placeholder="Enter start date" required>
                        <label for="new_endDate">End Date:</label>
                        <input type="date" class="form-control" name="new_endDate" placeholder="Enter end date" required>
                    </div>
                    <button type="submit" name="addPromo" class="button add-button" style="width:100%;">Add</button>
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
                <li><a href="Inventory.php">Inventory</a></li>
                <li style="background-color: var(--primary-color);"><a href="Products.php">Products</a></li>
                <li><a href="Staff.php">Staff</a></li>
                <li><a href="Reports.php">Reports</a></li>
                <li><a href="../POS front-end/Main.php">POS</a></li>
            </ul>
        </div>
        <div class="top-right">
            <a href="logout.php" class="login-button">Logout</a>
        </div>
        <div class="content">
            <h2>Products Listing
                <?php echo " (" . $_SESSION['username'] . " the " . $_SESSION['position'] . ") " ?>
            </h2>
            <div style="display: flex; justify-content: space-between;">
                <button type="button" class="button add-button" id="addForm">+ Add
                    Products</button>
                <button type="button" class="button add-button" id="managePromosBtn" style="margin-left: auto;">Manage
                    Promos</button>
            </div>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Product Discription</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Category<button type="button" id="categoryProduct" onclick="toggleForm()" style="background-color:transparent; border:none; padding:none;">⚙️</button>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productsData as $products) : ?>
                            <tr>
                                <td>
                                    <?= $products['product_name'] ?>
                                </td>
                                <td>
                                    <?= $products['product_description'] ?>
                                </td>
                                <td style="text-align:center;">
                                    <?= $products['price'] ?>
                                </td>
                                <?php if ($products['status'] == NULL) : ?>
                                    <td style="text-align:center;">
                                        <button type="button" class="button add-button" onclick="toggleIngredientForm('ingredientsForm<?= $products['product_id'] ?>')">Insert
                                            Ingredients</button>
                                    </td>
                                <?php else : ?>
                                    <td style="text-align:center;">
                                        <?php if ($products['status'] == "Available") : ?>
                                            <button type="button" class="button edit-button" onclick="toggleIngredientsList('ingredientsList<?= $products['product_id'] ?>')">
                                                <?= $products['status'] ?>
                                            </button>
                                        <?php else : ?>
                                            <button type="button" class="button delete-button" onclick="toggleIngredientsList('ingredientsList<?= $products['product_id'] ?>')">
                                                <?= $products['status'] ?>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

                                <td>
                                    <?= $products['category'] ?>
                                </td>
                                <td class="action-buttons" style="text-align:center;">
                                    <form method="post" action="" class="button-form">
                                        <input type="hidden" name="edit_product_id" value="<?= $products['product_id'] ?>">
                                        <button type="button" class="button edit-button" onclick="toggleEditFormy('editForm<?= $products['product_id'] ?>')">✎</button>
                                        <input type="hidden" name="delete_product_id" value="<?= $products['product_id'] ?>">
                                        <button type="submit" name="submit_delete" class="button delete-button">✖</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="edit-form" id="editForm<?= $products['product_id'] ?>">
                                <td colspan="6">
                                    <form method="post" action="">
                                        <input type="hidden" name="edit_product_id" value="<?= $products['product_id'] ?>">
                                        <input type="text" name="edited_product" placeholder="Edit Product Name" value="<?= $products['product_name'] ?>" required>
                                        <textarea name="edited_description" rows="2" cols="50" placeholder="Edit Product Description:" required><?= $products['product_description'] ?></textarea>
                                        <input type="number" step="0.01" name="edited_price" placeholder="Edit Price" value="<?= $products['price'] ?>" required>

                                        <!--select option for category-->
                                        <select name="edited_category" id="edited_category" required>
                                            <?php foreach ($categoryProductData as $category) : ?>
                                                <option value="<?= $category['category'] ?>" <?php echo ($products['category'] == $category['category']) ? 'selected' : ''; ?>>
                                                    <?php echo $category['category']; ?>
                                                </option>

                                            <?php endforeach; ?>
                                        </select>

                                        <button type="submit" name="submit_edit" class="button edit-button">💾</button>
                                    </form>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <br><br>
        </div>
    </div>
</body>

</html>