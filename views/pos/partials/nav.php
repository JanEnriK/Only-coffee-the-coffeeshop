    <!-- Navbar Start -->
    <style>
        nav>a {
            padding: 0;
            margin: 0;
            color: white;
            font-weight: bold;
            font-size: 1.5em;
            display: flex;
            align-items: center;
        }

        ul {
            padding: 0;
            margin: 0;
        }
    </style>
    <nav style="padding:10px;">
        <a style="color:white;">Point of Sales</a>
        <ul>
            <a class="login-button" style="background-color:black; color:white;" href="/pos_frontend/online_orders">Online Orders</a>
            <a class="login-button" style="background-color:black; color:white;" href="/pos_frontend">Products</a>
        </ul>
        <ul style="text-align:right;">
            <li>
                <a class="login-button" style="background-color:black; color:white;" href="/admin_dashboard">Dashboard</a>
            </li>
            <li>
                <a class="login-button" href="/sessions" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a>
                <form id="logout-form" action="/sessions" method="POST" style="display: none;">
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            </li>
        </ul>

    </nav>
    <!-- Navbar End -->