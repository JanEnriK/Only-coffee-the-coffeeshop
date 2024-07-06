<?php

use Core\App;
use Core\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require __DIR__ . '/../../../PHPMailer/src/Exception.php';
require __DIR__ . '/../../../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../../../PHPMailer/src/SMTP.php';

// Initialize the database connection
$db = App::resolve('Core\Database');

// Get form input
$email = $_POST['email'];

// Initialize error array
$errors = [];

// Validate form input
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Please provide a valid email.";
}

// Check if the user exists
$user = $db->query("SELECT * FROM tblemployees WHERE email = :email", ['email' => $email])->find();

if (!$user) {
    $errors['email'] = "No account found with that email.";
}

// If there are validation errors, return to the forgot password form
if (!empty($errors)) {
    return view('registration/forget.view.php', [
        'errors' => $errors,
    ]);
}

// Generate a unique password reset token
$token = bin2hex(random_bytes(5));

// Save the token in the database with an expiration time (e.g., 1 hour)
$db->query("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)", [
    'email' => $email,
    'token' => $token,
    'expires_at' => date('Y-m-d H:i:s', strtotime('+7 hour')),
]);

// Send password reset email
$mail = new PHPMailer(true);
try {
    // Server settings
    $mail->SMTPDebug = 0;                                 // Disable verbose debug output
    $mail->isSMTP();                                      // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                 // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                             // Enable SMTP authentication
    $mail->Username   = 'geoolarte04@gmail.com';          // SMTP username
    $mail->Password   = 'yclnmikifufhamaq';               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption
    $mail->Port       = 587;                              // TCP port to connect to

    // Recipients
    $mail->setFrom('no-reply@example.com', 'Only Coffee');
    $mail->addAddress($email);                            // Add recipient

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Password Reset Request';
    $mail->Body    = 'Your password reset token is: ' . $token;

    $mail->send();
    // Redirect to a page that tells the user to check their email for the token
    header('Location: /check-email');
    exit();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
