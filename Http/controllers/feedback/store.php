<?php

use Core\App;
use Core\Authenticator;
use Core\Database;
use Core\Validator;

$db = App::resolve('Core\Database');

$feedback = $db->query("SELECT * FROM tblfeedback JOIN tbluser ON id = customerid")->get();

$errors = [];

if (!Validator::string($_POST['title'], 1, 30)) {
  $errors['body'] = "A body of no more than 30 characters is required.";
}

if (!Validator::string($_POST['feedback_desc'], 1, 100)) {
  $errors['body'] = "A body of no more than 100 characters is required.";
}

if (!empty($errors)) {
  return view('testimonial.view.php', [
    'errors' => $errors,
    'feedback' => $feedback,
  ]);
}

if (empty($errors)) {
  $db->query("INSERT INTO tblfeedback(title, feedback_desc, customerid) VALUES(:title, :feedback_desc, :customerid)", ['title' => $_POST['title'], 'feedback_desc' => $_POST['feedback_desc'], 'customerid' => $_SESSION['user']['id']]);
}

header('location: /testimonial');
die();

// view('testimonial.view.php', [
//   'feedback' => $feedback,
// ]);