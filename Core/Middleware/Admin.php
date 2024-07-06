<?php 

namespace Core\Middleware;

class Admin 
{
  public function handle() 
  {
    if ($_SESSION['user']['position'] !== 'admin') 
    {
        header('location: /');
        die();
    }
  }
}

?>