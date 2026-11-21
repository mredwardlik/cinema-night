<?php
//	*******************************************************
	define('ROOT', dirname(__DIR__));
	error_reporting(E_ALL);
//	*******************************************************
	require_once ROOT . '/web/framework/Error/ErrorHandle.php';
	(new \lib\ErrorHandle())->register();
	require_once ROOT . '/web/framework/Config/Config.php';
	ini_set('display_errors', Config::getapp('debug') ? '1' : '0');
	date_default_timezone_set(Config::getapp('timezone'));
//	*******************************************************
	require_once ROOT . '/web/framework/Session/Session.php';
	Session::start();
	require_once ROOT . '/web/framework/Config/G.php';
	require_once ROOT . '/web/framework/Database/Database.php';
	require_once ROOT . '/web/framework/View/View.php';
	require_once ROOT . '/web/framework/Request/Ajax.php';
	require_once ROOT . '/web/framework/Request/Post.php';
	require_once ROOT . '/web/framework/Protection/Crypt.php';
	require_once ROOT . '/web/framework/Protection/Csrf.php';
	require_once ROOT . '/web/framework/Routing/Router.php';
	require_once ROOT . '/web/framework/Support/Str.php';
	require_once ROOT . '/web/framework/Support/Icons.php';
	require_once ROOT . '/web/framework/Support/helpers.php';
	spl_autoload_register(function ($class) {
		$file = ROOT . Config::getdirs('models') . '/' . $class . '.php';
		if (is_file($file)) require_once $file;
	});
//	*******************************************************
	if (PHP_SAPI !== 'cli')
	{
		(new Router())->run();
	}
//	*******************************************************
