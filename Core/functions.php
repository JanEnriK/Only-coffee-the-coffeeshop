<?php

use Core\Response;

function dd($value) {
  echo "<pre>";
  var_dump($value);
  echo "</pre>";
  die();
}


function abort($value = 404) 
{
http_response_code($value);
require base_path("views/{$value}.php");
die();
}

function isUrl($value) {
  return $_SERVER['REQUEST_URI'] === $value;
}

function authorize($condition, $status = Response::FORBIDDEN) {
  if (!$condition) {
    abort($status);
  }
}

function base_path($path) {
  return BASE_PATH . $path;
}

function view($path, $attributes = []) {
  extract($attributes);
  require base_path('views/' . $path);
} 

function redirect($path) {
  header("location: {$path}");
  die();
}

function old($key, $default = '') {
  return Core\Session::get('old')[$key] ?? $default;
}
