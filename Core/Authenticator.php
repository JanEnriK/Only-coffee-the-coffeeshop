<?php

namespace Core;

use Core\Session;

class Authenticator
{
  public function attemp($email, $password)
  {
    $users = (App::resolve(Database::class))->query("SELECT * FROM tblemployees WHERE email = :email", ['email' => $email])->find();

    if ($users) {
      if (password_verify($password, $users['password'])) {
        $this->login([
          'id' => $users['employeeID'],
          'email' => $email,
          'position' => $users['position'],
        ]);

        return true;
      }
    }

    return false;
  }

  public static function login($user)
  {
    if (is_array($user) && isset($user['id'], $user['email'], $user['position'])) {
      $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'position' => $user['position'],
      ];

      session_regenerate_id();
    } else {
      // Handle the case where $user is not an array or is missing required fields
      // You can log an error, display a message, or perform any other appropriate action here
    }
  }


  public static function logout()
  {
    Session::destroy();
  }
}
