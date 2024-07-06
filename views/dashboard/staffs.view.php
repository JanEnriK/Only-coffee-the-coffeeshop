<?php require "partials/head.php"; ?>
<?php require "partials/nav.php";
include "connect.php"; ?>

<link rel="stylesheet" href="/Dashboard/css/products.css">
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #F5F5DC;
    }


    table {
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
        background-color: #2473C0;
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
    }

    .button.edit-button {
        background-color: green;
        color: white;
    }

    .button.delete-button {
        background-color: #FF6347;
        color: white;
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
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        /* Ensure the overlay is on top */
    }
</style>

<script>
    function togglePassForm() {
        var x = document.getElementById("create_password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

    function togglePassEdit() {
        var x = document.getElementById("edit_pass");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
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
</script>
<div class="overlay" id="overlay">
    <div class="info-box">
        <button id="closeFormBtn" class="button delete-button">X</button>
        <h2>Create New Employee</h2>
        <form method="post" action="/admin_dashboard/staffs">
            <div class="form-group">
                <label for="create_firstname">First Name:</label>
                <input type="text" class="form-control" name="create_firstname" id="create_firstname" required>
            </div>
            <div class="form-group">
                <label for="create_lastname">Last Name:</label>
                <input type="text" class="form-control" name="create_lastname" id="create_lastname" required>
            </div>
            <div class="form-group">
                <label for="create_email">Email:</label>
                <input type="email" class="form-control" name="create_email" id="create_email" required>
            </div>
            <div class="form-group">
                <label for="create_position">Position:</label>
                <select name="create_position" id="create_position" class="form-control">
                    <option value="" selected disabled>Position:</option>
                    <option value="admin">Admin</option>
                    <option value="guest">Guest</option>
                </select>
            </div>
            <div class="form-group">
                <label for="create_hiredate">Hire Date:</label>
                <input type="date" class="form-control" name="create_hiredate" id="create_hiredate" required>
            </div>
            <div class="form-group">
                <label for="create_username">Username:</label>
                <input type="text" class="form-control" name="create_username" id="create_username" required>
            </div>
            <div class="form-group">
                <label for="create_password">Password:</label>
                <input type="password" class="form-control" name="create_password" id="create_password" required>
                <input type="checkbox" onclick="togglePassForm()"> show password
            </div>
            <button type="submit" name="create" onclick="return confirm('Are you sure you want to create this user?');">Create</button>

        </form>
    </div>
</div>

<div class="dashboard">
    <div class="content">
        <h2>Staff Information
            <?php echo " (" . $_SESSION['user']['email'] . " the " . $_SESSION['user']['position'] . ") " ?>
        </h2>
        <div>
            <button type="button" class="button add-button" id="addForm" onclick="toggleForm()">+ Add User</button>
        </div>
        <div>
            <div class="table-responsive">
                <!-- <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"> -->
                <table id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Position</th>
                            <th>Account Created</th>
                            <th>Username</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        // $servername = "localhost";
                        // $username = "root";
                        // $password = "";
                        // $dbname = "coffeeshop_db";

                        // // Create a database connection
                        // $conn = new mysqli($servername, $username, $password, $dbname);

                        // if ($conn->connect_error) {
                        //     die("Connection failed: " . $conn->connect_error);
                        // }

                        $sql = "SELECT * FROM tblemployees ORDER BY position";
                        $result = $conn->query($sql);

                        $conn->close();
                        while ($row = $result->fetch_assoc()) : ?>
                            <form action="/admin_dashboard/staffs" method="POST">
                                <tr>
                                    <td>
                                        <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                            <input type="text" name="firstname_<?php echo $row['employeeID']; ?>" value="<?php echo $row['firstname']; ?>" required>
                                        <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                            <?php echo $row['firstname']; ?>
                                        <?php else : ?>
                                            <?php echo $row['firstname']; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                            <input type="text" name="lastname_<?php echo $row['employeeID']; ?>" value="<?php echo $row['lastname']; ?>" required>
                                        <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                            <?php echo $row['lastname']; ?>
                                        <?php else : ?>
                                            <?php echo $row['lastname']; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                            <input type="text" name="email_<?php echo $row['employeeID']; ?>" value="<?php echo $row['email']; ?>" required>
                                        <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                            <?php echo $row['email']; ?>
                                        <?php else : ?>
                                            <?php echo $row['email']; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                            <select name="position_<?php echo $row['employeeID']; ?>" id="position_<?php echo $row['employeeID']; ?>" required>
                                                <option value="" disabled>Select Position:</option>
                                                <option value="admin" <?php echo ($row['position'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                                <option value="guest" <?php echo ($row['position'] == 'guest') ? 'selected' : ''; ?>>Guest</option>
                                            </select>
                                        <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                            <?php echo $row['position']; ?>
                                        <?php else : ?>
                                            <?php echo $row['position']; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                            <input type="date" name="hiredate_<?php echo $row['employeeID']; ?>" value="<?php echo $row['hiredate']; ?>" required>
                                        <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                            <?php echo $row['hiredate']; ?>
                                        <?php else : ?>
                                            <?php echo $row['hiredate']; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>
                                            <input type="text" name="username_<?php echo $row['employeeID']; ?>" value="<?php echo $row['username']; ?>" required>
                                        <?php elseif (isset($_POST['cancel']) && $_POST['cancel'] == $row['employeeID']) : ?>
                                            <?php echo $row['username']; ?>
                                        <?php else : ?>
                                            <?php echo $row['username']; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td style="display: inline-flex; width: 100%; gap:10px; text-align: center; justify-content: center;">
                                        <?php if (isset($_POST['edit']) && $_POST['edit'] == $row['employeeID']) : ?>

                                            <button type="submit" name="save" class="button edit-button" value="<?php echo $row['employeeID']; ?>" onclick="return confirm('Are you sure you want to save changes for this account?');">💾</button>
                            </form>
                            <form action="/admin_dashboard/staffs" method="POST" onsubmit="return confirm('Are you sure you want to save changes for this account?');">
                                <button type="submit" name="cancel" class="button delete-button" value="<?php echo $row['employeeID']; ?>">✖</button>
                            </form>
                        <?php elseif ($row['position'] === "admin" && $row['employeeID'] === "1") : ?>
                            <form action="/admin_dashboard/staffs" method="POST">
                                <button type="submit" name="edit" class="button edit-button" value="<?php echo $row['employeeID']; ?>" style="background-color:grey;" disabled>Super Admin</button>
                            </form>
                        <?php elseif ($row['position'] === "admin") : ?>
                            <form action="/admin_dashboard/staffs" method="POST">
                                <button type="submit" name="edit" class="button edit-button" value="<?php echo $row['employeeID']; ?>">✎</button>
                            </form>

                        <?php else : ?>
                            <form action="/admin_dashboard/staffs" method="POST">
                                <button type="submit" name="edit" class="button edit-button" value="<?php echo $row['employeeID']; ?>">✎</button>
                            </form>
                            <form action="/admin_dashboard/staffs" method="POST" onsubmit="return confirm('Are you sure you want to delete this account?');">
                                <button type="submit" name="delete" class="button delete-button" value="<?php echo $row['employeeID']; ?>">✖</button>
                            </form>
                        <?php endif; ?>
                        </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <!-- </form> -->
            </div>

        </div>
    </div>
</div>
</body>

</html>