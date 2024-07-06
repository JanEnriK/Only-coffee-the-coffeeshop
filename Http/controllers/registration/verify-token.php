<?php

use Core\App;
use Core\Database;

// Initialize the database connection
$db = App::resolve('Core\Database');

// Get the token from the GET request
$token = $_GET['token'];

// Initialize error array
$errors = [];

// Check if the token is valid
$reset = $db->query("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW()", ['token' => $token])->find();

if (!$reset) {
    $errors['token'] = "Invalid or expired token.";
    return view('registration/check-email.view.php', [
        'errors' => $errors,
    ]);
}

// If the token is valid, redirect to the reset password page with the token as a hidden input
return view('registration/reset.view.php', [
    'token' => $token,
]);

?>
