<?php require "partials/head.php"; ?>
<?php require "partials/nav.php"; ?>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<style>
  .card-text {
    user-select: none;
    /* Prevent text selection */
    text-align: center;
  }

  .card:hover {
    border: solid 2px black;
    background: #34495e;
    color: white;
  }
</style>

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

<!-- Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/store_payment" method="POST" onsubmit="return confirm('Are you sure you want to proceed with this transaction?')">
        <div class="modal-body" id="content2">
          <p id="orderNumberDisplay"></p>
          <p id="userNameDisplay"></p>
          <table class="table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Sub total</th>
              </tr>
            </thead>
            <tbody id="productList">
              <!-- Products will be added here dynamically -->
            </tbody>
            <tfoot>
              <tr id="totalRow" style="display: none;">
                <th colspan="3" class="text-right"><strong>Total:</strong></th>
                <th id="totalAmount"></th>
              </tr>
            </tfoot>
          </table>

          <!-- section for proof of payment preview -->
          <div id="proofPreview" class="container mt-4">
            <h5>Uploaded Proof of Payment:</h5>
            <div class="row">
              <div class="col-md-6">
                <img class="img-fluid action-buttons border border-5 w3-hover-opacity mb-3" id="proofImg" alt="Proof of Payment " onclick="onClick(this)">
              </div>
            </div>
            <label for="inputReferenceNumber">Reference Number:</label>
            <input type="text" id="inputReferenceNumber" name="inputReferenceNumber">
            <button type="submit" name="action" value="approve" class="btn btn-primary btn-block">Approve</button>
            <button type="submit" name="action" value="decline" class="btn btn-danger btn-block">Decline</button>
          </div>

          <!-- image preview fullscreen modal -->
          <div id="modal01" class="w3-modal" onclick="this.style.display='none'">
            <span class="w3-button w3-hover-red w3-xlarge w3-display-topright">&times;</span>
            <div class="w3-modal-content w3-animate-zoom">
              <img id="img01" style="width:100%">
            </div>
          </div>

          <div id="payment_details">
            <h5 class="modal-title d-none" id="orderDetailsModalLabel">Discount</h5>
            <label for="discount_code" class="d-none">Discount: </label>
            <select name="discount_code" id="discount_code" class="d-none">
              <option value=0 selected>No discount</option>
              <?php foreach ($discount_codes as $discount) : ?>
                <option value="<?= $discount['value'] ?>">
                  <?= $discount['promoname'] ?>
                </option>
              <?php endforeach; ?>
            </select>
            <h5 class="modal-title" id="orderDetailsModalLabel">Payment</h5>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="paymentMethod" id="cashPayment" value="cash">
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
            <!-- Cash Payment Form -->
            <div id="cashPaymentForm" style="display: none;">
              <label for="amountPaid">Amount Paid:</label>
              <input type="number" id="amountPaid" name="amountPaid" min="0" required>
              <p id="changeDue"></p>
            </div>
            <!-- Online Payment Form -->
            <div id="onlinePaymentForm" style="display: none;">
              <label for="referenceNumber">Reference Number:</label>
              <input type="text" id="referenceNumber" name="referenceNumber" required>
            </div>
            <!-- Hidden fields for total, customer_id, and order_number -->
            <input type="hidden" id="totalAmountHidden" name="totalAmount">
            <input type="hidden" id="customerIdHidden" name="customerId">
            <input type="hidden" id="orderNumberHidden" name="orderNumber">
          </div>
          <div class="modal-footer" id="modalFoot">
            <button type="submit" id="transact" class="btn btn-secondary btn-block">TRANSACT</button>
            <button type="button" id="print" class="btn btn-primary btn-block" onclick="downloadPDF('content2')">Print Invoice</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
<script>
  function downloadPDF(containerId) {
    var container = document.getElementById(containerId);
    if (container) {
      var contentToPrint = container.cloneNode(true);

      // Extract values from the input boxes, dropdown, and radio buttons

      // Assuming contentToPrint is a clone of the original container
      var discountCodeElement = contentToPrint.querySelector('#discount_code');
      var originalSelect = document.querySelector('#discount_code');
      var selectedOption = originalSelect.options[originalSelect.selectedIndex];

      // Set the selected option on the cloned node
      discountCodeElement.textContent = selectedOption.textContent;

      // Now you can safely access the selected option from the cloned node
      var discountCode = discountCodeElement.textContent;

      var paymentMethod = contentToPrint.querySelector('input[name="paymentMethod"]:checked').value;
      var amountPaid = contentToPrint.querySelector('#amountPaid').value;
      var referenceNumber = contentToPrint.querySelector('#referenceNumber').value;
      var totalAmount = parseFloat(document.getElementById('totalAmount').textContent.replace('₱', ''));


      // Optionally, remove elements that should not appear in the invoice
      $(contentToPrint).find('.btn,.btn-block').remove();
      $(contentToPrint).find('#payment_details').remove();

      // Dynamically insert the extracted values into the contentToPrint
      // Create new elements to display the values outside of the original input elements

      var discountCodeDisplay = document.createElement('p');
      discountCodeDisplay.textContent = `Discount Code: ${discountCode}`;
      contentToPrint.appendChild(discountCodeDisplay);

      // display the payment method
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

  // Store the original total amount
  let originalTotalAmount = 0;

  // Function to update the original total amount
  function updateOriginalTotalAmount(totalAmount) {
    originalTotalAmount = totalAmount;
  }

  //sets the print pdf and transact button if it is clickable or not
  function updatePrintButtonState() {
    console.log("updatePrintButtonState called");

    var printButton = document.getElementById('print');
    var submitButton = document.getElementById('transact');

    var cashPaymentSelected = document.getElementById('cashPayment').checked;
    var amountPaidInput = document.getElementById('amountPaid');
    console.log("Cash Payment Selected:", cashPaymentSelected, "Amount Paid Input:", amountPaidInput.value); // Ensure this logs the value correctly

    if (cashPaymentSelected && amountPaidInput.value === '') {
      printButton.disabled = true;
      submitButton.disabled = true;
    } else {
      printButton.disabled = false;
      submitButton.disabled = false;
    }

    var onlinePaymentSelected = document.getElementById('onlinePayment').checked;
    var referenceNumberInput = document.getElementById('referenceNumber');
    console.log("Online Payment Selected:", onlinePaymentSelected, "Reference Number Input:", referenceNumberInput.value); // Ensure this logs the value correctly

    if (onlinePaymentSelected && referenceNumberInput.value === '') {
      printButton.disabled = true;
      submitButton.disabled = true;
    } else {
      printButton.disabled = false;
      submitButton.disabled = false;
    }
  }

  // Ensure this script runs after the DOM is fully loaded
  document.getElementById('cashPayment').addEventListener('change', updatePrintButtonState);
  document.getElementById('onlinePayment').addEventListener('change', updatePrintButtonState);
  document.getElementById('amountPaid').addEventListener('input', updatePrintButtonState);
  document.getElementById('referenceNumber').addEventListener('input', updatePrintButtonState);


  // Function to show or hide payment forms based on the selected payment method
  function updatePaymentForm() {
    const cashPaymentSelected = document.getElementById('cashPayment').checked;
    document.getElementById('cashPaymentForm').style.display = cashPaymentSelected ? 'block' : 'none';
    document.getElementById('onlinePaymentForm').style.display = cashPaymentSelected ? 'none' : 'block';

    // Set the required attribute based on the selected payment method
    const referenceNumberInput = document.getElementById('referenceNumber');
    if (cashPaymentSelected) {
      referenceNumberInput.required = false; // Not required for cash payment
      document.getElementById('amountPaid').disabled = false;
      document.getElementById('referenceNumber').disabled = true;

    } else {
      referenceNumberInput.required = true; // Required for online payment
      document.getElementById('amountPaid').disabled = true;
      document.getElementById('referenceNumber').disabled = false;
    }

    // Clear the amount paid input box
    document.getElementById('amountPaid').value = '';
    document.getElementById('referenceNumber').value = '';
    document.getElementById('changeDue').textContent = '';

    var printButton = document.getElementById('print');
    var submitButton = document.getElementById('transact');
    printButton.disabled = true;
    submitButton.disabled = true;
  }

  // Example function to calculate change due
  // This should be adjusted based on your actual total amount and the amount paid
  document.getElementById('amountPaid').addEventListener('input', function() {
    const totalAmount = parseFloat(document.getElementById('totalAmount').textContent);
    const amountPaid = parseFloat(this.value);
    const changeDue = amountPaid - totalAmount;
    document.getElementById('changeDue').textContent = `Change Due: ${changeDue.toFixed(2)}`;
  });

  // Listen for changes to the discount code dropdown
  document.getElementById('discount_code').addEventListener('change', function() {
    const discountValue = parseFloat(this.value);
    // Calculate the new total amount based on the original total amount
    const discountedTotal = originalTotalAmount * (1 - discountValue);
    const deductedAmount = originalTotalAmount - discountedTotal;
    const vatAmount = <?= $vatPercentage; ?>

    // Listen for changes to the payment method radio buttons
    document.getElementById('cashPayment').addEventListener('change', updatePaymentForm);
    document.getElementById('onlinePayment').addEventListener('change', updatePaymentForm);


    // Clear existing discount rows
    const discountRows = document.querySelectorAll('.discount-row');
    discountRows.forEach(row => row.remove());

    // Add new rows for the deducted amount and the new total
    const tbody = document.getElementById('productList');
    const deductedRow = document.createElement('tr');
    deductedRow.className = 'discount-row';
    deductedRow.innerHTML = `
    <td colspan="3" class="text-right"><strong>Discount:</strong></td>
    <td id="deductedAmount">${deductedAmount.toFixed(2)}</td>`;
    tbody.appendChild(deductedRow);

    const vatPercentage = document.createElement('tr');
    vatPercentage.className = 'discount-row';
    vatPercentage.innerHTML = `
    <td colspan="3" class="text-right"><strong>VAT Percentage:</strong></td>
    <td id="newTotalAmount">${vatAmount.toFixed(2)}%</td>`;
    tbody.appendChild(vatPercentage);

    const calculatedVAT = document.createElement('tr');
    calculatedVAT.className = 'discount-row';
    calculatedVAT.innerHTML = `
      <td colspan="3" class="text-right"><strong>Calculated VAT:</strong></td>
      <td id="newTotalAmount">${(originalTotalAmount.toFixed(2)*(vatAmount/100)).toFixed(2)}</td>`;
    tbody.appendChild(calculatedVAT);


    // Update the total amount in the DOM
    document.getElementById('totalAmount').textContent = discountedTotal.toFixed(2);

    // Update the totalAmountHidden field with the discounted total amount
    document.getElementById('totalAmountHidden').value = discountedTotal.toFixed(2);

    // Clear the amount paid input box
    document.getElementById('amountPaid').value = '';
  });

  // Trigger the updatePaymentForm function on page load to set the initial state
  updatePaymentForm();
  // Function to update the submit button based on the amount paid
  function updateSubmitButton() {
    // Get the total amount from the hidden input field
    const totalAmount = parseFloat(document.getElementById('totalAmountHidden').value);
    // Get the amount paid from the input field
    const amountPaid = parseFloat(document.getElementById('amountPaid').value);

    // Get the submit button and print pdf
    const submitButton = document.getElementById('transact');
    const printButton = document.getElementById('print');


    if (amountPaid >= totalAmount) {
      // Disable the submit button and set a hover message
      submitButton.disabled = false;
      printButton.disabled = false;
      submitButton.title = '';
      printButton.title = '';
    } else {
      // Enable the submit button and clear the hover message
      submitButton.disabled = true;
      printButton.disabled = true;
      submitButton.title = 'Amount paid cannot be lower than the total amount.';
      printButton.title = 'Amount paid cannot be lower than the total amount.';
    }
  }

  // Add an event listener for changes in the amount paid input box
  document.getElementById('amountPaid').addEventListener('input', updateSubmitButton);

  // Call the function initially to set the initial state of the submit button
  updateSubmitButton();

  // Function to update the required attribute of the amountPaid input based on the modal's visibility and payment method
  function updateAmountPaidRequired() {
    const amountPaidInput = document.getElementById('amountPaid');
    const modal = document.getElementById('orderDetailsModal');
    const cashPaymentSelected = document.getElementById('cashPayment').checked;

    // Check if the modal is currently displayed and if cash payment is selected
    if (modal.classList.contains('show') && cashPaymentSelected) {
      // If the modal is displayed and cash payment is selected, set the required attribute
      amountPaidInput.required = true;
    } else {
      // If the modal is not displayed or online payment is selected, remove the required attribute
      amountPaidInput.required = false;
    }
  }

  // Call the function initially to set the initial state of the amountPaid input
  updateAmountPaidRequired();

  // Listen for changes to the modal's visibility
  $('#orderDetailsModal').on('shown.bs.modal', function() {
    // When the modal is shown, update the required attribute
    updateAmountPaidRequired();
  });

  $('#orderDetailsModal').on('hidden.bs.modal', function() {
    // When the modal is hidden, update the required attribute
    updateAmountPaidRequired();
  });

  // Listen for changes to the payment method radio buttons
  document.getElementById('cashPayment').addEventListener('change', updateAmountPaidRequired);
  document.getElementById('onlinePayment').addEventListener('change', updateAmountPaidRequired);


  //script for opening image preview of proof of payment
  function onClick(element) {
    document.getElementById("img01").src = element.src;
    document.getElementById("modal01").style.display = "block";
  }
</script>

<!-- VISIBLE MAIN -->
<div class="sellables-container">
  <div class="sellables">

    <!-- Online Payment Approval Section -->
    <div class="container mt-5 border border-3px bg-light">
      <div class="ml-5 mr-5  p-4">
        <h2 class="text-center">For Online Payment Approval</h2>
        <div class="container">
          <?php if (empty($pendingOnlineOrders)) : ?>
            <div class="text-center my-4">
              <h1 style="font-size: 24px;">No current order for payment approval.</h1>
            </div>
          <?php else : ?>
            <div class="row">
              <?php foreach ($pendingOnlineOrders as $index => $order) : ?>
                <div class="col-md-3 mb-4">
                  <div class="card h-100">
                    <div class="card-body" onclick="handleItemClick('<?= $order['order_number']; ?>', '<?= $order['customer_id']; ?>', '<?= $order['order_status']; ?>')">
                      <p class="card-text"><strong>Order Number:</strong> <?= $order['order_number']; ?></p>
                      <p class="card-text"><strong>User Name:</strong> <?= $order['username']; ?></p>
                      <p class="card-text"><strong>Order Count:</strong> <?= $order['order_number_count']; ?></p>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Onsite Payment Processing Section -->
    <div class="container mt-5 border border-3px bg-light">
      <div class="ml-5 mr-5  p-4">
        <h2 class="text-center">For Onsite Payments</h2>
        <div class="container">
          <?php if (empty($online_orders)) : ?>
            <div class="text-center my-4">
              <h1 style="font-size: 24px;">No current order for onsite payments.</h1>
            </div>
          <?php else : ?>
            <div class="row">
              <?php foreach ($online_orders as $index => $order) : ?>
                <div class="col-md-3 mb-4">
                  <div class="card h-100">
                    <div class="card-body" onclick="handleItemClick('<?= $order['order_number']; ?>', '<?= $order['customer_id']; ?>', '<?= $order['order_status']; ?>')">
                      <p class="card-text"><strong>Order Number:</strong> <?= $order['order_number']; ?></p>
                      <p class="card-text"><strong>User Name:</strong> <?= $order['username']; ?></p>
                      <p class="card-text"><strong>Order Count:</strong> <?= $order['order_number_count']; ?></p>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>


  </div>
</div>
<?php require "partials/foot.php"; ?>
<!-- Template Javascript -->
<?php require "js/main.php"; ?>
<!-- Contact Javascript File -->
<script src="mail/contact.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
  //show overlay(modal) of that specific order
  function handleItemClick(orderNumber, customerId, status) {
    // Make AJAX request to fetch order details
    $.ajax({
      url: '/get_orders', // Adjust the URL as necessary
      type: 'POST',
      data: {
        orderNumber: orderNumber,
        customerId: customerId,
        status: status,
      },
      success: function(response) {
        // Parse the JSON response
        const orderDetails = JSON.parse(response);
        const discount = orderDetails.discount;
        const orderStatus = orderDetails.order_status;

        // Populate the modal with the order details
        $('#orderNumberDisplay').text('Order Number: ' + orderDetails.order_number);
        $('#userNameDisplay').text('User Name: ' + orderDetails.username);
        $('#productList').empty(); // Clear existing product rows

        let totalAmount = 0;
        if (orderDetails.products && Array.isArray(orderDetails.products)) {
          orderDetails.products.forEach(function(product) {
            const productTotal = product.price * product.quantity;
            totalAmount += productTotal;
            $('#productList').append(`
            <tr>
              <td>${product.product_name}</td>
              <td>${product.price}</td>
              <td>${product.quantity}</td>
              <td>${productTotal.toFixed(2)}</td>
            </tr>
          `);
          });
        } else {
          console.error("No products found or products data is not an array.");
        }

        //apply discount set to be deducted in total amount
        const deductedAmount = totalAmount * discount;
        totalAmount = totalAmount - deductedAmount;

        // Display the total amount
        $('#totalAmount').text(totalAmount.toFixed(2)); // Format to 2 decimal places
        $('#totalRow').show(); // Show the total row

        //update the total based on the order opened
        updateOriginalTotalAmount(totalAmount);

        //inital load of any order
        const discountDropdown = document.getElementById('discount_code');
        document.getElementById('discount_code').selectedIndex = 0;
        discountDropdown.dispatchEvent(new Event('change'));
        document.getElementById('amountPaid').value = '';
        document.getElementById('referenceNumber').value = '';
        document.getElementById('changeDue').textContent = '';
        const radioChecked = document.getElementById('cashPayment');
        document.getElementById('cashPayment').checked = true;
        radioChecked.dispatchEvent(new Event('change'));
        const amountPaidInput = document.getElementById('amountPaid');
        amountPaidInput.required = true;
        document.getElementById('deductedAmount').textContent = "- " + deductedAmount.toFixed(2);

        // Set the values for the hidden fields
        document.getElementById('totalAmountHidden').value = totalAmount.toFixed(2);
        document.getElementById('customerIdHidden').value = customerId;
        document.getElementById('orderNumberHidden').value = orderNumber;

        //if order is for online payment approval remove unecessary information and show the preview of the proof of payment and a button that approves or decline the payment
        if (orderStatus == "pending") {
          document.getElementById('modalFoot').style.display = 'none';
          document.getElementById('payment_details').style.display = 'none';
          document.getElementById('proofPreview').style.display = 'block'; // Show the message
          document.getElementById('proofImg').src = '/uploads/' + orderDetails.payment_proof;
          document.getElementById('amountPaid').disabled = true;
        } else if (orderStatus == "notpayed") {
          document.getElementById('modalFoot').style.display = 'block';
          document.getElementById('payment_details').style.display = 'block';
          document.getElementById('proofPreview').style.display = 'none'; // Hide the message
          document.getElementById('inputReferenceNumber').disabled = true;
          document.getElementById('amountPaid').disabled = false;
        }



        // Show the modal
        $('#orderDetailsModal').modal('show');
      },
      error: function(xhr, status, error) {
        console.error("AJAX Error:", status, error);
      }
    });
  }
</script>