<?php require "partials/head.php"; ?>
<?php require "partials/nav.php"; ?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #ffff;
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
    }

    th {
        background-color: #4caf50;
        color: #fff;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .info-box {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-top: 5em;
    }

    .info-box h4 {
        margin-bottom: 10px;
    }

    .info-box p {
        margin: 0;
        color: #333;
    }

    /*edit button style*/
    .button {
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .button.edit-button {
        background-color: #008CBA;
        color: white;
    }

    .button.delete-button {
        background-color: #FF6347;
        color: white;
    }

    .button.add-button {
        background-color: #4CAF50;
        color: white;
        margin-right: 5px;
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

    .overlay-content::-webkit-scrollbar {
        display: none;
    }

    .overlay-content {
        max-height: 100%;
        /* Adjust maximum height as needed */
        max-width: 100%;
        /* Adjust maximum width as needed */
        overflow-y: auto;
    }
</style>
<script>
    // toggle edit coffeeshop form
    document.addEventListener('DOMContentLoaded', function() {
        const editForm = document.getElementById('editInfo');
        const overlay = document.getElementById('editOverlay');
        const closeFormBtn = document.getElementById('closeFormBtn');
        const body = document.body;
        // Initially hide the overlay form
        overlay.style.display = 'none';

        // Show the overlay form when the button is clicked
        editForm.addEventListener('click', function() {
            overlay.style.display = 'flex';
            body.style.overflow = 'hidden';
        });

        // Close the overlay form when the close button is clicked
        closeFormBtn.addEventListener('click', function() {
            overlay.style.display = 'none';
            body.style.overflow = 'visible';
        });
    });
</script>
<!-- form edit coffeeshop overlay-->
<div class="overlay" id="editOverlay">
    <div class="overlay-content">
        <div class="info-box">
            <button id="closeFormBtn" class="button delete-button">X</button>
            <h2>Edit Coffeeshop Info</h2>
            <?php foreach ($coffeeshopData as $coffeeshop) : ?>
                <form method="post" action="" onsubmit="return confirm('Are you sure you want to save?');">
                    <input type="hidden" class="form-control" name="editId" value="<?= $coffeeshop['coffeeshopid'] ?>">
                    <div class="form-group">
                        <label for="editShopName">Coffee Shop Name:</label>
                        <input type="text" class="form-control" name="editShopName" value="<?= $coffeeshop['shopname'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editBranch">Branch:</label>
                        <input type="text" class="form-control" name="editBranch" value="<?= $coffeeshop['branch'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editAddress">Address: </label>
                        <input type="text" class="form-control" name="editAddress" value="<?= $coffeeshop['address'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editContact">Contact Number: </label>
                        <input type="number" class="form-control" name="editContact" value="<?= $coffeeshop['contact_no'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email: </label>
                        <input type="email" class="form-control" name="editEmail" value="<?= $coffeeshop['email'] ?>" required>
                    </div>
                    <h2>Edit Value Added Tax(VAT)</h2>
                    <div class="form-group">
                        <label for="editVAT">VAT(in %):</label>
                        <input type="number" class="form-control" name="editVAT" min="0.01" step="0.01" max="100" placeholder="0.01% - 100.00%" value="<?= $coffeeshop['VAT'] ?>" required>
                    </div>
                    <button type="submit" name="submit_edit" class="button edit-button" style="width:100%;">💾Save</button>
                </form>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</div>
<div class="dashboard">
    <div class="content">
        <h2>Coffee Shop Information
            <?php echo " (" . $_SESSION['user']['email'] . " the " . $_SESSION['user']['position'] . ") " ?>
        </h2>
        <?php foreach ($coffeeshopData as $coffeeshop) : ?>
            <div class="info-box d-flex flex-column position-relative">
                <button type="button" class="btn btn-primary edit-button position-absolute top-0 end-0 m-3" id="editInfo">✎ Edit</button>
                <h2>General Coffee Shop Information</h2>
                <div class="info-item">
                    <h4><b>CoffeeShop Name:</b></h4>
                    <p><?php echo $coffeeshop['shopname']; ?></p>
                </div>
                <div class="info-item">
                    <h4><b>Branch:</b></h4>
                    <p><?php echo $coffeeshop['branch']; ?></p>
                </div>
                <div class="info-item">
                    <h4><b>Address:</b></h4>
                    <p><?php echo $coffeeshop['address']; ?></p>
                </div>
                <div class="info-item">
                    <h4><b>Contact Number:</b></h4>
                    <p><?php echo $coffeeshop['contact_no']; ?></p>
                </div>
                <div class="info-item">
                    <h4><b>Email:</b></h4>
                    <p><?php echo $coffeeshop['email']; ?></p>
                </div>
                <h2>Value Added Tax(VAT)</h2>
                <div class="info-item">
                    <h4><b>VAT(in %):</b></h4>
                    <p><?php echo $coffeeshop['VAT']; ?></p>
                </div>
            </div>
            <br><br><br><br>
        <?php endforeach; ?>
    </div>
</div>

</body>

</html>