<?php
session_start();
if (!isset($_SESSION['position']) && !isset($_SESSION['username']) && !isset($_SESSION['employeeID'])) {
  header("Location: ../Dashboard/Login.php");
  exit(); //Ensures that the script stops executing after redirection
} elseif ($_SESSION['position'] != "admin" && $_SESSION['position'] != "cashier") {
  header("Location: ../Dashboard/Login.php");

  exit();
}

?>


<html>

<head>
  <title>POS System</title>
  <link rel="stylesheet" href="Main.css" />
  <link rel="stylesheet" href="../Dashboard/dashboard.css" />

</head>

<body>
  <nav>
    <h2>POS
      <?php echo " (" . $_SESSION['username'] . " the " . $_SESSION['position'] . ") " ?>
    </h2>
    <ul>
      <li><a href="#">Sales</a></li>
      <li><a href="#">+</a></li>
      <li><a href="#">Tunnel</a></li>
      <li><a href="#">Surf</a></li>
      <li><a href="#">Parties</a></li>
    </ul>

    <div class="search">
      <input type="Search" placeholder="Search..." />
    </div>
    <a href="../Dashboard/logout.php" class="login-button">Logout</a>
  </nav>

  <div class="sellables-container">
    <div class="sellables">
      <div class="categories">
        <a class="category active" href="Main.html">Brewed Coffee</a>
        <a class="category" href="Frappe.html">Frappe</a>
        <a class="category" href="Americano.html">Americano</a>
        <a class="category" href="Espresso.html">Espresso</a>
        <a class="category" href="Latte.html">Latte</a>
        <a class="category" href="Cappuccino.html">Cappuccino</a>
      </div>

      <div class="item-group-wrapper">
        <div class="item-group">
          <script>

            function displayProducts() {
              fetch('posConnect.php')
                .then(response => response.json())
                .then(data => {
                  const productList = document.getElementById('item-group-wrapper');


                  data.forEach(tblproducts => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${tblproducts.product_name} - php${tblproducts.price}`;
                    productList.appendChild(listItem);
                  });
                })
                .catch(error => console.error('Error:', error));
            }


            displayProducts();
          </script>
        </div>
      </div>
      <div class="register-wrapper">
        <div class="customer">
          <input type="text" placeholder="John Doe" />
        </div>

        <div class="register">
          <div class="products">
            <div class="product-bar selected">
              <span>Salted Caramel</span>
              <span>$5.00</span>
            </div>

            <div class="product-bar">
              <span>Dark Caramel</span>
              <span>$5.00</span>
            </div>

            <div class="product-bar">
              <span>Cookies</span>
              <span>$5.00</span>
            </div>
          </div>

          <div class="pay-button">
            <a href="#">Pay $50.00</a>
          </div>
        </div>
      </div>
    </div>
</body>

</html>