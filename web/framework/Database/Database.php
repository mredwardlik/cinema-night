<?php
require_once ROOT . '/web/rb/rb.php';

/**
 * Connects RedBeanPHP to SQLite (default) or MySQL, see config/database.php.
 */
class Database
{
	public static function connect()
	{
		$config = Config::getdatabase();

		if ($config['driver'] === 'mysql')
		{
			R::setup(
				'mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'] . ';charset=utf8mb4',
				$config['user'],
				$config['password']
			);
		}
		else
		{
			$directory = dirname($config['path']);

			if (!is_dir($directory))
			{
				mkdir($directory, 0775, true);
			}

			R::setup('sqlite:' . $config['path']);
		}

		if (!R::testConnection())
		{
			http_response_code(500);
			exit('[Problems with DB Connection]');
		}
	}
}

Database::connect();
