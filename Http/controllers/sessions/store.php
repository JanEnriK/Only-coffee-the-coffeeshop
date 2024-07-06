<?php

use Http\Forms\LoginForm;
use Core\Authenticator;
use Core\Session;

$email = $_POST['email'];
$password = $_POST['password'];

$form = new LoginForm();

if ($form->validate($email, $password)) {
  $auth = new Authenticator();

  if ($auth->attemp($email, $password)) {
    if ($_SESSION['user']['position'] === 'admin') {
      $_SESSION['loginSuccess'] = true;
      redirect('/admin_dashboard');
    }
    $_SESSION['loginSuccess'] = true;
    redirect('/');
  }

  $form->error('email', 'No matching account for this email address and password');
}

// return view('sessions/create.view.php', [
//   'heading' => 'Login Form',
//   'errors' => $form->errors(),
// ]);

Session::flash('errors', $form->errors());
Session::flash('old', [
  'email' => $email,
  'password' => $password,
]);

return redirect('/login');
