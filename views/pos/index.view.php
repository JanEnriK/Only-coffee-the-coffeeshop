<?php

use Core\App;
use Core\Database;

// Define $db before using it
$db = App::resolve('Core\Database');

require "partials/head.php";
require "partials/nav.php";
?>
<style>
  .disabled {
    pointer-events: none;
    /* Prevents all click, state and cursor options on the specified HTML element */
    opacity: 0.6;
    /* Makes the element appear dimmed */
    cursor: not-allowed;
    /* Changes the cursor to a "not-allowed" cursor when hovering over the element */
  }

  .pay-button {
    user-select: none;
  }
</style>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- Optional Bootstrap JS and jQuery (for Bootstrap's JavaScript components) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php
if (isset($_SESSION['orderSubmited']['ordernumber'])) {
  // Output the JavaScript code to display the SweetAlert notification
  echo '
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  document.addEventListener("DOMContentLoaded", function() {
      Swal.fire({
          title: "Order ' . $_SESSION['orderSubmited']['ordernumber'] . ' Transaction Complete!",
          icon: "success",
          confirmButtonText: "OK"
      });
  });
  </script>
  ';
  // Clear the session variable to prevent the alert from showing again on page reload
  unset($_SESSION['orderSubmited']['ordernumber']);
}
?>

<div class="sellables-container">
  <div class="sellables">
    <div class="categories">
      <?php
      $categories = $db->query("SELECT DISTINCT category from tblproducts WHERE 1")->get();
      foreach ($categories as $category) {
        echo '<a class="category" onclick="displayProductsForCategory(\'' . $category['category'] . '\')" style="user-select: none;">' . $category['category'] . '</a>';
      }
      ?>
    </div>
    <div class="item-group-wrapper">
      <div class="item-group" id="item-data">

      </div>
    </div>
  </div>

  <div class="register-wrapper">
    <div class="customer">
      <input type="text" value="<?php echo $_SESSION['user']['email'] . " the " . $_SESSION['user']['position'] ?>" readonly>
      </input>
    </div>

    <div class="register">
      <div class="products">

        <div class="product-bar ">
          <span>Product</span>
          <span>Price</span>
          <span>Quantity</span>
          <span>Button</span>
        </div>
      </div>
      <div id="total-display">Total: ₱0</div> <!-- Placeholder for the total -->
      <div class="pay-button">
        <a id="checkoutButton">Checkout</a>
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
        <form action="/onsite_payment" method="POST" id="checkoutForm">
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
              <!-- Product rows will be added here dynamically -->
            </tbody>
            <tfoot>
            </tfoot>
          </table>
          <div id="payment_details">
            <h5 class="modal-title" id="checkoutModalLabel">Discounts</h5>
            <input type="hidden" name="orderNumberDisplay" id="orderNumberDisplay" value="<?= $newOrder ?>">
            <label for="discount_code">Discount: </label>
            <select name="discount_code" id="discount_code">
              <option value=0>No discount</option>
              <?php foreach ($discount_codes as $discount) : ?>
                <option value="<?= $discount['value'] ?>">
                  <?= $discount['promoname'] ?>
                </option>
              <?php endforeach; ?>
            </select>
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
            <div id="cashInput" class="mt-2">
              <label for="cashAmount">Cash Amount:</label>
              <input type="number" class="form-control" id="cashAmount" min="1" placeholder="Enter cash amount" required>
              <div id="changeDue" class="mt-2"></div>
            </div>
            <div id="onlineInput" class="mt-2" style="display: none;">
              <label for="referenceNumber">Reference Number:</label>
              <input type="text" class="form-control" id="referenceNumber" placeholder="Enter reference number">
            </div>
            <div class="mt-2">
              <button type="submit" id="transactButton" class="btn btn-success btn-block" onclick="return confirm('Are you sure you want to proceed with this transaction?');">Transact</button>
              <button type="button" id="downloadBtn" class="btn btn-primary btn-block" onclick="downloadPDF('content2')">Print Invoice</button>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1/jquery.min.js"></script>
<script>
  //printing invoice

  function downloadPDF(containerId) {
    var container = document.getElementById(containerId);
    if (container) {
      var contentToPrint = container.cloneNode(true);

      // Extract values from the input boxes, dropdown, and radio buttons
      var orderNumber = contentToPrint.querySelector('#orderNumberDisplay').value;

      // Assuming contentToPrint is a clone of the original container
      var discountCodeElement = contentToPrint.querySelector('#discount_code');
      var originalSelect = document.querySelector('#discount_code');
      var selectedOption = originalSelect.options[originalSelect.selectedIndex];

      // Set the selected option on the cloned node
      discountCodeElement.textContent = selectedOption.textContent;

      // Now you can safely access the selected option from the cloned node
      var discountCode = discountCodeElement.textContent;

      var paymentMethod = contentToPrint.querySelector('input[name="paymentMethod"]:checked').value;
      var amountPaid = contentToPrint.querySelector('#cashAmount').value;
      var referenceNumber = contentToPrint.querySelector('#referenceNumber').value;
      var totalAmount = parseFloat(document.getElementById('overalltotal').textContent.replace('₱', ''));


      // Optionally, remove elements that should not appear in the invoice
      $(contentToPrint).find('.btn,.btn-block').remove();
      $(contentToPrint).find('#payment_details').remove();

      // Dynamically insert the extracted values into the contentToPrint
      // Create new elements to display the values outside of the original input elements
      var orderNumberDisplay = document.createElement('p');
      orderNumberDisplay.textContent = `Order Number: ${orderNumber}`;
      contentToPrint.insertBefore(orderNumberDisplay, contentToPrint.firstChild);


      var discountCodeDisplay = document.createElement('p');
      discountCodeDisplay.textContent = `Discount Code: ${discountCode}`;
      contentToPrint.appendChild(discountCodeDisplay);

      //display the payment method 

      var paymentMethodDisplay = document.createElement('p');
      paymentMethodDisplay.textContent = `Payment Method: ${paymentMethod}`;
      contentToPrint.appendChild(paymentMethodDisplay);


      var amountPaidDisplay = document.createElement('p');
      if (paymentMethod === 'cash') {
        amountPaid = parseFloat(amountPaid);
        amountPaidDisplay.textContent = `Amount Paid: ₱${amountPaid.toFixed(2)}`;
      } else {
        // For online payment, display the total amount
        amountPaidDisplay.textContent = `Amount Paid: ₱${totalAmount.toFixed(2)}`;
      }
      contentToPrint.appendChild(amountPaidDisplay);
      if (amountPaid > totalAmount) {
        var changeDueDisplay = document.createElement('p');
        var changeDue = amountPaid - totalAmount;
        changeDueDisplay.textContent = `Change Due: ₱${changeDue.toFixed(2)}`;
        contentToPrint.appendChild(changeDueDisplay);
      }

      var referenceNumberDisplay = document.createElement('p');
      if (paymentMethod === 'online') {
        referenceNumberDisplay.textContent = `Reference Number: ${referenceNumber}`;
      }
      contentToPrint.appendChild(referenceNumberDisplay);


      var printWindow = window.open('', '', 'height=800,width=800'); // Adjust window size for better layout
      printWindow.document.write('<html><head><title>Invoice</title>');
      printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
      printWindow.document.write('<style>');
      printWindow.document.write(`
    body { font-family: Arial, sans-serif; }
   .invoice-container { margin: 20px; padding: 20px; border: 1px solid #dee2e6; border-radius: 5px; }
   .invoice-header { text-align: center; margin-bottom: 20px; }
   .invoice-header h1 { margin: 0; }
   .invoice-header p { margin: 0; font-size: 14px; color: #6c757d; }
   .invoice-details { margin-bottom: 20px; }
   .invoice-details th,.invoice-details td { padding: 8px; border: 1px solid #dee2e6; }
   .invoice-details th { background-color: #f8f9fa; }
   .invoice-details tr:nth-child(even) { background-color: #f8f9fa; }
   .invoice-footer { text-align: center; margin-top: 20px; }
   .invoice-footer p { margin: 0; font-size: 14px; color: #6c757d; }
    `);
      printWindow.document.write('</style></head><body>');

      // Add a wrapper div for styling
      var invoiceWrapper = document.createElement('div');
      invoiceWrapper.className = 'invoice-container';

      // Add invoice header
      var invoiceHeader = document.createElement('div');
      invoiceHeader.className = 'invoice-header';
      invoiceHeader.innerHTML = '<h1>Invoice</h1>';

      invoiceWrapper.appendChild(invoiceHeader);

      // Add invoice details
      invoiceWrapper.appendChild(contentToPrint);

      // Add invoice footer
      var invoiceFooter = document.createElement('div');
      invoiceFooter.className = 'invoice-footer';
      invoiceFooter.innerHTML = '<h3>Thank you for choosing us!</h3>';
      invoiceWrapper.appendChild(invoiceFooter);

      printWindow.document.body.appendChild(invoiceWrapper);
      printWindow.document.body.innerHTML += '</body></html>';
      printWindow.document.close();
      printWindow.print();
    } else {
      console.error("Container element not found.");
    }
  }

  function checkProductCount() {
    const productBars = document.querySelectorAll('.product-bar');
    const checkoutAnchor = document.querySelector('.pay-button a');

    // Check if there are more than 1 product
    const hasProducts = productBars.length > 1;

    // Toggle the clickable state of the checkout anchor using CSS
    if (!hasProducts) {
      checkoutAnchor.textContent = 'No products';
      checkoutAnchor.style.color = 'white';
      checkoutAnchor.classList.add('disabled'); // Add a CSS class to disable the anchor
    } else {
      // Enable the anchor
      checkoutAnchor.textContent = 'Checkout';
      checkoutAnchor.style.color = 'initial';
      checkoutAnchor.classList.remove('disabled'); // Remove the CSS class to enable the anchor
    }
  }

  // Initial check when the page loads
  checkProductCount();

  // Update the checkout anchor state when products are added or removed
  document.addEventListener('DOMContentLoaded', function() {
    checkProductCount();
  });

  // Additional event listeners for adding or removing products
  document.addEventListener('click', function(event) {
    if (event.target.matches('.increaseBtn,.decreaseBtn')) {
      checkProductCount();
    }
  });

  // Event listeners for payment method radio buttons
  document.getElementById('cashPayment').addEventListener('change', updateTransactButtonState);
  document.getElementById('onlinePayment').addEventListener('change', updateTransactButtonState);

  function updateTransactButtonState() {
    const cashAmountInput = document.getElementById('cashAmount');
    const cashAmount = parseFloat(cashAmountInput.value);
    const totalAmount = parseFloat(document.getElementById('overalltotal').textContent.replace('₱', ''));
    const transactButton = document.getElementById('transactButton');
    const downloadButton = document.getElementById('downloadBtn');
    const cashPaymentSelected = document.getElementById('cashPayment').checked;
    const onlinePaymentSelected = document.getElementById('onlinePayment').checked;
    const referenceNumberInput = document.getElementById('referenceNumber');
    const referenceNumber = referenceNumberInput.value.trim();

    // Check if the cash payment method is selected and if the cash amount is less than the total
    // Also, check if the cash amount input is empty
    if (cashPaymentSelected && (isNaN(cashAmount) || cashAmount < totalAmount)) {
      transactButton.disabled = true;
      downloadButton.disabled = true;
      transactButton.title = 'Inputted amount is less than the total.';
      downloadButton.title = 'Inputted amount is less than the total.';
    } else if (onlinePaymentSelected && referenceNumber === '') {
      // Check if the online payment method is selected and if the reference number input is empty
      transactButton.disabled = true;
      downloadButton.disabled = true;
      transactButton.title = 'Reference number is required.';
      downloadButton.title = 'Reference number is required.';
    } else {
      // If neither of the above conditions are met, enable the Transact button
      transactButton.disabled = false;
      downloadButton.disabled = false;
      transactButton.title = '';
      downloadButton.title = '';
    }
  }

  //event listener for cash payment input box
  document.getElementById('cashAmount').addEventListener('input', function() {
    const cashAmount = parseFloat(this.value);
    const totalAmount = parseFloat(document.getElementById('overalltotal').textContent.replace('₱', ''));
    let changeDue = 0;

    if (cashAmount > totalAmount) {
      changeDue = cashAmount - totalAmount;
    }

    const changeDueDiv = document.getElementById('changeDue');
    if (changeDue > 0) {
      changeDueDiv.textContent = `Change Due: ₱${changeDue.toFixed(2)}`;
    } else {
      changeDueDiv.textContent = ''; // Clear the change due if the cash amount is less than or equal to the total
    }

    // Update the state of the Transact button based on the payment method and cash amount
    updateTransactButtonState();
  });

  // Event listener for the reference number input box
  document.getElementById('referenceNumber').addEventListener('input', updateTransactButtonState);


  //form handling inside modal
  document.addEventListener('DOMContentLoaded', function() {
    // Toggle payment method inputs
    const paymentMethods = document.querySelectorAll('input[name="paymentMethod"]');
    paymentMethods.forEach(method => {
      method.addEventListener('change', function() {
        const cashInput = document.getElementById('cashInput');
        const onlineInput = document.getElementById('onlineInput');
        const cashAmountInput = document.getElementById('cashAmount');
        const referenceNumberInput = document.getElementById('referenceNumber');
        if (this.id === 'cashPayment') {
          cashInput.style.display = 'block';
          onlineInput.style.display = 'none';
          cashAmountInput.required = true; // Make cash amount input required
          cashAmountInput.disabled = false; // Enable cash amount input
          referenceNumberInput.value = '';
          referenceNumberInput.required = false; // Make reference number input not required
          referenceNumberInput.disabled = true; // Disable reference number input
        } else if (this.id === 'onlinePayment') {
          cashInput.style.display = 'none';
          onlineInput.style.display = 'block';
          cashAmountInput.required = false; // Make cash amount input not required
          cashAmountInput.disabled = true; // Disable cash amount input
          cashAmountInput.value = '';
          referenceNumberInput.required = true; // Make reference number input required
          referenceNumberInput.disabled = false; // Enable reference number input
        }
      });
    });

    // Trigger the change event to set the initial state correctly
    document.querySelector('input[name="paymentMethod"]:checked').dispatchEvent(new Event('change'));


    let baseTotal = 0; // Initialize base total

    // Function to calculate and store the base total
    function calculateBaseTotal() {
      console.log('Calculating base total...');
      baseTotal = Object.values(existingProducts).reduce((total, productObj) => {
        const quantity = parseInt(productObj.quantitySpan.textContent);
        const price = parseFloat(productObj.product.price.replace('₱', ''));
        console.log('Quantity:', quantity, 'Price:', price);
        const productTotal = price * quantity;
        console.log('Product Total:', productTotal);
        return total + productTotal;
      }, 0);
      console.log('Base Total:', baseTotal);
    }


    // Add an event listener to the discount dropdown
    document.getElementById('discount_code').addEventListener('change', function() {
      // Recalculate the base total before applying the discount
      calculateBaseTotal();

      const discountValue = parseFloat(this.value); // Get the discount value
      console.log('Discount Value:', discountValue); // Debugging line

      // Calculate the discount based on the base total
      const discount = baseTotal * discountValue;
      console.log('Discount:', discount); // Debugging line
      const discountedTotal = baseTotal - discount;
      console.log('Discounted Total:', discountedTotal); // Debugging line

      // Update the total element with the discounted total
      document.getElementById('overalltotal').textContent = `₱${discountedTotal.toFixed(2)}`;

      // Check if a discount row already exists

      let discountRow = document.getElementById('discountRow');
      if (!discountRow) {
        // If not, add a new row to the table body showing the discount lessened
        discountRow = document.createElement('tr');
        discountRow.id = 'discountRow'; // Assign an ID to the row for future reference
        const discountNameCell = document.createElement('td');
        discountNameCell.textContent = 'Discount:';
        const discountValueCell = document.createElement('td');
        discountValueCell.textContent = `-₱${discount.toFixed(2)}`;
        discountNameCell.colSpan = 3; // Span across 3 columns

        discountRow.appendChild(discountNameCell);
        discountRow.appendChild(discountValueCell);
        document.getElementById('checkoutTableBody').appendChild(discountRow);
      } else {
        // If it exists, update the discount value
        const discountValueCell = discountRow.querySelector('td:last-child');
        discountValueCell.textContent = `-₱${discount.toFixed(2)}`;
      }
    });
  });


  //modal handling and intiantating the modal##########################################################################################
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.pay-button a').addEventListener('click', function(event) {
      event.preventDefault(); // Prevent the default action

      //initialize the selected dropdown for discount
      const discountDropdown = document.getElementById('discount_code');
      document.getElementById('discount_code').selectedIndex = 0;
      discountDropdown.dispatchEvent(new Event('change'));

      // Initialize the selected radio button to "Cash"
      const cashPaymentRadio = document.getElementById('cashPayment');
      cashPaymentRadio.checked = true; // Select the Cash payment method

      // Clear the content of the cash and reference number input boxes
      const cashAmountInput = document.getElementById('cashAmount');
      const referenceNumberInput = document.getElementById('referenceNumber');
      const cashInput = document.getElementById('cashInput');
      const onlineInput = document.getElementById('onlineInput');
      const transactButton = document.getElementById('transactButton');
      const downloadButton = document.getElementById('downloadBtn');
      cashAmountInput.value = ''; // Clear the cash amount input
      referenceNumberInput.value = ''; // Clear the reference number input
      cashInput.style.display = 'block';
      onlineInput.style.display = 'none';
      transactButton.disabled = true;
      downloadButton.disabled = true;

      cashAmountInput.required = true; // Make cash amount input required
      cashAmountInput.disabled = false; // Enable cash amount input
      referenceNumberInput.required = false; // Make reference number input not required
      referenceNumberInput.disabled = true; // Disable reference number input

      showCheckoutModal(); // Call the function to populate and show the modal
    });
  });

  //show the checkout modal
  function showCheckoutModal() {
    console.log(existingProducts); // Debug: Check the contents of existingProducts
    const modal = document.getElementById('checkoutModal');
    const tableBody = document.getElementById('checkoutTableBody');
    const tableFooter = document.querySelector('#checkoutForm tfoot'); // Target the table footer
    tableBody.innerHTML = ''; // Clear previous rows
    tableFooter.innerHTML = ''; // Clear previous rows

    let total = 0; // Initialize total to 0

    // Populate the table with product details
    Object.values(existingProducts).forEach(productObj => {
      const product = productObj.product; // Retrieve the product object
      const quantity = parseInt(productObj.quantitySpan.textContent);
      const price = parseFloat(product.price.replace('₱', ''));
      const rowTotal = price * quantity;

      const row = document.createElement('tr');
      const nameCell = document.createElement('td');
      nameCell.textContent = product.product_name;
      const priceCell = document.createElement('td');
      priceCell.textContent = `₱${price.toFixed(2)}`;
      const quantityCell = document.createElement('td');
      quantityCell.textContent = quantity;
      const totalCell = document.createElement('td');
      totalCell.textContent = `₱${rowTotal.toFixed(2)}`;

      row.appendChild(nameCell);
      row.appendChild(priceCell);
      row.appendChild(quantityCell);
      row.appendChild(totalCell);
      tableBody.appendChild(row);

      total += rowTotal; // Accumulate the total
      console.log('Total:', total);
    });

    // Calculate VAT (assuming 12% VAT)
    var vatPercentage = <?= $vatPercentage; ?>;
    const vatAmount = total * (vatPercentage / 100);

    // Add VAT percentage and amount rows
    const vatRow1 = createVatRow("VAT Percentage", vatPercentage + "%");
    const vatRow2 = createVatRow("Calculated VAT", `₱${vatAmount.toFixed(2)}`);

    // Create a row for the total
    const totalRow = document.createElement('tr');
    const totalNameCell = document.createElement('th');
    totalNameCell.textContent = 'Total';
    totalNameCell.setAttribute('colspan', '3'); // Span across 3 columns
    const totalValueCell = document.createElement('th');
    totalValueCell.textContent = `₱${total.toFixed(2)}`;
    totalValueCell.setAttribute('id', 'overalltotal'); // Set the ID for styling or other purposes

    totalRow.appendChild(totalNameCell);
    totalRow.appendChild(totalValueCell);
    tableFooter.appendChild(vatRow1); // Append the VAT percentage row
    tableFooter.appendChild(vatRow2); // Append the calculated VAT row
    tableFooter.appendChild(totalRow); // Append the total row to the table body

    console.log('Showing checkout modal'); // Debug: Verify the modal is being shown
    $('#checkoutModal').modal('show');
  }

  // Helper function to create a row with label and value
  function createVatRow(label, value) {
    const row = document.createElement('tr');
    const labelCell = document.createElement('th');
    labelCell.textContent = label;
    labelCell.setAttribute('colspan', '3'); // Span across 3 columns
    const valueCell = document.createElement('th');
    valueCell.textContent = value;
    valueCell.setAttribute('id', 'vat' + Math.random().toString()); // Unique ID for each VAT row
    row.appendChild(labelCell);
    row.appendChild(valueCell);
    return row;
  }
  // Get the <span> element that closes the modal
  const span = document.getElementsByClassName("close")[0];

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    document.getElementById('checkoutModal').style.display = "none";
  }

  // When the user clicks anywhere outside of the modal, close it
  window.onclick = function(event) {
    if (event.target == document.getElementById('checkoutModal')) {
      document.getElementById('checkoutModal').style.display = "none";
    }
  }
  //submitting the modal
  document.getElementById('checkoutForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Capture the current total
    const total = document.getElementById('overalltotal').textContent.replace('₱', '');

    // Create hidden input fields for the total
    const totalInput = document.createElement('input');
    totalInput.type = 'hidden';
    totalInput.name = 'total';
    totalInput.value = total;

    // Append the total input field to the form
    this.appendChild(totalInput);

    // Iterate over existingProducts to capture product IDs and quantities
    Object.values(existingProducts).forEach((productObj, index) => {
      const productId = productObj.product.product_id;
      const quantity = parseInt(productObj.quantitySpan.textContent);

      // Create hidden input fields for each product's ID and quantity
      const productIdInput = document.createElement('input');
      productIdInput.type = 'hidden';
      productIdInput.name = `productIds[${index}]`;
      productIdInput.value = productId;

      const quantityInput = document.createElement('input');
      quantityInput.type = 'hidden';
      quantityInput.name = `quantities[${index}]`;
      quantityInput.value = quantity;

      // Append the hidden input fields to the form
      this.appendChild(productIdInput);
      this.appendChild(quantityInput);
    });

    // Capture the value in cashAmount
    const cashAmount = document.getElementById('cashAmount').value;
    const cashAmountInput = document.createElement('input');
    cashAmountInput.type = 'hidden';
    cashAmountInput.name = 'cashAmount';
    cashAmountInput.value = cashAmount;

    // Append the cashAmount input field to the form
    this.appendChild(cashAmountInput);

    // Capture the value in referenceNumber
    const referenceNumber = document.getElementById('referenceNumber').value;
    const referenceNumberInput = document.createElement('input');
    referenceNumberInput.type = 'hidden';
    referenceNumberInput.name = 'referenceNumber';
    referenceNumberInput.value = referenceNumber;

    // Append the referenceNumber input field to the form
    this.appendChild(referenceNumberInput);

    // Submit the form
    this.submit();
    // Process the transaction here
    document.getElementById('checkoutModal').style.display = "none";
  });


  //--------------------------------------------------------------------------------
  // for side register products
  function displayProductsForCategory(category) {
    fetch('/pos_frontend/pos_connect?category=' + encodeURIComponent(category))
      .then(response => response.json())
      .then(data => {
        const productList = document.getElementById('item-data');
        productList.innerHTML = ''; // Clear previous products
        data.forEach(product => {
          // Check if the product status is not null
          if (product.status !== null) {
            const listItem = document.createElement('div'); // Change 'a' to 'div' for the wrapper
            listItem.setAttribute("class", "item-wrapper"); // Add a class for styling

            const innerItem = document.createElement('a'); // This is the actual item
            innerItem.setAttribute("class", "item");
            innerItem.textContent = product.product_name;
            const lineBreak = document.createElement('br');
            const productPrice = document.createElement('div');
            productPrice.textContent = `Price: ₱${product.price}`;

            // Check if the product status is "Not Available"
            if (product.status === "Not Available") {
              innerItem.style.backgroundColor = 'grey'; // Set background color to grey
              innerItem.style.pointerEvents = 'none'; // Make the element unclickable
              innerItem.style.cursor = 'default'; // Change cursor to default to indicate it's not clickable
              listItem.setAttribute('title', 'This product is unavailable'); // Set hover title on the wrapper
            } else {
              // If the product is available, make it clickable
              innerItem.addEventListener('click', function() {
                addToProductBar(product);
                checkProductCount();
              });
            }

            innerItem.appendChild(lineBreak);
            innerItem.appendChild(productPrice);
            listItem.appendChild(innerItem); // Append the inner item to the wrapper
            productList.appendChild(listItem); // Append the wrapper to the product list
          }
        });
      })
      .catch(error => console.error('Error:', error));
  }


  function calculateTotal() {
    const productBars = document.querySelectorAll('.product-bar');
    let total = 0;
    productBars.forEach(productBar => {
      const priceElement = productBar.querySelector('.price');
      const quantityElement = productBar.querySelector('.quantity');
      console.log('Price element:', priceElement); // Debugging line
      console.log('Quantity element:', quantityElement); // Debugging line
      if (priceElement && quantityElement) {
        const price = parseFloat(priceElement.textContent.replace('₱', ''));
        const quantity = parseInt(quantityElement.textContent);
        total += price * quantity;
      }
    });
    document.getElementById('total-display').textContent = `Total: ₱${total.toFixed(2)}`;
  }

  let existingProducts = {}; // Define existingProducts at the top level of your script

  function addToProductBar(product) {
    const productBar = document.querySelector('.products');
    const productId = product.product_id; // Assuming each product has a unique ID

    // Check if the product already exists
    if (existingProducts[productId]) {
      // Retrieve maxQuantitySpan for the existing product
      const maxQuantity = existingProducts[productId].maxQuantity;
      // Increase the quantity of the existing product
      increaseQuantity(existingProducts[productId].quantitySpan, maxQuantity);
    } else {
      // Add the product to the product bar
      const productDiv = document.createElement('div');
      productDiv.setAttribute("class", "product-bar");

      // Create spans for item name, price, and quantity
      const itemNameSpan = document.createElement('span');
      itemNameSpan.textContent = product.product_name;
      const priceSpan = document.createElement('span');
      priceSpan.textContent = `₱${product.price}`;
      priceSpan.setAttribute("class", "price");
      const quantitySpan = document.createElement('span');
      quantitySpan.textContent = '1';
      quantitySpan.setAttribute("class", "quantity");
      const maxQuantitySpan = document.createElement('span');
      maxQuantitySpan.textContent = product.SKU;
      maxQuantitySpan.style.display = 'none';

      // Store the entire product object and the quantity span for future reference
      // Adjusting the storage in existingProducts
      existingProducts[productId] = {
        product: product,
        quantitySpan: quantitySpan,
        maxQuantitySpan: maxQuantitySpan // Store maxQuantitySpan as well
      };
      console.log(existingProducts);

      // Create buttons for increasing and decreasing quantity
      // Inside addToProductBar function, after defining maxQuantitySpan
      const increaseBtn = document.createElement('button');
      increaseBtn.textContent = '+';
      increaseBtn.addEventListener('click', function() {
        // Ensure maxQuantitySpan is captured correctly here
        increaseQuantity(quantitySpan, maxQuantitySpan); // Pass maxQuantitySpan as well
      });
      const decreaseBtn = document.createElement('button');
      decreaseBtn.textContent = '-';
      decreaseBtn.addEventListener('click', function() {
        decreaseQuantity(quantitySpan);
      });

      // Create a button for removing the item
      const removeBtn = document.createElement('button');
      removeBtn.textContent = 'X';
      removeBtn.style.backgroundColor = 'red';
      removeBtn.style.color = 'white';
      removeBtn.addEventListener('click', function() {
        productDiv.remove();
        delete existingProducts[productId]; // Remove the product from the existingProducts object
        calculateTotal();
        checkProductCount();
      });

      // Append spans and buttons to the productDiv
      productDiv.appendChild(itemNameSpan);
      productDiv.appendChild(priceSpan);
      productDiv.appendChild(quantitySpan);
      productDiv.appendChild(increaseBtn);
      productDiv.appendChild(maxQuantitySpan);
      productDiv.appendChild(decreaseBtn);
      productDiv.appendChild(removeBtn);

      // Append the productDiv to the productBar
      productBar.appendChild(productDiv);
      calculateTotal();
    }
  }

  function increaseQuantity(quantitySpan, maxQuantitySpan) {
    const quantity = parseInt(quantitySpan.textContent);
    // Ensure maxQuantitySpan is correctly interpreted as a number
    const maxQuantity = parseInt(maxQuantitySpan.textContent);

    // Only increase the quantity if it's less than the maximum quantity
    if (quantity < maxQuantity) {
      quantitySpan.textContent = quantity + 1;
      console.log('Quantity increased'); // Debugging line
      calculateTotal();
    } else {
      alert('Maximum quantity reached for this product.');
      console.log('Maximum quantity reached'); // Debugging line
    }
  }

  function decreaseQuantity(quantitySpan) {
    const quantity = parseInt(quantitySpan.textContent);
    if (quantity > 1) {
      quantitySpan.textContent = quantity - 1;
    }
    console.log('Quantity decreased'); // Debugging line
    calculateTotal();
  }
</script>
<?php require "partials/foot.php"; ?>