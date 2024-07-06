<?php require "partials/head.php"; ?>
<?php require "partials/nav.php"; ?>

    <div class="sellables-container">
      <div class="sellables">
      <?php require "partials/nav2.php"; ?>

        <div class="item-group-wrapper">
          <div class="item-group" id="item-data">
          </div>
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

    <script>
    function displayProducts() {
    fetch('/pos_frontend/pos_connect')
        .then(response => response.json())
        .then(data => {
          const productList = document.getElementById('item-data');
          data.forEach(tblproducts => {
            if (tblproducts.category === 'latte') {
              const listItem = document.createElement('a');
              listItem.setAttribute("class", "item");
              listItem.textContent = `${tblproducts.product_name} - php${tblproducts.price}`;
              productList.appendChild(listItem);
            }
          });
        })
        .catch(error => console.error('Error:', error));
    }

    displayProducts();
    </script>

<?php require "partials/foot.php"; ?>
