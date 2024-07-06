<?php

use Core\App;
use Core\Database;

// Initialize the database connection
$db = App::resolve('Core\Database');

// Get form input
$token = $_POST['token'] ?? '';
$new_password = $_POST['password'] ?? '';
$confirm_password = $_POST['password_confirmation'] ?? '';

// Initialize error array
$errors = [];

// Validate form input
if (strlen($new_password) < 7) {
    $errors['password'] = "Please provide a password of at least 7 characters.";
}

if ($new_password !== $confirm_password) {
    $errors['password_confirmation'] = "Passwords do not match.";
}

// Check if the token is valid
$reset = $db->query("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW()", ['token' => $token])->find();

if (!$reset) {
    $errors['token'] = "Invalid or expired token.";
}

// If there are validation errors, return to the reset password form
if (!empty($errors)) {
    return view('registration/reset.view.php', [
        'errors' => $errors,
        'token' => $token,
    ]);
}

// Update the user's password
$db->query("UPDATE tblemployees SET password = :password WHERE email = :email", [
    'password' => password_hash($new_password, PASSWORD_BCRYPT),
    'email' => $reset['email'],
]);

// Delete the password reset token
$db->query("DELETE FROM password_resets WHERE token = :token", ['token' => $token]);

header('Location: /login');
exit();

?>
