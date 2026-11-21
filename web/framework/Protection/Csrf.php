<?php
/**
 * CSRF protection.
 *
 * One random token per session. Every POST request must send it either in the
 * form field (config/csrf.php -> key) or in the X-CSRF-Token header
 * (jQuery sends the header automatically, see _views/parts/scripts.php).
 */
class Csrf
{
	private static function badresponce()
	{
		http_response_code(419);
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode(['error' => 'Сессия устарела. Обновите страницу.'], JSON_UNESCAPED_UNICODE);
		exit;
	}

	public static function key()
	{
		return Config::getcsrf('key');
	}

	public static function token()
	{
		if (!Session::is(self::key()))
		{
			Session::set(self::key(), Crypt::bin2hexPHP());
		}

		return Session::get(self::key());
	}

	public static function field()
	{
		echo '<input type="hidden" name="' . e(self::key()) . '" value="' . e(self::token()) . '">';
	}

	public static function catch()
	{
		if (!Config::getcsrf('power') || $_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			return;
		}

		$sent = $_POST[self::key()] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

		if (!is_string($sent) || !hash_equals(self::token(), $sent))
		{
			self::badresponce();
		}
	}
}
