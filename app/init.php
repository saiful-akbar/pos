<?php

/**
 * Fungsi base url
 */
function baseUrl($query = null)
{
	$url = "http://localhost:8080/apps/php-mvc";

	if ($query != null) {
		$url .= htmlspecialchars(trim($query));
	}

	return $url;
}


spl_autoload_register(function ($class) {
	require __DIR__ . "/Core/{$class}.php";
});
