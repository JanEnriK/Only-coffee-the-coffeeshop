<?php require 'partials/head.php'; ?>
<?php require 'partials/nav.php'; ?>
<link href="css/chathead.css" rel="stylesheet">
<link href="css/table.css" rel="stylesheet">
<?php
if (isset($_SESSION['payment_errors'])) {
    echo "<script>alert('" . htmlspecialchars($_SESSION['alert_message']) . "');</script>";
    unset($_SESSION['alert_message']); // Clear the message after displaying it
}
//alert if the item has reached max stocks to be ordered
if (isset($_SESSION['alert_message'])) {
    echo "<script>alert('" . htmlspecialchars($_SESSION['alert_message']) . "');</script>";
    unset($_SESSION['alert_message']); // Clear the message after displaying it
}
// Check if the session variable for the order number is set
if (isset($_SESSION['orderSubmited']['ordernumber'])) {
    // Output the order number
    $orderNumber = $_SESSION['orderSubmited']['ordernumber'];
    echo '
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            window.onload = function() {
                Swal.fire({
                    title: "Order Placed!",
                    text: "Your order number is ' . $orderNumber . '",
                    icon: "success",
                    confirmButtonText: "OK"
                });
            };
        </script>
        ';
    // Clear the session variable to prevent showing the alert multiple times
    unset($_SESSION['orderSubmited']['ordernumber']);
}
?>

<style>
    .category-btn-checkbox {
        display: none;
        /* hide the checkbox */
        user-select: none;
    }

    .category-btn-label {
        display: inline-block;
        padding: 10px 20px;
        background-color: red;
        /* primary button color */
        color: #fff;
        cursor: pointer;
        user-select: none;
    }

    .category-btn-label.selected {
        background-color: #ff6f00;
        color: #080808;
        font-weight: bold;
        user-select: none;
        /* secondary button color */
    }

    /* Mark input boxes that gets an error on validation: */
    input.invalid {
        background-color: #ffdddd;
    }

    /* Hide all steps by default: */
    .tab {
        display: none;
    }

    button {
        background-color: #04AA6D;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        font-size: 17px;
        font-family: Raleway;
        cursor: pointer;
    }

    button:hover {
        opacity: 0.8;
    }

    #prevBtn {
        background-color: #bbbbbb;
    }

    /* Make circles that indicate the steps of the form: */
    .step {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #bbbbbb;
        border: none;
        border-radius: 50%;
        display: none;
        opacity: 0.5;
    }

    .step.active {
        opacity: 1;
    }

    /* Mark the steps that are finished and valid: */
    .step.finish {
        background-color: #04AA6D;
    }

    .quantity-input {
        display: inline-flex;
        align-items: center;
    }

    .quantity-control {
        display: flex;
        align-items: center;
    }

    .quantity-btn {
        width: 20px;
        /* Adjust the width of the buttons */
        height: 30px;
        /* Adjust the height of the buttons */
        font-size: 20px;
        cursor: pointer;
        border: 1px solid #ccc;
        /* Add border for better visibility */
        border-radius: 4px;
        /* Add some border radius for a rounded look */
        display: flex;
        /* Use flexbox */
        justify-content: center;
        /* Center the text horizontally */
        align-items: center;
        /* Center the text vertically */
    }

    .quantity-field {
        width: 50px;
        text-align: center;
        margin: 0 10px;
        /* Add some margin between the input field and buttons */
    }
</style>



<!-- Page Header Start -->
<div class="container-fluid page-header mb-5 position-relative overlay-bottom">
    <div class="d-flex flex-column align-items-center justify-content-center pt-0 pt-lg-5" style="min-height: 400px">
        <h1 class="display-4 mb-3 mt-0 mt-lg-5 text-white text-uppercase">Menu</h1>
    </div>
</div>
<!-- Page Header End -->

<!-- Menu Start -->
<div id="product-container" class="container-fluid pt-5">
    <div class="container" id="product-list">
        <div class="section-title">
            <h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">Menu & Pricing</h4>
            <h1 class="display-4">Competitive Pricing</h1>

            <div class="dashboard">
                <div class="content">
                    <div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Product Description</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Image</th>
                                </tr>
                            </thead>
                            <tbody id="tbl_body">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <script>
                // Fetch product data from the backend
                fetch('/get_products')
                    .then(response => response.json())
                    .then(products => {
                        const productContainer = document.getElementById('tbl_body');

                        // Loop through the products and display them
                        products.forEach(product => {
                            const productCard = document.createElement('tr');
                            // productCard.className = 'col-lg-4 col-md-6 mb-5';
                            // productCard.innerHTML = `
                            //                         <td><a href =/show_product?id=${product.product_id}>${product.product_name}</a></td>
                            //                         <td>${product.product_description}</td>
                            //                         <td>${product.price}</td>
                            //                         <td>${product.status}</td>
                            //                         <td><img height="100px" src="uploads/${product.image}" alt="${product.product_name}"></td>
                            //                     `;


                            if (product.status === "Available") {
                                // If the product is available, enable the link
                                productCard.innerHTML = `
                                <td><a href="/show_product?id=${product.product_id}">${product.product_name}</a></td>
                                <td>${product.product_description}</td>
                                <td>${product.price}</td>
                                <td>${product.status}</td>
                                <td><img height="100px" src="uploads/${product.image}" alt="${product.product_name}"></td>
                            `;
                            } else if (product.status === "Not Available") {
                                // If the product is not available, disable the link
                                productCard.innerHTML = `
                                <td><a style="color:#878787;" disabled>${product.product_name}</a></td>
                                <td>${product.product_description}</td>
                                <td>${product.price}</td>
                                <td>${product.status}</td>
                                <td><img height="100px" src="uploads/${product.image}" alt="${product.product_name}"></td>
                            `;
                            } else {
                                //display null products
                            }
                            productContainer.appendChild(productCard);
                            productContainer.appendChild(productCard);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            </script>
        </div>


    </div>
</div>

<!-- Menu End -->


<!-- Coffee Shop Cart section start -->
<div id="overlay"></div>

<div class="container">
    <!--Cart-Head Starts-->
    <div class="position-fixed rounded-circle bg-primary text-white p-3 cart-head" id="cartHead">
        <i class="fas fa-shopping-cart"></i>
    </div>
    <!--Cart-Head End-->

    <!-- Cart (Hidden) -->
    <div class="position-fixed bottom-0 right-0 m-3 cart bg-light border rounded w-50 h-75" id="cart" style="display: none;">
        <div class="cart-header bg-primary text-white p-2 rounded-top d-flex justify-content-between align-items-center">
            <h4 class="m-0">Your Cart</h4>
            <div class="ml-auto d-flex align-items-center">
                <button class="close-btn btn btn-sm btn-light" id="orderHistory"><i class="fas fa-history"></i></button>
                <button class="close-btn btn btn-sm btn-light ml-2" id="closeCart">&times;</button>
            </div>
        </div>
        <div class="cart-body p-3 w-30 h-75">
            <form>
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th style="width:30px;">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) : ?>
                            <tr>
                                <td colspan="4" class="text-center py-3">
                                    <div class="alert alert-info m-0" role="alert">
                                        NO CART ITEMS
                                    </div>
                                </td>
                            </tr>
                            <?php else :
                            $total = 0.0; // Initialize total variable
                            foreach ($_SESSION['cart'] as $key => $item) :
                                $id = $item['base_coffee_id'];
                                $prod = $db->query("SELECT product_id, 
                                product_name,
                                product_description, 
                                price,
                                SKU,
                                CONCAT(UCASE(SUBSTRING(category, 1, 1)), LOWER(SUBSTRING(category, 2))) AS category, 
                                image 
                                FROM tblproducts WHERE product_id = $id")->get();
                            ?>
                                <tr>
                                    <td><?= $item['base_coffee'] ?></td>
                                    <td><?= $prod[0]['price'] ?></td>
                                    <td>
                                        <div class="quantity-input">
                                            <button class="quantity-btn minus-btn" type="button" onclick="decrementQuantity(this)">-</button>
                                            <input style="width: 50px;" type="number" name="<?= $item['base_coffee_id'] ?>" value="<?= $item['quantity'] ?>" readonly data-stock-level="<?= $prod[0]['SKU'] ?>">
                                            <button class="quantity-btn plus-btn" type="button" onclick="incrementQuantity(this,<?= $prod[0]['price'] ?>)">+</button>
                                        </div>
                                    </td>
                                    <td data-base-coffee-id="<?= $item['base_coffee_id'] ?>" data-price="<?= $prod[0]['price'] ?>" data-quantity="<?= $item['quantity'] ?>">
                                        <button type="button" class="remove-item-btn btn btn-danger" name="remove_item" data-base-coffee-id="<?= $item['base_coffee_id'] ?>" value="">X</button>
                                    </td> <!-- Button to remove item -->
                                <?php
                                $total += ($prod[0]['price'] * $item['quantity']); // Accumulate price to total
                            endforeach;
                                ?>
                                <tr>
                                    <th>Total:</th>
                                    <td colspan="3">
                                        <input style="border:none; width:100px;" disabled id="total_order" type="float" value="<?= number_format($total, 2) ?>" step="2" readonly></input> php
                                    </td> <!-- Output total here -->
                                </tr>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) : ?>
                                <tr>
                                    <td colspan="4">
                                        <button type="button" class="btn btn-primary btn-block" id="checkoutBtn">Checkout</button>
                                    </td> <!-- Checkout button -->
                                </tr>
                            <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>


<!-- Order History Modal -->
<div id="orderHistoryModal" class="modal fade">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order History</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Content will be populated dynamically -->
                <div id="orderHistoryContent"></div>
            </div>
        </div>
    </div>
</div>


<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkoutModalLabel">Checkout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="content2">
                <form action="/store_cart" enctype="multipart/form-data" method="POST" id="checkoutForm">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="checkoutTableBody">

                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                    <div id="payment_details">
                        <h5 class="modal-title" id="checkoutModalLabel">Discounts</h5>
                        <input type="hidden" name="orderNumberDisplay" id="orderNumberDisplay" value="<?= $newOrder ?>">

                        <label for="discount_code">Discount: </label>
                        <input type="text" class="form-control" id="discount_code" name="discount_code" placeholder="Enter discount code:">
                        <div id="discount_error">
                            <!-- error message displays here -->
                        </div>
                        <button type="button" id="discountBtn" onclick="validateDiscountCode()">Apply discount</button>

                        <h5 class="modal-title" id="checkoutModalLabel">Payment Method</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="cashPayment" value="cash" checked>
                            <label class="form-check-label" for="cashPayment">
                                Cash
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="onlinePayment" value="online">
                            <label class="form-check-label" for="onlinePayment">
                                Online Payment
                            </label>
                        </div>
                        <!-- <div id="cashInput" class="mt-2">
                            <label for="cashAmount">Cash Amount:</label>
                            <input type="number" class="form-control" id="cashAmount" min="1" placeholder="Enter cash amount" required>
                            <div id="changeDue" class="mt-2"></div>
                        </div> -->
                        <div id="proofOfPaymentInput" class="mt-2" style="display: none;">
                            <label for="proofOfPayment">Reference Number:</label>
                            <input type="file" class="form-control" name="proofOfPayment" id="proofOfPayment" placeholder="Upload Prood of Payment" accept="image/jpeg,image/png,image/gif" required>
                            <?php if (isset($errors)) : ?>
                                <p><?= $errors ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="mt-2">
                            <button type="submit" id="transactButton" class="btn btn-success btn-block" onclick="return confirm('Are you sure you want to proceed with this transaction?');">Pay Now</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<!-- Chatbot section start -->

<div id="overlay"></div>

<div class="container">
    <!-- <div class="position-fixed bottom-0 end-0 p-2">
        <div class="chat-icon bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" id="toggleChat">
            <i class="fas fa-comment-alt"></i>
        </div>
    </div> -->
    <div class="chat-box bg-light border rounded position-fixed bottom-0 end-0" id="chatBot" style="display: none;">
        <div class="chat-header bg-primary text-white p-2 rounded-top d-flex justify-content-between align-items-center">
            <h4 class="m-0">Order your own coffee here</h4>
            <button class="close-chat-btn btn btn-link text-white">&times;</button>
        </div>
        <div class="chat-body p-3" id="chatBody">
            <form id="chatForm" action="/menu" method="POST">
                <!-- One "tab" for each step in the form: -->
                <div class="tab" id="categories">
                    <?php foreach ($productCategories as $categories) : ?>
                        <label id="<?= ucfirst($categories['category']) ?>" for="<?= $categories['category'] ?>" class="category-btn-label"><?= ucfirst($categories['category']) ?></label>
                        <input id="<?= $categories['category'] ?>" type="radio" name="category" class="category-btn-checkbox" value="<?= ucfirst($categories['category']) ?>">

                    <?php endforeach; ?>
                </div>
                <div class="tab" id="base-coffee">
                    <!-- Coffee Base -->
                </div>
                <div class="tab" id="size-con">
                    <!-- Sizes -->
                </div>
                <div style="overflow:auto;">
                    <div style="float:right;">
                        <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
                        <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
                    </div>
                </div>
                <!-- Circles which indicates the steps of the form: -->
                <div style="text-align:center;margin-top:40px;">
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chatbot section end-->



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
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css">


<!-- Template Javascript -->
<?php require "js/main.php"; ?>

<!-- Contact Javascript File -->
<script src="mail/jqBootstrapValidation.min.js"></script>
<script src="mail/contact.js"></script>

<script>
    //download Specific card as image
    function downloadIMG(orderNumber) {
        var divId = 'card_' + orderNumber;
        var element = document.getElementById(divId);

        // Temporarily remove buttons from the DOM
        var buttons = element.querySelectorAll('button');
        var buttonParents = []; // Store parent elements to re-append buttons later
        buttons.forEach(function(btn, index) {
            buttonParents[index] = btn.parentNode;
            btn.parentNode.removeChild(btn);
        });

        html2canvas(element, {
            onrendered: function(canvas) {
                var link = document.createElement('a');
                link.download = 'order_' + orderNumber + '.jpg'; // Specify the file extension as.jpg
                link.href = canvas.toDataURL("image/jpeg", 0.9); // Convert to JPEG with 90% quality
                link.click();
                console.log(canvas);

                // Re-append buttons to their original locations
                buttons.forEach(function(btn, index) {
                    buttonParents[index].appendChild(btn);
                });
            },
            useCORS: true,
            logging: true, // Optional: Enable logging for debugging
        });
    }

    //Open Orderhistory Modal
    document.getElementById('orderHistory').addEventListener('click', function() {
        // Close the cart modal
        document.getElementById('cart').style.display = 'none';
        var cartBlub = document.getElementById('cartHead');
        cartBlub.style.zIndex = 1040;

        // Open the order history modal
        $('#orderHistoryModal').modal('show');
        // Fetch order history from the server and populate #orderHistoryContent
        fetchOrderHistory();
    });

    function fetchOrderHistory() {
        fetch('fetch_history.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                let html = data.map(order => {
                    // Convert the order datetime to a Date object
                    let orderDate = new Date(order.order_datetime);
                    // Format the date
                    let formattedDate = orderDate.toLocaleString('en-US', {
                        weekday: 'long', // Monday
                        year: 'numeric', // 2024
                        month: 'long', // April
                        day: 'numeric', // 29
                        hour: 'numeric', // 9
                        minute: 'numeric', // 14
                        hour12: true, // Use 12-hour clock
                    });

                    return `
                            <div class="col-md-12">
                                <div class="card mb-3 rounded">
                                    <div class="card-body bg-white" id="card_${order.order_number}">
                                        <span class="d-flex justify-content-between">
                                        <h5 class="card-title pl-lg-2">Order Number: ${order.order_number}</h5>
                                        <h5 class="card-title pl-lg-2 pr-lg-2">No. of Items: ${order.record_count}</h5></span>
                                        <h6 class="card-title pl-lg-2"><b>Date Ordered:</b> ${formattedDate}</h6>
                                        <div id="items_${order.order_number}" style="display:none;"></div>
                                        <button type="button" id="btn_${order.order_number}" class="btn btn-primary btn-block" onclick="expandView(${order.order_number}, '${order.order_datetime}')">Expand View</button>
                                        <!-- Add more details as per your data structure -->
                                    </div>
                                </div>
                            </div>
                        `;
                }).join('');

                document.getElementById('orderHistoryContent').innerHTML = `<div class="row">${html}</div>`;
            })
            .catch(error => {
                console.error('Error fetching order history:', error);
            });
    }

    //Expand view of a order history card
    function expandView(orderNumber, orderDate) {
        const toggleButton = document.querySelector(`#btn_${orderNumber}`);
        const orderDetailsContainer = document.getElementById(`items_${orderNumber}`);

        if (toggleButton.textContent === 'Expand View') {
            // Expand view
            toggleButton.textContent = 'Contract';
            orderDetailsContainer.style.display = 'block'; // Show the content
            let url = `fetch_order_details.php?orderNumber=${orderNumber}&orderDate=${orderDate}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (Array.isArray(data) && data.length > 0) {
                        let overallTotal = data.reduce((acc, item) => acc + (parseFloat(item.quantity) * parseFloat(item.price)), 0);
                        overallTotal = overallTotal.toFixed(2);

                        // Calculate total discount and VAT for the entire order
                        let totalDiscount = data.reduce((acc, item) => item.discount * 100, 0);
                        let vatPercentage = data.reduce((acc, item) => item.VAT, 0);
                        let totalVAT = data.reduce((acc, item) => (item.VAT / 100) * overallTotal, 0);
                        overallTotal = overallTotal - (overallTotal * (totalDiscount / 100));

                        // Assuming status and order_datetime are properties of the first item for demonstration
                        let status = data[0].order_status;
                        if (status == "pending") {
                            status = "Pending online payment approval...";
                        } else if (status == "notpayed") {
                            status = "Pay at the store cashier.";
                        } else if (status == "canceled") {
                            status = "Canceled.There was no payment for this order.";
                        } else {
                            status = "Payed. Please pick up your order inside the store.";
                        }
                        let orderDate = new Date(data[0].order_datetime);
                        const options = {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric',
                            hour12: true
                        };
                        orderDate = orderDate.toLocaleString('en-US', options);
                        // Generate table rows
                        let details = `<table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Items</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.map(item => `
                            <tr>
                                <td>${item.product_name}</td>
                                <td>₱ ${parseFloat(item.price).toFixed(2)}</td>
                                <td>${item.quantity}</td>
                                <td>₱ ${(parseFloat(item.quantity) * parseFloat(item.price)).toFixed(2)}</td>
                            </tr>`).join('')}
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" style="text-align:right;"> Discount: </th>
                                <td>${totalDiscount}%</td>
                            </tr>
                            <tr>
                                <th colspan="3" style="text-align:right;"> VAT Percentage: </th>
                                <td>${vatPercentage}%</td>
                            </tr>
                            <tr>
                                <th colspan="3" style="text-align:right;"> Calculated VAT: </th>
                                <td>₱ ${totalVAT.toFixed(2)}</td>
                            </tr>
                            <tr>
                                <th colspan="3" style="text-align:right;">Grand Total: </th>
                                <td>₱ ${overallTotal}</td>
                            </tr>
                        </tfoot>
                    </table>
                    <h6><b>Status:</b> ${status}</h6>
                    <h6><b>Order Date:</b> ${orderDate}</h6>
                    <button type="button" class="btn btn-secondary mb-2 ml-auto d-flex" onclick="downloadIMG(${orderNumber})"><i class="bi bi-download"></i></button>`;
                        orderDetailsContainer.innerHTML = details;
                    } else {
                        console.error('No order details found');
                    }
                })
                .catch(error => {
                    console.error('Error fetching order details:', error);
                });
        } else {
            // Contract view
            toggleButton.textContent = 'Expand View';
            orderDetailsContainer.style.display = 'none'; // Hide the content
        }
    }


    //validate discount code entered
    function validateDiscountCode() {
        var discountCode = document.getElementById('discount_code').value.trim();
        var discountButton = document.getElementById('discountBtn');
        var discountInput = document.getElementById('discount_code');
        if (!discountCode) {
            document.getElementById('discount_error').innerText = 'Please enter a discount code.';
            return; // Exit if the input is empty
        }

        fetch('/validate_discount.php?code=' + encodeURIComponent(discountCode))
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    document.getElementById('discount_error').innerText = 'Valid Discount Code: ' + (data.value * 100) + '%';
                    // Apply the discount to the total
                    applyDiscount(data.value);
                    discountButton.disabled = true;
                    discountButton.style = "cursor:not-allowed; background-color: #ccc; color: #666;";
                    discountInput.disabled = true;
                } else {
                    document.getElementById('discount_error').innerText = data.message;
                }
            })
            .catch(error => console.error('Error validating discount code:', error));
    }

    function applyDiscount(discountPercentage) {
        var currentTotalElement = document.getElementById('overalltotal'); // Assuming 'total_order' is the ID of an element displaying the current total
        var currentTotal = parseFloat(currentTotalElement.textContent.replace(/[^0-9.]+/g, '')); // Strip non-numeric characters and convert to float
        var deductedAmount = currentTotal * discountPercentage; // Ensure discountPercentage is treated as a percentage
        var discountedTotal = currentTotal - deductedAmount;
        currentTotalElement.textContent = discountedTotal.toFixed(2);

        // Select the discount deducted cell
        var tbody = document.getElementById('checkoutTableBody');
        var trElements = tbody.querySelectorAll('tr');
        var fourthToLastIndex = trElements.length - 4;

        if (fourthToLastIndex >= 0) {
            var fourthToLastTr = trElements[fourthToLastIndex];
            var secondTd = fourthToLastTr.querySelectorAll('td')[1];

            // Update the secondTd content with the deducted amount, prefixed with a "-" sign
            secondTd.textContent = '-' + deductedAmount.toFixed(2); // Format deductedAmount as a string with "-" sign and 2 decimal places
        } else {
            console.log('Not enough rows to find the fourth-to-last row.');
        }
    }



    document.addEventListener('DOMContentLoaded', function() {
        const categoryCheckboxes = document.querySelectorAll('.category-btn-checkbox');
        const baseCoffeeContainer = document.getElementById('base-coffee');
        const ingredientsContainer = document.getElementById('prod_ingredients');

        const baseCoffeeOptions = <?= json_encode($products) ?>;
        // Function to update base coffee options based on selected categories
        function updateBaseCoffeeOptions() {
            const selectedCategories = Array.from(categoryCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);

            // Clear previous options
            baseCoffeeContainer.innerHTML = '';

            // Populate base coffee options for selected categories
            const filteredBaseCoffee = baseCoffeeOptions.filter(coffee => {
                return selectedCategories.includes(coffee.category);
            });

            filteredBaseCoffee.forEach(coffee => {
                const label = document.createElement('label');
                label.textContent = coffee.product_name;
                label.setAttribute('for', coffee.product_name.toLowerCase().replaceAll(' ', '-'));
                label.classList.add('category-btn-label');
                label.id = coffee.product_name.toLowerCase().replaceAll(' ', '_');

                const input = document.createElement('input');
                input.type = 'radio';
                input.name = 'base_coffee';
                input.classList.add('category-btn-checkbox');
                input.value = coffee.product_name.toLowerCase().replaceAll(' ', '_');
                input.id = coffee.product_name.toLowerCase().replaceAll(' ', '-');

                const input1 = document.createElement('input');
                input1.type = 'hidden';
                input1.name = 'order_type';
                input1.value = "take-out";
                input1.id = coffee.product_id;

                const input2 = document.createElement('input');
                input2.type = 'hidden';
                input2.name = 'base_coffee_id';
                input2.value = coffee.product_id;
                input2.id = coffee.product_id;

                const div = document.createElement('div');
                div.classList.add('btn-group');
                div.appendChild(label);
                div.appendChild(input);
                div.appendChild(input1);
                div.appendChild(input2);

                baseCoffeeContainer.appendChild(div);
            });

            $('input[name=base_coffee]').change(function() {
                if ($(this).is(':checked')) {
                    const amer = document.getElementById(this.value);
                    amer.classList.add("selected");
                    $('.category-btn-label').not('#' + this.value).removeClass('selected');
                }
            });
        }

        // Event listener for category checkboxes change
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBaseCoffeeOptions);
        });

        // Initially update base coffee options
        updateBaseCoffeeOptions();

        sizes = ["Small", "Medium", "Large"];
        const baseSizeContainer = document.getElementById('size-con');

        sizes.forEach(size => {
            const label = document.createElement('label');
            label.textContent = size;
            label.setAttribute('for', size);
            label.classList.add('category-btn-label');
            label.id = size.toLowerCase();;

            const input = document.createElement('input');
            input.type = 'radio';
            input.name = 'size';
            input.classList.add('category-btn-checkbox');
            input.value = size.toLowerCase();
            input.id = size;

            const div = document.createElement('div');
            div.classList.add('btn-group');
            div.appendChild(label);
            div.appendChild(input);

            baseSizeContainer.appendChild(div);

            $('input[name=size]').change(function() {
                if ($(this).is(':checked')) {
                    const amer = document.getElementById(this.value);
                    amer.classList.add("selected");
                    $('.category-btn-label').not('#' + this.value).removeClass('selected');
                }
            });

        });


        // Event listener for checkout button
        document.getElementById('checkoutBtn').addEventListener('click', function() {
            // Check stock levels before proceeding
            fetch('check_stock_levels.php')
                .then(response => response.json())
                .then(proceed => {
                    if (proceed) {
                        // Show the checkout modal
                        $('#checkoutModal').modal('show');

                        var discountBtn = document.getElementById('discountBtn');
                        var discountCodeInput = document.getElementById('discount_code');
                        var cartBlub = document.getElementById('cartHead');

                        discountBtn.disabled = false;
                        discountCodeInput.disabled = false;
                        discountBtn.style.cursor = "pointer";
                        discountBtn.style.backgroundColor = "#04AA6D";
                        discountBtn.style.color = "#ffffff";
                        cartBlub.style.zIndex = 1040;

                        // Hide the cart modal
                        document.getElementById('cart').style.display = 'none';

                        // Optionally, if there's an overlay that needs to be hidden as well, you can hide it too
                        document.getElementById('overlay').style.display = 'none';

                        var tableBody = document.getElementById('checkoutTableBody');
                        tableBody.innerHTML = ''; // Clear existing content

                        // Fetch the updated cart items from the server
                        fetch('get_cart_items.php')
                            .then(response => response.json())
                            .then(cartItems => {
                                let total = 0;

                                // Assuming cartItems is correctly populated from PHP and includes price
                                Object.keys(cartItems).forEach(function(key) {
                                    var item = cartItems[key];
                                    var prod = {
                                        product_name: item.base_coffee,
                                        price: parseFloat(item.price), // Ensure price is treated as a number
                                        quantity: parseInt(item.quantity) // Ensure quantity is treated as a number
                                    };

                                    var row = document.createElement('tr');

                                    var nameCell = document.createElement('td');
                                    nameCell.textContent = prod.product_name;
                                    row.appendChild(nameCell);

                                    var priceCell = document.createElement('td');
                                    priceCell.textContent = prod.price.toFixed(2); // Use price from the prepared data
                                    row.appendChild(priceCell);

                                    var quantityCell = document.createElement('td');
                                    quantityCell.textContent = prod.quantity;
                                    row.appendChild(quantityCell);

                                    var totalCell = document.createElement('td');
                                    totalCell.textContent = (prod.price * prod.quantity).toFixed(2); // Calculate total for the row
                                    row.appendChild(totalCell);

                                    tableBody.appendChild(row);
                                    total += prod.price * prod.quantity;
                                });

                                console.log('Total:', total);

                                // Calculate VAT 
                                var vatPercentage = <?= $vatPercentage; ?>;
                                const vatAmount = total * (vatPercentage / 100);

                                // Add VAT percentage and amount rows
                                const vatRow0 = createVatRow("Discount:", "- 0");
                                const vatRow1 = createVatRow("VAT Percentage:", vatPercentage + "%");
                                const vatRow2 = createVatRow("Calculated VAT:", `₱${vatAmount.toFixed(2)}`);

                                // Create a row for the total
                                const totalRow = document.createElement('tr');
                                const totalNameCell = document.createElement('td');
                                totalNameCell.textContent = 'Total:';
                                totalNameCell.setAttribute('colspan', '3'); // Span across 3 columns
                                totalNameCell.style.textAlign = "right";
                                totalNameCell.style.fontWeight = "bold";
                                const totalValueCell = document.createElement('td');
                                totalValueCell.textContent = `₱${total.toFixed(2)}`;
                                totalValueCell.setAttribute('id', 'overalltotal'); // Set the ID for styling or other purposes

                                totalRow.appendChild(totalNameCell);
                                totalRow.appendChild(totalValueCell);
                                tableBody.appendChild(vatRow0);
                                tableBody.appendChild(vatRow1); // Append the VAT percentage row
                                tableBody.appendChild(vatRow2); // Append the calculated VAT row
                                tableBody.appendChild(totalRow); // Append the total row to the table body

                                function createVatRow(label, value) {
                                    const row = document.createElement('tr');
                                    const labelCell = document.createElement('td');
                                    labelCell.textContent = label;
                                    labelCell.setAttribute('colspan', '3'); // Span across 3 columns
                                    labelCell.style.textAlign = 'right';
                                    labelCell.style.fontWeight = 'bold';
                                    const valueCell = document.createElement('td');
                                    valueCell.textContent = value;
                                    row.appendChild(labelCell);
                                    row.appendChild(valueCell);
                                    return row;
                                }
                            })
                            .catch(error => console.error('Error fetching cart items:', error));
                    } else {
                        alert('Transaction cannot proceed because an item in the cart is not available. Please try again');
                        location.reload();
                    }
                })
                .catch(error => console.error('Error checking stock levels:', error));
        });






    });

    // Function to toggle the visibility and disabled state of the proof of payment input
    function toggleProofOfPaymentInput() {
        var paymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
        var proofOfPaymentInput = document.getElementById('proofOfPaymentInput');
        var proofOfFileInput = document.getElementById('proofOfPayment');
        var buttonText = document.getElementById('transactButton');

        if (paymentMethod.value === 'online') {
            proofOfPaymentInput.style.display = 'block'; // Show the input when Online Payment is selected
            proofOfFileInput.disabled = false; // Enable the input
            buttonText.textContent = "Pay Now";
        } else {
            proofOfPaymentInput.style.display = 'none'; // Hide the input when Cash Payment is selected
            proofOfFileInput.disabled = true; // Disable the input
            buttonText.textContent = "Pay on Counter";
        }
    }

    // Attach the toggleProofOfPaymentInput function to changes on the payment method radio buttons
    document.getElementById('cashPayment').addEventListener('change', toggleProofOfPaymentInput);
    document.getElementById('onlinePayment').addEventListener('change', toggleProofOfPaymentInput);

    // Initial call to set the correct state on page load
    toggleProofOfPaymentInput();



    document.querySelectorAll('.remove-item-btn').forEach(button => {
        button.addEventListener('click', function() {
            const baseCoffeeId = this.getAttribute('data-base-coffee-id');
            removeItemFromCart(baseCoffeeId);
        });
    });

    function removeItemFromCart(baseCoffeeId) {
        fetch('/remove_cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    base_coffee_id: baseCoffeeId,
                }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Item removed successfully');
                const button = document.querySelector(`button[data-base-coffee-id="${baseCoffeeId}"]`);
                if (button) {
                    // Find the closest ancestor that is a table row (<tr>)
                    const row = button.closest('tr');
                    if (row) {
                        // Remove the row from the DOM
                        row.remove();
                    }
                    // Update total cost after removing the item
                    const newtotalInput = document.getElementById('total_order');
                    newtotalInput.value = calculateTotal();
                }

                // Check if cart is empty
                const cartIsEmpty = document.querySelectorAll('#cart tbody tr').length <= 2;
                if (cartIsEmpty) {
                    // Reload the page
                    location.reload();
                }
            })
            .catch((error) => {
                console.error('Error removing item:', error);
            });
    }




    function calculateTotal() {
        const items = document.querySelectorAll('#cart tbody tr');
        let total = 0.0;
        items.forEach(item => {
            const quantityInput = item.querySelector('input[type="number"]');
            if (quantityInput) {
                const price = parseFloat(item.querySelector('td:nth-child(2)').textContent);
                const quantity = parseInt(quantityInput.value);
                total += price * quantity;
            }
        });
        console.log('Total:', total.toFixed(2)); // Debugging statement
        return total.toFixed(2); // Round to two decimal places
    }


    function incrementQuantity(button) {
        const input = button.parentNode.querySelector('input');
        const currentValue = parseInt(input.value);
        const totalInput = document.getElementById('total_order');
        // Extract baseCoffeeId from the input's name attribute
        const baseCoffeeId = input.name;

        // Assuming the stock level is stored in a data attribute on the input element
        const stockLevel = parseInt(input.dataset.stockLevel); // Get the stock level from the data-stock-level attribute

        // Check if there is enough stock to increase the quantity
        if (currentValue < stockLevel) { // Only allow increment if the current value is less than the stock level
            input.value = currentValue + 1;
            totalInput.value = calculateTotal();

            // Update the session cart
            updateSessionCart(baseCoffeeId, currentValue + 1);
        } else {
            // Optionally, show an alert or notification that the stock level has been reached
            alert('Stock level reached for this product.');
        }
    }

    function decrementQuantity(button) {
        const input = button.parentNode.querySelector('input');
        const currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
            const totalInput = document.getElementById('total_order');
            // Extract baseCoffeeId from the input's name attribute
            const baseCoffeeId = input.name;
            totalInput.value = calculateTotal();

            // Update the session cart
            updateSessionCart(baseCoffeeId, currentValue - 1);
        }
    }

    function updateSessionCart(baseCoffeeId, newQuantity) {
        fetch('/update_cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    base_coffee_id: baseCoffeeId, // Ensure this matches the key expected by the server
                    quantity: newQuantity, // Ensure this matches the key expected by the server
                }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Cart updated successfully');
            })
            .catch((error) => {
                console.error('Error updating cart:', error);
            });
    }
</script>


<?php require 'partials/foot.php'; ?>