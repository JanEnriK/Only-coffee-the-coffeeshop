<?php 

use Core\App;
use Core\Container;
use Core\Database;

$container = new Container();

$container->bind('Core\Database', function() {
  $config = require(base_path("config.php"));
  return new Core\Database($config['database'], $config['account']['username'], $config['account']['password']);
});

App::setContainer($container);

?>