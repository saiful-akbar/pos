<?php

/**
 * Fungsi base url
 * 
 * @param null $path
 * 
 * @return String
 */
function baseUrl($path = null)
{
  if ($path != null) {
    $url = substr(trim($path), 0, 1) == "/" ? trim($path) : "/" . trim($path);
    return $url;
  }
  return "localhost:8000/";
}


spl_autoload_register(function ($class) {
  require __DIR__ . "/Core/{$class}.php";
});
