<!-- Navbar Start -->
<div class="sidebar">
    <h1>COFFEE SHOP</h1>
    <ul>
        <li><a href="/admin_dashboard"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="/admin_dashboard/info"><i class="fa fa-coffee"></i> Coffee Shop</a></li>
        <!-- <li><a href="/admin_dashboard/accounts"><i class="fa fa-user"></i> Accounts</a></li> -->
        <li><a href="/admin_dashboard/orders"><i class="fa fa-shopping-cart"></i> Orders</a></li>
        <li><a href="/admin_dashboard/inventory"><i class="fa fa-archive"></i> Inventory</a></li>
        <li><a href="/admin_dashboard/products"><i class="fa fa-cube"></i> Products</a></li>
        <li><a href="/admin_dashboard/staffs"><i class="fa fa-users"></i> Accounts</a></li>
        <li><a href="/admin_dashboard/reports"><i class="fa fa-bar-chart"></i> Reports</a></li>
        <li><a href="/pos_frontend"><i class="fa fa-money"></i> POS</a></li>
        <button class="btn h6" style="background-color: grey;" onclick="window.location.href='/';">Go to Website</button>
        <form action="/sessions" method="POST">
            <input type="hidden" name="_method" value="DELETE">
            <button class="btn h6" style="background-color: grey;">Log Out</button>
        </form>

    </ul>
</div>

<!-- Navbar End -->

<script>
    document.addEventListener(" DOMContentLoaded", function() {
        var currentPath = window.location.pathname.replace(/\/$/, "");
        var links = document.querySelectorAll(".sidebar ul li a");
        links.forEach(function(link) {
            var href = link.getAttribute("href").replace(/\/$/, "");
            if (href === currentPath) {
                link.classList.add("active");
            }
        });
        document.querySelector('.toggle-btn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
        if (window.innerWidth < 768) {
            document.querySelector('.sidebar').classList.add('active');
        }
    });
</script>
<!-- Navbar End -->