<?php


use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

$feedback = $db->query("SELECT * FROM tblfeedback JOIN tblemployees ON employeeID = customerid ORDER BY RAND() LIMIT 5")->get();


view('index.view.php', [
  'feedback' => $feedback
]);
