<?php
/**
 * Thin wrapper over $_SESSION.
 *
 * Values are stored as plain arrays, e.g. $_SESSION['user'] = ['id' => 1, 'login' => 'alex', ...]
 */
class Session
{

	public static function start()
	{
		if (session_status() !== PHP_SESSION_ACTIVE)
		{
			session_set_cookie_params([
				'httponly' => true,
				'samesite' => 'Lax',
				'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
			]);
			session_start();
		}
	}

	public static function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	/**
	 * Session::get('user')          -> whole value
	 * Session::get('user', 'login') -> one field of an array value
	 */
	public static function get($key, $field = null, $default = null)
	{
		if (!isset($_SESSION[$key]))
		{
			return $default;
		}

		if ($field === null)
		{
			return $_SESSION[$key];
		}

		return is_array($_SESSION[$key]) && array_key_exists($field, $_SESSION[$key])
			? $_SESSION[$key][$field]
			: $default;
	}

	public static function forget($key)
	{
		unset($_SESSION[$key]);
	}

	public static function destroy()
	{
		$_SESSION = [];
		session_destroy();
	}

	public static function is($name)
	{
		return isset($_SESSION[$name]);
	}
}
