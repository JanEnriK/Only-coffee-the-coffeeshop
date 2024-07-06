var isSalesTableVisible = false;
var isInventoryTableVisible = false;
var isFeedbackTableVisible = false;
var isUserlogsTableVisible = false;

function showReport(reportType) {
  if (reportType === 'sales') {
    // Toggle visibility of sales report container
    isSalesTableVisible = !isSalesTableVisible;
    $("#salesReportContainer").toggle(isSalesTableVisible);
    $(".search-filter").toggle(isSalesTableVisible);
    fetchDataAndDisplay(reportType); // Pass reportType as an argument
    // Hide other report containers
    $("#inventoryReportContainer").hide();
    $("#feedbackReportContainer").hide();
    $("#userlogsReportContainer").hide();
  } else if (reportType === 'inventory') {
    // Toggle visibility of inventory report container
    isInventoryTableVisible = !isInventoryTableVisible;
    $("#inventoryReportContainer").toggle(isInventoryTableVisible);
    $(".search-filter").toggle(isInventoryTableVisible);
    fetchDataAndDisplay(reportType); // Pass reportType as an argument
    // Hide other report containers
    $("#salesReportContainer").hide();
    $("#feedbackReportContainer").hide();
    $("#userlogsReportContainer").hide();
  } else if (reportType === 'feedback') {
    // Toggle visibility of feedback report container
    isFeedbackTableVisible = !isFeedbackTableVisible;
    $("#feedbackReportContainer").toggle(isFeedbackTableVisible);
    $(".search-filter").toggle(isFeedbackTableVisible);
    fetchDataAndDisplay(reportType); // Pass reportType as an argument
    // Hide other report containers
    $("#salesReportContainer").hide();
    $("#inventoryReportContainer").hide();
    $("#userlogsReportContainer").hide();
  } else if (reportType === 'userlogs') {
    // Toggle visibility of userlogs report container
    isUserlogsTableVisible = !isUserlogsTableVisible;
    $("#userlogsReportContainer").toggle(isUserlogsTableVisible);
    $(".search-filter").toggle(isUserlogsTableVisible);
    fetchDataAndDisplay(reportType); // Pass reportType as an argument
    // Hide other report containers
    $("#salesReportContainer").hide();
    $("#inventoryReportContainer").hide();
    $("#feedbackReportContainer").hide();
  } 
}

// Remove the unnecessary condition for 'userlogs' in fetchDataAndDisplay function


 
function downloadPDF(containerId) {
var container = document.getElementById(containerId);
  if (container) {
    var contentToPrint = container.cloneNode(true); 
    
    $(contentToPrint).find('.search-filter, .download-btn').remove();

    var printWindow = window.open('', '', 'height=400,width=800');
    printWindow.document.write('<html><head><title>PDF Export</title>');
    printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">'); 
    printWindow.document.write('<style>');
    printWindow.document.write(`
      .container { margin-top: 20px; }
      .table { border-collapse: collapse; width: 100%; }
      .table th, .table td { border: 1px solid #dee2e6; padding: 8px; }
      .table th { background-color: #f8f9fa; }
      .text-center { text-align: center; }
      .table-responsive { overflow-x: hidden; } /* Disable horizontal scrolling */
      .table-responsive table { width: auto; } /* Set table width to auto */
    `); 
    printWindow.document.write('</style></head><body>');
    printWindow.document.write(contentToPrint.innerHTML); 
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
  } else {
    console.error("Container element not found.");
  }
}

//filtersss start
function searchSales() {//for sales report
  var startDate = $("#startDateSales").val();
  var endDate = $("#endDateSales").val();

  fetchDataAndDisplay('sales', "", startDate, endDate);
}

function filterTable(reportType) {//for inventory report
  var filterId;

  if (reportType === 'inventory') {
    filterId = 'quantityFilterInventory';
    tableBodyId = 'inventoryTableBody';
  } else if (reportType === 'feedback'){
    var startDate = $("#startDateFeedback").val();
    var endDate = $("#endDateFeedback").val();
  } else if (reportType === 'userlogs'){
    var startDate = $("#startDateUserlog").val();
    var endDate = $("#endDateUserlog").val();
  }

  var filterValue = $("#" + filterId).val();

  if (reportType === 'inventory') {
    fetchDataAndDisplay(reportType, filterValue, "", "");
  } else if (reportType === 'feedback'){
    fetchDataAndDisplay(reportType, "", startDate, endDate);
  } else if (reportType === 'userlogs'){
    fetchDataAndDisplay(reportType, "", startDate, endDate);
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
    row += "<td>" + data[i].feedback_datetime + "</td>";
    row += "<td>" + data[i].customerid + "</td>";
    row += "</tr>";

    $("#tableBodyFeedback").append(row);
  }
}

function displayUserlogsReport(data) {
  $("#userlogsTableBody").empty();

  for (var i = 0; i < data.length; i++) {
    var row = "<tr>";
    row += "<td>" + data[i].logid + "</td>";
    row += "<td>" + data[i].log_datetime + "</td>";
    row += "<td>" + data[i].loginfo + "</td>";
    row += "<td>" + data[i].employeeid + "</td>";
    row += "</tr>";

    $("#userlogsTableBody").append(row);
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

function escapeHtml(text) {
  var div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}


function fetchDataAndDisplay(reportType, filterValue, startDate, endDate) {
  var url;

  if (reportType === 'sales') {
    url = 'sales_data.php?get_sales_data';

    if (startDate && endDate) {
      url += '&startDate=' + startDate + '&endDate=' + endDate;
    }
  } else  if (reportType === 'inventory') {
      url = 'inventory_data.php?get_inventory_data';
    if(filterValue){
      url += '&filterValue=' + filterValue;
    }
  } else if (reportType === 'feedback') {
    url = 'feedback_data.php?get_feedback_data';
    if (startDate && endDate) {
      url += '&startDate=' + startDate + '&endDate=' + endDate;
    }
  } else if (reportType === 'userlogs') {
    url = 'userlogs_data.php?get_userlogs_data';
    if (startDate && endDate) {
      url += '&startDate=' + startDate + '&endDate=' + endDate;
    }
  } else {
    console.error('Invalid report type: ' + reportType);
    return; // Exit function if reportType is not recognized
  }

  $.get(url, function(data) {
    var reportData = JSON.parse(data);
    console.log('Data received:', reportData); // Debug log to check the data received

    if (reportType === 'sales') {
      displaySalesReport(reportData);
    } else if (reportType === 'inventory') {
      displayInventoryReport(reportData);
    } else if (reportType === 'feedback') {
      displayFeedbackReport(reportData);
    } else if (reportType === 'userlogs') {
      displayUserlogsReport(reportData);
    }
  });
}


function displaySalesReport(data) {
  $("#tableBodySales").empty();
  
  // Sort data based on payment type
  data.sort(function(a, b) {
    var paymentTypeA = a.paymenttype.toUpperCase(); 
    var paymentTypeB = b.paymenttype.toUpperCase(); 
    if (paymentTypeA < paymentTypeB) {
      return -1;
    } else if (paymentTypeA > paymentTypeB) {
      return 1;
    } else {
      return 0;
    }
  });

  // Iterate through sorted data and append rows to the table
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

