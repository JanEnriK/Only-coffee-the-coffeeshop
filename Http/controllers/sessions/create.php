<?php 

use Core\Session;

view('sessions/create.view.php', [
  'heading' => 'Login Form',
  'errors' => Session::get('errors'),
]);

?>