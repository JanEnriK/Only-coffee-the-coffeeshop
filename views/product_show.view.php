<?php require 'partials/head.php'; ?>
<?php require 'partials/nav.php'; ?>
<link href="css/chathead.css" rel="stylesheet">
<link href="css/table.css" rel="stylesheet">

<style>
    .card {
        display: flex;
        flex-direction: column;
        height: 100%;
        /* Make sure the card can grow to fill the container */
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        /* Pushes the button to the bottom */
        flex-grow: 1;
        /* Allows the card-body to grow and fill the available space */
    }

    .card-img-top {
        width: 100%;
        height: 200px;
        /* Fixed height for images */
        object-fit: cover;
        /* Ensure images cover the area without stretching */
    }

    /* Optional: Style for disabled links */
    .disabled {
        opacity: 0.65;
        pointer-events: none;
    }
</style>
<!-- Page Header Start -->
<div class="container-fluid page-header mb-5 position-relative overlay-bottom">
    <div class="d-flex flex-column align-items-center justify-content-center pt-0 pt-lg-0" style="min-height: 90px">
        <!-- <h1 class="display-4 mb-3 mt-0 mt-lg-5 text-white text-uppercase">NAME OF THE COFFEE EXAMPLE</h1>
        <div class="d-inline-flex mb-lg-5">
            <p class="m-0 text-white"><a class="text-white" href="">Home</a></p>
            <p class="m-0 text-white px-2">/</p>
            <p class="m-0 text-white">NAME OF THE COFFEE EXAMPLE</p>
        </div> -->
    </div>
</div>
<!-- Page Header End -->


<!-- Menu Start -->
<!-- Product Image and Details Section -->
<div class="container product-section mt-5">
    <div class="row align-items-center">
        <!-- Product Image -->
        <div class="col-md-6">
            <!-- Fixed size for the image using Bootstrap's img-fluid class and custom styles -->
            <img src="uploads/<?= $product['image'] ?>" alt="Product Image" class="img-fluid" style="width: 300px; height: auto;">
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <h2><?= $product['product_name'] ?></h2>
            <p><?= $product['product_description'] ?></p>
            <p><strong>Price: ₱<?= $product['price'] ?></strong></p>

            <form action="/menu" method="POST">
                <input type="hidden" name="order_type" value="take-out">
                <input type="hidden" name="base_coffee_id" value="<?= $product['product_id'] ?>">
                <input type="hidden" name="base_coffee" value="<?= $product['product_name'] ?>">
                <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>
        </div>
        <!-- Product Details -->
    </div>
</div>
</br>


<div class="container product-suggestions mt-5">
    <h2 class="text-center">You May Also Like</h2>
    <br>
    <br>
    <div class="dashboard">
        <div class="content">
            <div class="row" id="productContainer">
                <!-- Products will be inserted here -->
            </div>
        </div>
    </div>
    <script>
        // Fetch product data from the backend
        fetch('/get_products')
            .then(response => response.json())
            .then(products => {
                const productContainer = document.getElementById('productContainer');

                // Loop through the products and display them as cards
                products.forEach(product => {
                    let viewProductLink = `<a href="/show_product?id=${product.product_id}" class="btn btn-primary btn-block">View Product</a>`;
                    if (product.status !== "Available") {
                        // If the product is not available, make the link unclickable and visually disabled
                        viewProductLink = `<a href="#" class="btn btn-primary btn-block disabled">View Product</a>`;
                    }
                    const productCard = document.createElement('div');
                    productCard.className = 'col-lg-3 col-md-6 col-sm-12 mb-4'; // Adjust grid classes as needed

                    productCard.innerHTML = `
                <div class="card h-100 rounded"> <!-- Ensure the card takes full height -->
                    <img src="uploads/${product.image}" class="card-img-top rounded-top" alt="${product.product_name}" style="height: 200px; object-fit: cover;">
                    <div class="card-body ">
                        <h5 class="card-title">${product.product_name}</h5>
                        <p class="card-text">${product.product_description}</p>
                        <p class="card-text"><small class="text-muted">₱ ${product.price}</small>
                        <p class="card-text"><small class="text-muted">${product.status}</small><br>
                        ${viewProductLink}
                    </div>
                </div>
            `;

                    productContainer.appendChild(productCard);
                });
            })
            .catch(error => console.error('Error:', error));
    </script>
</div>

<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>


<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="lib/tempusdominus/js/moment.min.js"></script>
<script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
<script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<!-- Template Javascript -->
<?php require "js/main.php"; ?>

<!-- Contact Javascript File -->
<script src="mail/jqBootstrapValidation.min.js"></script>
<script src="mail/contact.js"></script>

<?php require 'partials/foot.php'; ?>