<?php
/**
 * Loads config/*.php files once and gives read access to them.
 */
class Config
{
	private static $files = ['app', 'dirs', 'global', 'routes', 'database', 'csrf'];

	private static $config = [];

//	******************************************
//	*******************SET********************
//	******************************************

	public static function init()
	{
		foreach (self::$files as $name)
		{
			self::$config[$name] = require ROOT . '/config/' . $name . '.php';
		}
	}

//	******************************************
//	*******************GET********************
//	******************************************

	public static function get($file, $key = '', $default = null)
	{
		$values = self::$config[$file] ?? [];

		if ($key === '')
		{
			return $values;
		}

		return array_key_exists($key, $values) ? $values[$key] : $default;
	}

	public static function getapp($key = '')      { return self::get('app', $key); }
	public static function getdirs($key = '')     { return self::get('dirs', $key); }
	public static function getglobal($key = '')   { return self::get('global', $key); }
	public static function getroutes($key = '')   { return self::get('routes', $key); }
	public static function getdatabase($key = '') { return self::get('database', $key); }
	public static function getcsrf($key = '')     { return self::get('csrf', $key); }
}

Config::init();
