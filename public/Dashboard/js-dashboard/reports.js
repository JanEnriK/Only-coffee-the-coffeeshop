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
    var feedbackStartDate = $("#startDateFeedback").val();
    var feedbackEndDate = $("#endDateFeedback").val();
  } else if (reportType === 'userlogs'){
    var userlogsStartDate = $("#startDateUserlog").val();
    var userlogsEndDate = $("#endDateUserlog").val();
  }

  var filterValue = $("#" + filterId).val();

  if (reportType === 'inventory') {
    fetchDataAndDisplay(reportType, filterValue, "", "");
  } else if (reportType === 'feedback'){
    fetchDataAndDisplay(reportType, "", feedbackStartDate , feedbackEndDate);
  } else if (reportType === 'userlogs'){
    fetchDataAndDisplay(reportType, "", userlogsStartDate, userlogsEndDate);
  }
  
}




function displayInventoryReport(data) {
  $("#inventoryTableBody").empty();

  for (var i = 0; i < data.length; i++) {
    var row = "<tr>";
    row += "<td>" + data[i].inventory_report_id + "</td>";
    row += "<td>" + data[i].inventory_item + "</td>";
    row += "<td>" + data[i].quantity + "</td>";
    row += "<td>" + data[i].unit + "</td>";
    row += "<td>" + data[i].record_type + "</td>";
    row += "<td>" + data[i].reason + "</td>";
    row += "<td>" + data[i].datetime + "</td>";
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


function fetchDataAndDisplay(reportType, filterValue, fetchedStartDate, fetchedEndDate) {
  var url;

  if (reportType === 'sales') {
    url = 'reports?get_sales_data';

    if (fetchedStartDate && fetchedEndDate) {
      url += '&startDate=' + fetchedStartDate + '&endDate=' + fetchedEndDate;
    }
  } else  if (reportType === 'inventory') {
      url = 'reports?get_inventory_data';
    if(filterValue){
      url += '&filterValue=' + filterValue;
    }
  } else if (reportType === 'feedback') {
    url = 'reports?get_feedback_data';
    if (fetchedStartDate && fetchedEndDate) {
      url += '&feedbackStartDate=' + fetchedStartDate + '&feedbackEndDate=' + fetchedEndDate;
    }
  } else if (reportType === 'userlogs') {
    url = 'reports?get_userlogs_data';
    if (fetchedStartDate && fetchedEndDate) {
      url += '&userlogStartDate=' + fetchedStartDate + '&userlogEndDate=' + fetchedEndDate;
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
    row += "<td>" + data[i].reference_no + "</td>";
    row += "<td>" + data[i].customerid + "</td>";
    // row += "<td>" + data[i].orderid + "</td>";
    row += "</tr>";

    $("#tableBodySales").append(row);
  }
}

