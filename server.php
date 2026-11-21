<?php
/**
 * Router for PHP's built-in web server:
 *
 *     php -S localhost:8000 server.php
 *
 * Serves existing static files as is and sends everything else to index.php,
 * the same way .htaccess does on Apache.
 */

$path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file) && !preg_match('#^/(config|storage|_controllers|_views|web|bin)/#', $path) && pathinfo($file, PATHINFO_EXTENSION) !== 'php')
{
	return false;
}

require __DIR__ . '/index.php';
