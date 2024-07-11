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
  date_default_timezone_set('Asia/Manila');
  $today = new DateTime('now', new DateTimeZone('Asia/Manila'));
  $today = $today->format('Y-m-d H:i:s');

  if (empty($errors)) {
    $db->query("INSERT INTO tblfeedback(title, feedback_desc, customerid, feedback_datetime) VALUES(:title, :feedback_desc, :customerid, :feedback_datetime)", ['title' => $_POST['title'], 'feedback_desc' => $_POST['feedback_desc'], 'customerid' => $_SESSION['user']['id'], 'feedback_datetime' => $today]);
  }

  header('location: /testimonial');
  die();

  // view('testimonial.view.php', [
  //   'feedback' => $feedback,
  // ]);