<?php
//*******************************************************************************
//							SETTING START INFO
//*******************************************************************************
//
//	driver: 'sqlite' (default, works out of the box) or 'mysql'
//
//	Every value can be overridden with an environment variable:
//	DB_DRIVER, DB_PATH, DB_HOST, DB_NAME, DB_USER, DB_PASSWORD

return [
	'driver'   => getenv('DB_DRIVER') ?: 'sqlite',

	// SQLite
	'path'     => getenv('DB_PATH') ?: ROOT . '/storage/cinema-night.sqlite',

	// MySQL
	'host'     => getenv('DB_HOST') ?: 'localhost',
	'dbname'   => getenv('DB_NAME') ?: 'cinema_night',
	'user'     => getenv('DB_USER') ?: 'root',
	'password' => getenv('DB_PASSWORD') ?: ''
];
