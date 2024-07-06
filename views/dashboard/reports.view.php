<?php require "partials/head.php"; ?>
<?php require "partials/nav.php"; ?>



<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="dashboard.css" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="/Dashboard/css/reports.css">

<style>
  .search-filter {
    display: none;
  }

  .report-container {
    display: none;
  }
</style>

<body>
  <div class="content">
    <h2>Reports
      <?php echo " (" . $_SESSION['user']['email'] . " the " . $_SESSION['user']['position'] . ") " ?>
    </h2>
    <div class="container">
      <div class="row justify-content-center mt-3">
        <div class="col text-center">
          <button class="btn btn-primary" onclick="showReport('sales')">Show Sales Report</button>
          <button class="btn btn-primary" onclick="showReport('inventory')">Show Inventory Report</button>
          <button class="btn btn-primary" onclick="showReport('feedback')">Show Feedback Report</button>
          <button class="btn btn-primary" onclick="showReport('userlogs')">Show Userlogs Report</button>
        </div>
      </div>

      <div id="salesReportContainer" class="report-container">
        <div class="search-filter">
          <div class="row mt-2 align-items-center">
            <div class="col-4">
              <label for="startDateSales">Start Date:</label>
              <input type="date" id="startDateSales" class="form-control">
            </div>
            <div class="col-4">
              <label for="endDateSales">End Date:</label>
              <input type="date" id="endDateSales" class="form-control">
            </div>
            <div class="col-4">
              <button onclick="searchSales()" class="btn btn-primary mt-4">Search</button>
            </div>
          </div>
        </div>
        <div class="table-responsive" style="max-height: 800px; overflow-y: auto;" id="content">
          <table id="salesTable" class="report-table table">
            <thead>
              <tr>
                <th>Payment ID</th>
                <th>Order Date Time</th>
                <th>Amount Paid</th>
                <th>Payment Type</th>
                <th>Reference No.</th>
                <th>Customer ID</th>

              </tr>
            </thead>
            <tbody id="tableBodySales" style="height: 100%;"></tbody>
          </table>
        </div>
        <div class="row justify-content-center mb-2">
          <div class="col text-center">
            <button class="download-btn btn btn-primary" data-report-type="sales" onclick="downloadPDF('salesReportContainer')">Print as PDF</button>
          </div>
          <div class="col text-center">
            <button class="download-btn btn btn-primary" data-report-type="sales" onclick="demoFromHTML()">Download as PDF</button>
          </div>
        </div>
      </div>

      <div id="inventoryReportContainer" class="report-container">
        <div class="search-filter">
          <div class="row align-items-center">
            <div class="col-5">
              <label for="quantityFilterInventory">Filter by Quantity Change:</label>
              <select id="quantityFilterInventory" class="form-control" onchange="filterTable('inventory')">
                <option value="">All</option>
                <option value="deduct">Deduct</option>
                <option value="add">Add</option>
              </select>
            </div>
            <div class="col-7">
              <button onclick="filterTable('inventory')" class="btn btn-primary mt-4">Search</button>
            </div>
          </div>
        </div>
        <div class="table-responsive" style="max-height: 800px; overflow-y: auto;" id="content2">
          <table id="inventoryTable" class="report-table table mt-2">
            <thead>
              <tr>
                <th>Inventory Report id</th>
                <th>Item</th>
                <th>Quantity Change</th>
                <th>Unit</th>
                <th>Report Type</th>
                <th>Note</th>
                <th>Date Time</th>
              </tr>
            </thead>
            <tbody id="inventoryTableBody" style="height: 100%;"></tbody>
          </table>
        </div>

        <div class="row justify-content-center mb-2">
          <div class="col text-center">
            <button class="download-btn btn btn-primary" data-report-type="inventory" onclick="downloadPDF('inventoryReportContainer')">Print as PDF</button>
          </div>
          <div class="col text-center">
            <button class="download-btn btn btn-primary" data-report-type="inventory" onclick="demoFromHTML2()">Download as PDF</button>
          </div>
        </div>
      </div>


      <div>
        <div id="feedbackReportContainer" class="report-container">
          <div class="search-filter">
            <div class="row mt-2 align-items-center">
              <div class="col-4">
                <label for="startDateFeedback">Start Date:</label>
                <input type="date" id="startDateFeedback" class="form-control">
              </div>
              <div class="col-4">
                <label for="endDateFeedback">End Date:</label>
                <input type="date" id="endDateFeedback" class="form-control">
              </div>
              <div class="col-4">
                <button onclick="filterTable('feedback')" class="btn btn-primary mt-4">Search</button>
              </div>
            </div>
          </div>
          <div id="content3">
            <table id="feedbackTable" class="report-table table">
              <thead>
                <tr>
                  <th>Feedback ID</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Date and time</th>
                  <th>Customer ID</th>
                </tr>
              </thead>
              <tbody id="tableBodyFeedback"></tbody>
            </table>
          </div>
          <div class="row justify-content-center mb-2">
            <div class="col text-center">
              <button class="download-btn btn btn-primary" data-report-type="feedback" onclick="downloadPDF('feedbackReportContainer')">Print as PDF</button>
            </div>
            <div class="col text-center">
              <button class="download-btn btn btn-primary" data-report-type="sales" onclick="demoFromHTML3()">Download as PDF</button>
            </div>
          </div>
        </div>
      </div>

      <div id="userlogsReportContainer" class="report-container">
        <div class="search-filter">
          <div class="row mt-2 align-items-center">
            <div class="col-4">
              <label for="startDateUserlog">Start Date:</label>
              <input type="date" id="startDateUserlog" class="form-control">
            </div>
            <div class="col-4">
              <label for="endDateUserlog">End Date:</label>
              <input type="date" id="endDateUserlog" class="form-control">
            </div>
            <div class="col-4">
              <button onclick="filterTable('userlogs')" class="btn btn-primary mt-4">Search</button>
            </div>
          </div>
        </div>
        <div class="table-responsive" style="max-height: 800px; overflow-y: auto;" id="content4">
          <table id="userlogsTable" class="report-table table mt-2">
            <thead>
              <tr>
                <th>Log ID</th>
                <th>Date and Time</th>
                <th>Log Information</th>
                <th>Employee ID</th>
              </tr>
            </thead>
            <tbody id="userlogsTableBody" style="height: 100%;"></tbody>
          </table>
        </div>
        <div class="row justify-content-center mb-2">
          <div class="col text-center">
            <button class="download-btn btn btn-primary" data-report-type="feedback" onclick="downloadPDF('userlogsReportContainer')">Print as PDF</button>
          </div>
          <div class="col text-center">
            <button class="download-btn btn btn-primary" data-report-type="sales" onclick="demoFromHTML4()">Download as PDF</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="/Dashboard/js-dashboard/reports.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>

    <script>
      function demoFromHTML() {
        var pdf = new jsPDF('p', 'pt', 'letter');
        // source can be HTML-formatted string, or a reference
        // to an actual DOM element from which the text will be scraped.
        source = $('#content')[0];

        // we support special element handlers. Register them with jQuery-style 
        // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
        // There is no support for any other type of selectors 
        // (class, of compound) at this time.
        specialElementHandlers = {
          // element with id of "bypass" - jQuery style selector
          '#bypassme': function(element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
          }
        };
        margins = {
          top: 80,
          bottom: 60,
          left: 40,
          width: 522
        };
        // all coords and widths are in jsPDF instance's declared units
        // 'inches' in this case
        pdf.fromHTML(
          source, // HTML string or DOM elem ref.
          margins.left, // x coord
          margins.top, { // y coord
            'width': margins.width, // max width of content on PDF
            'elementHandlers': specialElementHandlers
          },

          function(dispose) {
            // dispose: object with X, Y of the last line add to the PDF 
            //          this allow the insertion of new lines after html
            pdf.save('table_sales.pdf');
          }, margins
        );
      }

      function demoFromHTML2() {
        var pdf = new jsPDF('p', 'pt', 'letter');
        // source can be HTML-formatted string, or a reference
        // to an actual DOM element from which the text will be scraped.
        source = $('#content2')[0];

        // we support special element handlers. Register them with jQuery-style 
        // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
        // There is no support for any other type of selectors 
        // (class, of compound) at this time.
        specialElementHandlers = {
          // element with id of "bypass" - jQuery style selector
          '#bypassme': function(element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
          }
        };
        margins = {
          top: 80,
          bottom: 60,
          left: 40,
          width: 522
        };
        // all coords and widths are in jsPDF instance's declared units
        // 'inches' in this case
        pdf.fromHTML(
          source, // HTML string or DOM elem ref.
          margins.left, // x coord
          margins.top, { // y coord
            'width': margins.width, // max width of content on PDF
            'elementHandlers': specialElementHandlers
          },

          function(dispose) {
            // dispose: object with X, Y of the last line add to the PDF 
            //          this allow the insertion of new lines after html
            pdf.save('table_inventory.pdf');
          }, margins
        );
      }

      function demoFromHTML3() {
        var pdf = new jsPDF('p', 'pt', 'letter');
        // source can be HTML-formatted string, or a reference
        // to an actual DOM element from which the text will be scraped.
        source = $('#content3')[0];

        // we support special element handlers. Register them with jQuery-style 
        // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
        // There is no support for any other type of selectors 
        // (class, of compound) at this time.
        specialElementHandlers = {
          // element with id of "bypass" - jQuery style selector
          '#bypassme': function(element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
          }
        };
        margins = {
          top: 80,
          bottom: 60,
          left: 40,
          width: 522
        };
        // all coords and widths are in jsPDF instance's declared units
        // 'inches' in this case
        pdf.fromHTML(
          source, // HTML string or DOM elem ref.
          margins.left, // x coord
          margins.top, { // y coord
            'width': margins.width, // max width of content on PDF
            'elementHandlers': specialElementHandlers
          },

          function(dispose) {
            // dispose: object with X, Y of the last line add to the PDF 
            //          this allow the insertion of new lines after html
            pdf.save('table_report.pdf');
          }, margins
        );
      }

      function demoFromHTML4() {
        var pdf = new jsPDF('p', 'pt', 'letter');
        // source can be HTML-formatted string, or a reference
        // to an actual DOM element from which the text will be scraped.
        source = $('#content4')[0];

        // we support special element handlers. Register them with jQuery-style 
        // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
        // There is no support for any other type of selectors 
        // (class, of compound) at this time.
        specialElementHandlers = {
          // element with id of "bypass" - jQuery style selector
          '#bypassme': function(element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
          }
        };
        margins = {
          top: 80,
          bottom: 60,
          left: 40,
          width: 522
        };
        // all coords and widths are in jsPDF instance's declared units
        // 'inches' in this case
        pdf.fromHTML(
          source, // HTML string or DOM elem ref.
          margins.left, // x coord
          margins.top, { // y coord
            'width': margins.width, // max width of content on PDF
            'elementHandlers': specialElementHandlers
          },

          function(dispose) {
            // dispose: object with X, Y of the last line add to the PDF 
            //          this allow the insertion of new lines after html
            pdf.save('table_report.pdf');
          }, margins
        );
      }
    </script>

</body>

</html>