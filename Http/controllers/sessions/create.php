<?php

use Core\Session;
use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

$coffee = $db->query("SELECT * FROM tblcoffeeshop")->find();

view('sessions/create.view.php', [
  'heading' => 'Login Form',
  'errors' => Session::get('errors'),
  'coffee' => $coffee,
]);
