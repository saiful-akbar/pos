<?php

/**
 * Fungsi base url
 */
function baseUrl($path = null)
{
	$url = "http://localhost:8080/apps/php-mvc";

	if ($path != null) {
		$url .= substr(trim($path), 0, 1) == "/" ? trim($path) : "/".trim($path);
	}

  	return $url;
}


spl_autoload_register(function ($class) {
  require __DIR__ . "/Core/{$class}.php";
});
