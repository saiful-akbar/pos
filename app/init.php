<?php

function baseUrl($param = null)
{
  if ($param != null) {
    return substr(trim($param), 0, 1) == "/" ? trim($param) : "/" . trim($param);
  }
  return "localhost:8000";
}

spl_autoload_register(function ($class) {
  require __DIR__ . "/Core/{$class}.php";
});
