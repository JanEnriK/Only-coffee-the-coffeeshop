<?php
session_start();

// Database connection
$servername = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "coffeeshop";

try {
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}

// Sales report 
if (isset($_GET['get_sales_data'])) {
  try {
    $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

    $query = "SELECT * FROM tblpayment WHERE 1";


    if ($filter === 'cash' || $filter === 'card') {
      $query .= " AND paymenttype = :payment_type";
    }

    if ($startDate && $endDate) {
      $query .= " AND DATE(order_datetime) BETWEEN :start_date AND :end_date";
    }

    $stmt = $pdo->prepare($query);


    if ($filter === 'cash') {
      $stmt->bindParam(':payment_type', 'Cash', PDO::PARAM_STR);
    } elseif ($filter === 'card') {
      $stmt->bindParam(':payment_type', 'Credit Card', PDO::PARAM_STR);
    }

    if ($startDate && $endDate) {
      $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
      $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
    }

    $stmt->execute();
    $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($salesData);
    exit();
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

// Inventory report 
if (isset($_GET['get_inventory_data'])) {
  try {
    $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

    $query = "SELECT * FROM tblinventory WHERE 1";

    switch ($filter) {
      case 'low':
        $query .= " AND quantity < 10 ORDER BY quantity ASC";
        break;
      case 'high':
        $query .= " AND quantity >= 10 ORDER BY quantity DESC";
        break;

      default:
        break;
    }

    $stmt = $pdo->prepare($query);

    $stmt->execute();
    $inventoryData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($inventoryData);
    exit();
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

// Feedback report 
if (isset($_GET['get_feedback_data'])) {
  try {
    $query = "SELECT * FROM tblfeedback";
    $stmt = $pdo->prepare($query);

    $stmt->execute();
    $feedbackData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($feedbackData);
    exit();
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sales and Inventory Report</title>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"
    integrity="sha384-9BqRVXKuH4lVVzPBgS9yYQFkWDaQb1e3n9MSs+tk+ctMZhR6IkaSLb7X0PFVfsrD"
    crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"
    integrity="sha512-ekgFwLfK6GnFbM0xqpWmR0Fb/6N8DykGepb8aQFb+U0U4lSbTX6VFBwXwo6qOqN2Y6yr9k+3cIrPyw31XqUx8A=="
    crossorigin="anonymous"></script>


  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      text-align: center;
      padding: 20px;
    }

    .report-btn {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      cursor: pointer;
      margin-right: 10px;
    }

    .search-filter {
      display: none;
      margin-bottom: 10px;
    }

    .report-container {
      display: none;
      margin-top: 20px;
    }

    .report-table {
      width: 80%;
      margin: 20px auto;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .report-table th,
    .report-table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    .report-table th {
      background-color: #4CAF50;
      color: white;
    }

    .download-btn {
      display: block;
      background-color: #008CBA;
      color: white;
      border: none;
      padding: 10px 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin-top: 10px;
      cursor: pointer;
    }

    .search-filter input,
    .search-filter select {
      padding: 8px;
      margin-right: 10px;
      width: 200px;
    }
  </style>

</head>

<body>

  <button class="report-btn" onclick="showReport('sales')">Show Daily Sales Report</button>
  <button class="report-btn" onclick="showReport('inventory')">Show Inventory Report</button>
  <button class="report-btn" onclick="showReport('feedback')">Show Feedback Report</button>

  <div id="salesReportContainer" class="report-container">
    <div class="search-filter">
      <label for="quantityFilter">Filter by Payment Type:</label>
      <select id="filterDropdownSales">
        <option value="">All</option>
        <option value="card">Card</option>
        <option value="cash">Cash</option>
      </select>
      <label for="startDateSales">Start Date:</label>
      <input type="date" id="startDateSales">
      <label for="endDateSales">End Date:</label>
      <input type="date" id="endDateSales">
      <button onclick="searchSales()">Search</button>
    </div>
    <table id="salesTable" class="report-table">
      <thead>
        <tr>
          <th>Payment ID</th>
          <th>Order Date Time</th>
          <th>Amount Paid</th>
          <th>Payment Type</th>
          <th>Customer ID</th>
          <th>Order ID</th>
        </tr>
      </thead>
      <tbody id="tableBodySales"></tbody>
    </table>
    <button class="download-btn" data-report-type="sales" onclick="downloadPDF('sales')">Download as PDF</button>
  </div>

  <div id="inventoryReportContainer" class="report-container">
    <div class="search-filter">
      <label for="quantityFilter">Filter by Quantity:</label>
      <select id="quantityFilterInventory" onchange="filterTable('inventory')">
        <option value="">All</option>
        <option value="low">Low Quantity</option>
        <option value="high">High Quantity</option>
      </select>
      <button onclick="filterTable('inventory')">Search</button>
    </div>
    <table id="inventoryTable" class="report-table">
      <thead>
        <tr>
          <th>Inventory ID</th>
          <th>Item</th>
          <th>Type</th>
          <th>Quantity</th>
          <th>Unit</th>
          <th>Supplier ID</th>
          <th>Product ID</th>
        </tr>
      </thead>
      <tbody id="inventoryTableBody"></tbody>
    </table>
    <button class="download-btn" data-report-type="inventory" onclick="downloadPDF('inventory')">Download as
      PDF</button>
  </div>

  <div id="feedbackReportContainer" class="report-container">
    <table id="feedbackTable" class="report-table">
      <thead>
        <tr>
          <th>Feedback ID</th>
          <th>Title</th>
          <th>Description</th>
          <th>Customer ID</th>
        </tr>
      </thead>
      <tbody id="tableBodyFeedback"></tbody>
    </table>
    <button class="download-btn" data-report-type="feedback" onclick="downloadPDF('feedback')">Download as PDF</button>
  </div>

  <script>
    var isSalesTableVisible = false;
    var isInventoryTableVisible = false;
    var isFeedbackTableVisible = false;

    function showReport(reportType) {
      if (reportType === 'sales') {
        isSalesTableVisible = !isSalesTableVisible;
        $("#salesReportContainer").toggle(isSalesTableVisible);
        $(".search-filter").toggle(isSalesTableVisible);
        fetchDataAndDisplay('sales');
      } else if (reportType === 'inventory') {
        isInventoryTableVisible = !isInventoryTableVisible;
        $("#inventoryReportContainer").toggle(isInventoryTableVisible);
        $(".search-filter").toggle(isInventoryTableVisible);
        fetchDataAndDisplay('inventory');
      } else if (reportType === 'feedback') {
        isFeedbackTableVisible = !isFeedbackTableVisible;
        $("#feedbackReportContainer").toggle(isFeedbackTableVisible);
        $(".search-filter").toggle(!isFeedbackTableVisible);
        fetchDataAndDisplay('feedback');
      }
    }

    function filterTable(reportType) {
      var filterId, tableBodyId, startDateId, endDateId;

      if (reportType === 'sales') {
        filterId = 'filterDropdownSales';
        tableBodyId = 'tableBodySales';
        startDateId = 'startDateSales';
        endDateId = 'endDateSales';
      } else if (reportType === 'inventory') {
        filterId = 'quantityFilterInventory';
        tableBodyId = 'inventoryTableBody';
      }

      var filterValue = $("#" + filterId).val();
      var startDate = $("#" + startDateId).val();
      var endDate = $("#" + endDateId).val();

      fetchDataAndDisplay(reportType, filterValue, startDate, endDate);
    }

    function searchSales() {
      var filterValue = $("#filterDropdownSales").val();
      var startDate = $("#startDateSales").val();
      var endDate = $("#endDateSales").val();

      fetchDataAndDisplay('sales', filterValue, startDate, endDate);
    }

    function fetchDataAndDisplay(reportType, filterValue, startDate, endDate) {
      var url;

      if (reportType === 'sales') {
        url = 'sales_data.php?get_sales_data';


        if (filterValue !== '') {
          url += '&filter=' + filterValue;
        }


        if (startDate && endDate) {
          url += '&startDate=' + startDate + '&endDate=' + endDate;

        } else if (reportType === 'inventory') {
          url = 'inventory_data.php?get_inventory_data';
        } else if (reportType === 'feedback') {
          url = 'feedback_data.php?get_feedback_data';
        }
      }
      $.get(url, function (data) {
        var reportData = JSON.parse(data);
        if (reportType === 'sales') {
          displaySalesReport(reportData);
        } else if (reportType === 'inventory') {
          displayInventoryReport(reportData);
        } else if (reportType === 'feedback') {
          displayFeedbackReport(reportData);
        }
      });
    }

    function displaySalesReport(data) {
      $("#tableBodySales").empty();

      for (var i = 0; i < data.length; i++) {
        var row = "<tr>";
        row += "<td>" + data[i].paymentID + "</td>";
        row += "<td>" + data[i].order_datetime + "</td>";
        row += "<td>" + data[i].amountpayed + "</td>";
        row += "<td>" + data[i].paymenttype + "</td>";
        row += "<td>" + data[i].customerid + "</td>";
        row += "<td>" + data[i].orderid + "</td>";
        row += "</tr>";

        $("#tableBodySales").append(row);
      }
    }

    function displayInventoryReport(data) {
      $("#inventoryTableBody").empty();

      for (var i = 0; i < data.length; i++) {
        var row = "<tr>";
        row += "<td>" + data[i].inventory_id + "</td>";
        row += "<td>" + data[i].inventory_item + "</td>";
        row += "<td>" + data[i].item_type + "</td>";
        row += "<td>" + data[i].quantity + "</td>";
        row += "<td>" + data[i].unit + "</td>";
        row += "<td>" + data[i].supplier_id + "</td>";
        row += "<td>" + data[i].product_id + "</td>";
        row += "</tr>";

        $("#inventoryTableBody").append(row);
      }
    }

    function displayFeedbackReport(data) {
      $("#tableBodyFeedback").empty();

      for (var i = 0; i < data.length; i++) {
        var row = "<tr>";
        row += "<td>" + data[i].feedbackid + "</td>";
        row += "<td>" + data[i].title + "</td>";

        var fullDescription = data[i].feedback_desc;
        var truncatedDescription = truncateDescription(fullDescription, 100);

        row += "<td class='feedback-description' data-full-description='" + escapeHtml(fullDescription) + "'>" + truncatedDescription + "</td>";

        row += "<td>" + data[i].customerid + "</td>";
        row += "</tr>";

        $("#tableBodyFeedback").append(row);
      }
    }

    function truncateDescription(description, maxLength) {
      if (description.length > maxLength) {
        var truncated = description.substring(0, maxLength);
        return truncated + "<span class='show-more' onclick='showFullDescription(this)'> Show More</span>";
      } else {
        return description;
      }
    }
    function showFullDescription(element) {
      var tdElement = $(element).parent();
      var fullDescription = tdElement.data('full-description');


      var fullDescriptionDiv = $("<div>").addClass('full-description').html(fullDescription);


      tdElement.append(fullDescriptionDiv);


      tdElement.css({
        'max-width': '20px',
        'overflow': 'hidden',
      });

      tdElement.find('.original-content').hide();


      $(element).hide();


      tdElement.append("<span class='show-less' onclick='showLessDescription(this)'> Show Less</span>");
    }

    function showLessDescription(element) {
      var tdElement = $(element).parent();

      tdElement.find('.original-content').show();


      tdElement.find('.show-more').show();


      $(element).hide();

      tdElement.find('.full-description').remove();


      tdElement.css({
        'max-width': 'none',
        'overflow': 'visible',
      });
    }

    function truncateDescription(description, maxLength, maxHeight) {
      if (description.length > maxLength) {
        var truncated = description.substring(0, maxLength);
        return truncated + "<span class='show-more' onclick='showFullDescription(this)'> Show More</span>";
      } else {
        return description;
      }
    }


    function displayFeedbackReport(data) {
      $("#tableBodyFeedback").empty();

      for (var i = 0; i < data.length; i++) {
        var row = "<tr>";
        row += "<td>" + data[i].feedbackid + "</td>";
        row += "<td>" + data[i].title + "</td>";

        var fullDescription = data[i].feedback_desc;
        var truncatedDescription = truncateDescription(fullDescription, 100, 40); // pang adjust ng character limit tsaka maximum height


        row += "<td data-full-description='" + escapeHtml(fullDescription) + "'>" + truncatedDescription + "</td>";

        row += "<td>" + data[i].customerid + "</td>";
        row += "</tr>";

        $("#tableBodyFeedback").append(row);
      }
    }


    function escapeHtml(text) {
      var div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }


    function downloadPDF(reportType) {
      var elementId, element;
      var startY = 20;

      if (reportType === 'sales') {
        elementId = 'salesTable';
      } else if (reportType === 'inventory') {
        elementId = 'inventoryTable';
      } else if (reportType === 'feedback') {
        elementId = 'feedbackTable';
      }

      element = document.getElementById(elementId);

      var pdf = new jsPDF({
        unit: 'mm',
        format: 'a4',
        orientation: 'portrait',
      });

      pdf.text('Your Report Title', 15, 15);

      pdf.autoTable({
        html: '#' + elementId,
        startY: 30,
        theme: 'grid',
        headStyles: {
          fillColor: [51, 122, 183],
          textColor: 255,
        },
        bodyStyles: {
          textColor: 0,
        },
        columnStyles: {
          0: { cellWidth: 20 },
        },
      });

      pdf.save('custom_report.pdf');
    }

    function filterTable(reportType) {
      var filterId, tableBodyId;

      if (reportType === 'sales') {
        filterId = 'filterDropdownSales';
        tableBodyId = 'tableBodySales';
      } else if (reportType === 'inventory') {
        filterId = 'quantityFilterInventory';
        tableBodyId = 'inventoryTableBody';
      }

      var filterValue = $("#" + filterId).val();
      fetchDataAndDisplay(reportType, filterValue);
    }
  </script>

</body>

</html>