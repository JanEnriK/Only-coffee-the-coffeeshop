<?php

use Core\App;
use Core\Database;
use Core\Validator;
use Core\Authenticator;

$db = App::resolve('Core\Database');

$first_name = $_POST['firstname'];
$last_name = $_POST['lastname'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

$errors = [];

if (!Validator::email($email)) {
  $errors['email'] = "Please provide a valid email.";
}

if (!Validator::string($password, 7, 255)) {
  $errors['password'] = "Please provide a password of atleast 7 characters.";
}

$checkUsername = $db->query("SELECT * FROM tblemployees where 1")->get();


foreach ($checkUsername as $usernameExist) {
  if ($usernameExist['username'] == $username) {
    $errors["username"] = "The username '$username' has already been taken.";
  }
  if ($usernameExist['email'] == $email) {
    $errors["email"] = "An account with this email '$email' is currently existing.";
  }
}


if (!empty($errors)) {
  return view('registration/create.view.php', [
    'heading' => 'Register',
    'errors' => $errors,
    'coffee' => $coffee,
  ]);
}

$user = $db->query("SELECT * FROM tblemployees where email = :email", ['email' => $email])->find();

if ($user) {

  header('location: /');
  die();
} else {
  // Register the user
  $db->query("INSERT INTO tblemployees(firstname, lastname, email, username, password) VALUES(:firstname, :lastname, :email, :username, :password)", [
    'firstname' => $first_name,
    'lastname' => $last_name,
    'email' => $email,
    'username' => $username,
    'password' => password_hash($password, PASSWORD_BCRYPT),
  ]);

  Authenticator::login($user);
  $_SESSION['signupSuccess'] = true;
  header('location: /login');
  die();
}
