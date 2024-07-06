<?php require "partials/head.php"; ?>
<?php require "partials/nav.php"; ?>
<?php include "connect.php"; ?>

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
        user-select: none;
    }

    .low-stock-container:hover {
        background-color: #fff;
        color: #ff6347;
        border: 1px solid;
        box-shadow: 2px 2px 2px #ff6347;
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
        width: 100%;
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
        background-color: #2473c0;
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
        background-color: #2473c0;
        color: white;
        margin-right: 5px;
    }

    .button.edit-button {
        background-color: green;
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

    // filing pilferage(shrinkage)
    document.addEventListener('DOMContentLoaded', function() {
        const filePilferageBtn = document.getElementById('filePilferageBtn');
        const filePilferageOverlay = document.getElementById('filePilferageOverlay');
        const closePilferageFormBtn = document.getElementById('closePilferageFormBtn');
        const body = document.body;

        // Initially hide the file pilferage form
        filePilferageOverlay.style.display = 'none';

        // Show the form when the button is clicked
        filePilferageBtn.addEventListener('click', function() {
            filePilferageOverlay.style.display = 'flex';
            body.style.overflow = 'hidden';
        });

        // Close the form when the close button is clicked
        closePilferageFormBtn.addEventListener('click', function() {
            filePilferageOverlay.style.display = 'none';
            body.style.overflow = 'visible';
        });
    });

    // adding shits
    document.addEventListener('DOMContentLoaded', function() {
        const addInventoryBtn = document.getElementById('addInventoryBtn');
        const addInventoryOverlay = document.getElementById('addInventoryOverlay');
        const closeAddInventoryFormBtn = document.getElementById('closeAddInventoryFormBtn');
        const selectSupplyItem = document.getElementById('supply_item');
        const lowStockOverlay = document.getElementById('lowStockOverlay');
        const unitIncrease = document.getElementById('unit_increase');
        const body = document.body;

        // Initially hide the add inventory form
        addInventoryOverlay.style.display = 'none';

        // Function to close the Low Stock modal and open the Add Inventory modal with a pre-selected option
        function handleAddSupplyClick(event) {
            if (event.target.id.startsWith('supply_')) {
                const inventoryId = event.target.dataset.id; // Get the inventory_id from the data-id attribute
                lowStockOverlay.style.display = 'none'; // Close the Low Stock modal
                addInventoryOverlay.style.display = 'flex'; // Open the Add Inventory modal
                document.body.style.overflow = 'hidden'; // Prevent scrolling on the body

                // Find the option in the dropdown that matches the inventory_id and set it as selected
                const options = selectSupplyItem.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].getAttribute('data-id-increase') === inventoryId) {
                        selectSupplyItem.selectedIndex = i;
                        break;
                    }
                }

                // Update the unit name display based on the selected option
                const selectedOption = selectSupplyItem.options[selectSupplyItem.selectedIndex];
                if (selectedOption && selectedOption.hasAttribute('unit-name-increase')) {
                    const unitName = selectedOption.getAttribute('unit-name-increase');
                    unitIncrease.textContent = "Unit: " + unitName;
                }
            }
        }

        // Attach the event listener to the Low Stock modal
        lowStockOverlay.addEventListener('click', handleAddSupplyClick);

        // Show the form when the button is clicked
        addInventoryBtn.addEventListener('click', function() {
            addInventoryOverlay.style.display = 'flex';
            body.style.overflow = 'hidden';
        });

        // Close the form when the close button is clicked
        closeAddInventoryFormBtn.addEventListener('click', function() {
            addInventoryOverlay.style.display = 'none';
            body.style.overflow = 'visible';

        });
    });



    // low stock modal
    document.addEventListener('DOMContentLoaded', function() {
        const lowStockBtn = document.getElementById('lowStockContainer');
        const lowStockOverlay = document.getElementById('lowStockOverlay');
        const closelowStockOverlay = document.getElementById('closelowStockOverlay');
        const body = document.body;

        // Initially hide the low stock modal
        lowStockOverlay.style.display = 'none';

        // Show the form when the button is clicked
        lowStockBtn.addEventListener('click', function() {
            lowStockOverlay.style.display = 'flex';
            body.style.overflow = 'hidden';
        });

        // Close the form when the close button is clicked
        closelowStockOverlay.addEventListener('click', function() {
            lowStockOverlay.style.display = 'none';
            body.style.overflow = 'visible';
        });
    });

    //alert add
    function confirmAdd() {
        return confirm("Are you sure you want to add this inventory?");
    }


    //alert update all inventory
    function confirmUpdate() {
        return confirm("Are you sure you want to apply this update to all inventory items?");
    }


    //alert delete
    function confirmDelete() {
        return confirm("Are you sure you want to delete this inventory item?");
    }

    //alert update current inventory
    function confirmEdit() {
        return confirm("Are you sure you want to edit this inventory item?");
    }

    //setting the inventory item id for hidden input and shows the unit of that current selected inventory for filing filperage
    document.addEventListener('DOMContentLoaded', function() {
        const selectElement = document.getElementById('new_item');
        const hiddenInput = document.getElementById('item_id');
        const unitSpan = document.getElementById('unit'); // Reference to the <span> element

        selectElement.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const selectedItemId = selectedOption.getAttribute('data-id'); // Get the inventory_id from the data-id attribute
            const unitName = selectedOption.getAttribute('unit-name'); // Get the unit name from the unit-name attribute

            // Update the hidden input field with the selected item's ID
            hiddenInput.value = selectedItemId;

            // Update the <span> element with the unit name
            unitSpan.textContent = "Unit: " + unitName; // Update the content of the <span> element
        });

        // Optionally, trigger the change event on page load to set the initial value
        selectElement.dispatchEvent(new Event('change'));
    });

    //setting the inventory item id for hidden input and shows the unit of that current selected inventory for adding supply
    document.addEventListener('DOMContentLoaded', function() {
        const selectElement = document.getElementById('supply_item');
        const hiddenInput = document.getElementById('item_id_increase');
        const unitSpan = document.getElementById('unit_increase');

        selectElement.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const selectedItemId = selectedOption.getAttribute('data-id-increase');
            const unitName = selectedOption.getAttribute('unit-name-increase');

            console.log('Selected Item ID:', selectedItemId); // Debugging line
            console.log('Unit Name:', unitName); // Debugging line

            hiddenInput.value = selectedItemId;
            unitSpan.textContent = "Unit: " + unitName;
        });

    });
</script>

<body>
    <!-- low stock modal -->
    <div class="overlay" id="lowStockOverlay">
        <div class="overlay-content">
            <div class="info-box">
                <button id="closelowStockOverlay" class="button delete-button">X</button>
                <h2>Low Stock Inventory</h2>
                <table>
                    <tr>
                        <th>Inventory Item</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $sql = "SELECT * FROM tblinventory WHERE status = 'Low Stock'";
                    $statement = $pdo->prepare($sql);
                    $statement->execute();
                    $lowStock = $statement->fetchAll(PDO::FETCH_ASSOC);
                    if (!empty($lowStock)) {
                        foreach ($lowStock as $row) : ?>
                            <tr>
                                <td><?= $row['inventory_item'] ?></td>
                                <td><?= $row['item_type'] ?></td>
                                <td><?= $row['quantity'] ?></td>
                                <td><?= $row['unit'] ?></td>
                                <td><button id="supply_<?= $row['inventory_id'] ?>" type="button" value="<?= $row['inventory_id'] ?>" data-id="<?= $row['inventory_id'] ?>">Add Supply</button></td>
                            </tr>
                        <?php endforeach;
                    } else { ?>
                        <tr>
                            <td style="text-align: center;" colspan="5">No current inventory marked as low stock.</td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Filing a filperage -->
    <div class="overlay" id="filePilferageOverlay">
        <div class="overlay-content">
            <div class="info-box">
                <button id="closePilferageFormBtn" class="button delete-button">X</button>
                <h2>File Pilferage</h2>
                <form method="post" action="/admin_dashboard/inventory" id="addInventoryForm">
                    <div class="form-group">
                        <!-- Hidden input to store the inventory_id -->
                        <input type="hidden" name="item_id" id="item_id" value="">

                        <label for="new_item">Inventory Item:</label>
                        <select name="new_item" class="form-control" id="new_item" required>
                            <option value="" selected disabled>Inventory Item:</option>
                            <?php foreach ($inventoryData as $item) : ?>
                                <option value="<?= $item['inventory_item'] ?>" data-id="<?= $item['inventory_id'] ?>" unit-name="<?= $item['unit'] ?>">
                                    <?= $item['inventory_item'] ?> <!-- Display the inventory_item -->
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="width: 50%; display: inline-block;">
                        <label for="new_quantity">Quantity to reduce: </label>
                        <input type="number" class="form-control" name="new_quantity" placeholder="Quantity" required>
                    </div>
                    <span style="color: grey; font-size: small;" id=unit></span>
                    <div class="form-group">
                        <p style="color: black"><label for="reason">Reason: </label></p>
                        <textarea name="reason" required></textarea>
                    </div>
                    <button type="submit" name="submit_file" id="addButton" class="button add-button" style="width:100%;">File</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Adding shit in inv -->
    <div class="overlay" id="addInventoryOverlay">
        <div class="overlay-content">
            <div class="info-box">
                <button id="closeAddInventoryFormBtn" class="button delete-button">X</button>
                <h2>Add Inventory</h2>
                <form method="post" action="/admin_dashboard/inventory" id="addInventoryForm">
                    <div class="form-group">
                        <input type="hidden" name="item_id_increase" id="item_id_increase" value="">
                        <label for="new_item">Inventory Item:</label>
                        <select name="supply_item" class="form-control" id="supply_item" required>
                            <option value="" selected disabled>Inventory Item:</option>
                            <?php foreach ($inventoryData as $item) : ?>
                                <option value="<?= $item['inventory_item'] ?>" data-id-increase="<?= $item['inventory_id'] ?>" unit-name-increase="<?= $item['unit'] ?>">
                                    <?= $item['inventory_item'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="width: 50%; display: inline-block;">
                        <label for="new_quantity">Quantity to add in supply: </label>
                        <input type="number" class="form-control" name="new_quantity" placeholder="Quantity" required>
                    </div>
                    <span style="color: grey; font-size: small;" id="unit_increase"></span>
                    <button type="submit" name="submit_add_item" id="addInventoryButton" class="button add-button" style="width:100%;">Add</button>
                </form>
            </div>
        </div>
    </div>

    <!--hidden add inventory form-->
    <div class="overlay" id="overlay">
        <div class="overlay-content">
            <div class="info-box">
                <button id="closeFormBtn" class="button delete-button">X</button>
                <h2>Add New Inventory</h2>
                <form method="post" action="/admin_dashboard/inventory" id="addInventoryForm" onsubmit="return confirmAdd()">
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
                    <div class="form-group">
                        <label for="new_reorder_point">Re-Order Point: </label>
                        <input type="text" class="form-control" name="new_reorder_point" placeholder="Re-Order Point:" required>
                    </div>
                    <button type="submit" name="submit_add" id="addButton" class="button add-button" style="width:100%;">Add</button>
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
                <form method="post" action="/admin_dashboard/inventory" id="updateInventoryForm" onsubmit="return confirmUpdate()">
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
                    <button type="submit" name="submit_update_all" id="applyUpdateButton" class="button add-button" style="width:100%;">Apply Update</button>
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
                                    <form method="post" action="/admin_dashboard/inventory" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        <input type="hidden" name="update_category_id" value="<?= $category['categoryInventory_id'] ?>">
                                        <button type="button" class="button edit-button" onclick="toggleEditCategoryForm('editCategory<?= $category['categoryInventory_id'] ?>')">✎</button>
                                        <input type="hidden" name="delete_category_id" value="<?= $category['categoryInventory_id'] ?>">
                                        <button type="submit" name="categoryDelete" class="button delete-button">✖</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="edit-form" id="editCategory<?= $category['categoryInventory_id'] ?>">
                                <td colspan="2">
                                    <form method="post" action="" onsubmit="return confirm('Are you sure you want to change this category?');">
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
                <form method="post" action="/admin_dashboard/inventory" onsubmit="return confirm('Are you sure you want to add this category?');">
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
        <div class="content">
            <h2>Inventory
                <?php echo " (" . $_SESSION['user']['email'] . " the " . $_SESSION['user']['position'] . ") " ?>
            </h2>

            <div>
                <div class="containertab">
                    <div class="stock-container total-products-container" id="totalProductsContainer">
                        <h4><i class="fa fa-shopping-cart"></i> Total Inventory Items</h4>
                        <p>
                            <?php echo $totalProducts; ?>
                        </p>
                    </div>

                    <div class="stock-container low-stock-container" id="lowStockContainer">
                        <h4><i class="fa fa-exclamation-triangle"></i> Low Stock Items</h4>
                        <p>
                            <?php echo !empty($lowStockData) ? $lowStockData[0]['lowStock'] : 0; ?>
                        </p>
                    </div>

                    <!-- <div class="stock-container out-of-stock-container" id="outofStockContainer">
                        <h4><i class="fa fa-ban"></i> Out of Stock</h4>
                        <p>
                            // echo !empty($outOfStockData) ? $outOfStockData[0]['out_of_stock'] : 0; ?>
                        </p>
                    </div> -->

                    <div class="stock-container most-stock-container" id="mostStockContainer">
                        <h4><i class="fa fa-check-circle"></i> Most Stock</h4>
                        <p>
                            <?php echo !empty($mostStockData) ? $mostStockData[0]['most_stock'] : 0; ?>
                        </p>
                    </div>
                </div>
                <br>
                <div style="display: flex; justify-content: space-between;">
                    <button type="button" class="button add-button" id="addForm">+ Add
                        Inventory</button>

                    <button type="button" class="button edit-button" id="addInventoryBtn">+ Add Supply</button>
                    <button type="button" class="button delete-button" id="filePilferageBtn" style="margin-left: auto;">🗑️ File Shrinkage</button>
                    <!-- <button type="button" class="button add-button" id="updateInventoryBtn" style="margin-left: auto;">Update All
                    Inventory</button> -->
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
                                <th>Re-Order Point</th>
                                <th>Status</th>
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
                                    <td>
                                        <?= $item['reorder_point'] ?>
                                    </td>
                                    <td>
                                        <?= $item['status'] ?>
                                    </td>

                                    <td class="action-buttons">
                                        <form method="post" action="/admin_dashboard/inventory" class="button-form" onsubmit="return confirmDelete()">
                                            <input type="hidden" name="edit_item_id" value="<?= $item['inventory_id'] ?>">
                                            <button type="button" class="button edit-button" onclick="toggleEditForm('editForm<?= $item['inventory_id'] ?>')">✎</button>
                                            <input type="hidden" name="delete_item_id" value="<?= $item['inventory_id'] ?>">
                                            <button type="submit" name="submit_delete" class="button delete-button">✖</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="edit-form" id="editForm<?= $item['inventory_id'] ?>">
                                    <td colspan="7">
                                        <form method="post" action="/admin_dashboard/inventory" onsubmit="return confirmEdit()">
                                            <input type="hidden" name="edit_item_id" value="<?= $item['inventory_id'] ?>">
                                            <input type="text" name="edited_item" placeholder="Edit Item" value="<?= $item['inventory_item'] ?>" required>
                                            <select name="edited_type" id="edited_type" required>
                                                <?php foreach ($categoryInventoryData as $category) : ?>
                                                    <option value="<?= $category['inventory_category'] ?>" <?php echo ($item['item_type'] == $category['inventory_category']) ? 'selected' : ''; ?>>
                                                        <?= $category['inventory_category'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <!-- <input type="number" name="edited_quantity" placeholder="Edit Quantity" value="// $item['quantity'] " disabled> -->
                                            <input type="text" name="edited_unit" placeholder="Unit measurement" value="<?= $item['unit'] ?>" required>
                                            <input type="text" name="edited_reorder_point" placeholder="Reorder Point" value="<?= $item['reorder_point'] ?>" required>
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