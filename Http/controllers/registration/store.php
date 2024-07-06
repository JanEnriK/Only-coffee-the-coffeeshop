<?php

use Core\App;
use Core\Database;
use Core\Validator;
use Core\Authenticator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files
require __DIR__ . '/../../../PHPMailer/src/Exception.php';
require __DIR__ . '/../../../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../../../PHPMailer/src/SMTP.php';

// Initialize the database connection
$db = App::resolve('Core\Database');

// Get form input
$first_name = $_POST['firstname'];
$last_name = $_POST['lastname'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

// Initialize error array
$errors = [];

// Validate form input
if (!Validator::email($email)) {
    $errors['email'] = "Please provide a valid email.";
}

if (!Validator::string($password, 7, 255)) {
    $errors['password'] = "Please provide a password of at least 7 characters.";
}

// Check for existing usernames and emails
$checkUsername = $db->query("SELECT * FROM tblemployees")->get();

foreach ($checkUsername as $usernameExist) {
    if ($usernameExist['username'] == $username) {
        $errors["username"] = "The username '$username' has already been taken.";
    }
    if ($usernameExist['email'] == $email) {
        $errors["email"] = "An account with this email '$email' is currently existing.";
    }
}

// If there are validation errors, return to the registration form
if (!empty($errors)) {
    return view('views/registration/create.view.php', [
        'heading' => 'Register',
        'errors' => $errors,
    ]);
}

// Check if the user already exists
$user = $db->query("SELECT * FROM tblemployees WHERE email = :email", ['email' => $email])->find();

if ($user) {
    header('location: /');
    die();
} else {
    // Register the user
    $db->query("INSERT INTO tblemployees (firstname, lastname, email, username, password) VALUES (:firstname, :lastname, :email, :username, :password)", [
        'firstname' => $first_name,
        'lastname' => $last_name,
        'email' => $email,
        'username' => $username,
        'password' => password_hash($password, PASSWORD_BCRYPT),
    ]);

    // Send welcome email
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                 // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                             // Enable SMTP authentication
        $mail->Username   = 'geoolarte04@gmail.com';           // SMTP username
        $mail->Password   = 'yclnmikifufhamaq';            // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption
        $mail->Port       = 587;                              // TCP port to connect to

        // Recipients
        $mail->setFrom('no-reply@example.com', 'Your Service');
        $mail->addAddress($email, $first_name);               // Add recipient

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Welcome to Our Service';
        $mail->Body    = 'Hi ' . $first_name . ',<br>Thank you for registering with us.';

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    // Log in the user
    Authenticator::login($user);
    $_SESSION['signupSuccess'] = true;
    header('location: /login');
    die();
}
?>
