<?php
/**
 * Runs the callback only for POST requests.
 */
class Post
{
	public static function catch($UserFunction, $withoutError = true)
	{
		if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST')
		{
			$UserFunction();
		}
		elseif ($withoutError)
		{
			json_response(['error' => 'Method Not Allowed'], 405);
		}
	}

	/** Trimmed string from $_POST */
	public static function str($key, $default = '')
	{
		$value = $_POST[$key] ?? $default;
		return is_string($value) ? trim($value) : $default;
	}
}
