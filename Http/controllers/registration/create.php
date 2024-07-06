<?php

use Core\App;
use Core\Database;

$db = App::resolve('Core\Database');

$coffee = $db->query("SELECT * FROM tblcoffeeshop")->find();
view(
    'registration/create.view.php',
    [
        'coffee' => $coffee,
    ]
);
