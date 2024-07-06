<?php

$router->get('/', 'index.php');
$router->get('/about', 'about.php');
$router->get('/service', 'service.php');
$router->get('/menu', 'menu/menu.php');
$router->post('/menu', 'menu/store.php')->only('auth');
$router->post('/store_cart', 'menu/store_cart.php')->only('auth');
$router->post('/check_discount', 'menu/check_discount.php')->only('auth');
$router->post('/remove_cart', 'menu/remove_cart.php')->only('auth');
$router->post('/update_cart', 'menu/update_cart.php')->only('auth');
$router->get('/get_products', 'get_products.php');
$router->get('/show_product', 'product_page.php')->only('auth');

$router->get('/reservation', 'reservation.php')->only('auth');
$router->get('/testimonial', 'feedback/testimonial.php')->only('auth');
$router->post('/feedback', 'feedback/store.php')->only('auth');
$router->get('/contact', 'contact.php');
// $router->get('/notes', 'notes/index.php')->only('auth');
// $router->get('/note', 'notes/show.php');
// $router->delete('/note', 'notes/destroy.php');

$router->get('/admin_dashboard', 'dashboard/index.php')->only('admin');
$router->get('/admin_dashboard/info', 'dashboard/coffee_info.php')->only('admin');
$router->post('/admin_dashboard/info', 'dashboard/coffee_info.php')->only('admin');
$router->get('/admin_dashboard/accounts', 'dashboard/accounts.php')->only('admin');
$router->get('/admin_dashboard/orders', 'dashboard/orders.php')->only('admin');
$router->post('/admin_dashboard/orders', 'dashboard/orders.php')->only('admin');
$router->get('/admin_dashboard/inventory', 'dashboard/inventory.php')->only('admin');
$router->post('/admin_dashboard/inventory', 'dashboard/inventory.php')->only('admin');
$router->get('/admin_dashboard/products', 'dashboard/products.php')->only('admin');
$router->post('/admin_dashboard/products', 'dashboard/products.php')->only('admin');
$router->get('/admin_dashboard/staffs', 'dashboard/staffs.php')->only('admin');
$router->post('/admin_dashboard/staffs', 'dashboard/staffs.php')->only('admin');
$router->get('/admin_dashboard/reports', 'dashboard/reports.php')->only('admin');

$router->get('/admin_dashboard/reports?get_sales_data', 'dashboard/sales_data.php')->only('admin');
$router->get('/admin_dashboard/reports?get_inventory_data', 'dashboard/inventory_data.php')->only('admin');
$router->get('/admin_dashboard/reports?get_feedback_data', 'dashboard/feedback_data.php')->only('admin');
$router->get('/admin_dashboard/reports?get_userlogs_data', 'dashboard/userlogs_data.php')->only('admin');

$router->get('/pos_frontend', 'pos/index.php')->only('admin');
$router->get('/pos_frontend/frappe', 'pos/frappe.php')->only('admin');
$router->get('/pos_frontend/americano', 'pos/americano.php')->only('admin');
$router->get('/pos_frontend/expresso', 'pos/expresso.php')->only('admin');
$router->get('/pos_frontend/latte', 'pos/latte.php')->only('admin');
$router->get('/pos_frontend/capuccino', 'pos/capuccino.php')->only('admin');

$router->get('/pos_frontend/online_orders', 'pos/online_orders.php')->only('admin');
$router->post('/get_orders', 'pos/get_orders.php')->only('admin');
$router->post('/store_payment', 'pos/store_payment.php')->only('admin');
$router->post('/onsite_payment', 'pos/onsite_payment.php')->only('admin');

$router->get('/pos_frontend/pos_connect', 'pos/pos_connect.php')->only('admin');

// $router->get('/notes/create', 'notes/create.php');
// $router->post('/notes/store', 'notes/store.php');

// $router->get('/notes/edit', 'notes/edit.php');
// $router->patch('/notes', 'notes/update.php');

$router->get('/register', 'registration/create.php')->only('guest');
$router->post('/register', 'registration/store.php')->only('guest');

$router->get('/login', 'sessions/create.php')->only('guest');
$router->post('/sessions', 'sessions/store.php')->only('guest');
$router->delete('/sessions', 'sessions/destroy.php')->only('auth');

$router->get('/test', 'test.php');
