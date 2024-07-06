<?php

session_start();

use Core\Session;

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . "Core/functions.php";

spl_autoload_register(function($class) {
  //Core\Database
  $class = str_replace('\\', DIRECTORY_SEPARATOR , $class);

  //dd($class);

  require base_path("{$class}.php");
});

// require base_path("Database.php");
// require base_path("Response.php");
// require base_path("Core/router.php");

require base_path('bootstrap.php');

$router = new \Core\Router();

$routes = require base_path('routes.php');
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method);

Session::unflash();