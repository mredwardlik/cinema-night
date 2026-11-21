<?php
/**
 * Runs the callback only for AJAX (X-Requested-With: XMLHttpRequest) requests.
 */
class Ajax
{
	public static function is()
	{
		return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
	}

	public static function catch($UserFunction, $withoutError = true)
	{
		if (self::is())
		{
			$UserFunction();
		}
		elseif ($withoutError)
		{
			json_response(['error' => 'Bad Request'], 400);
		}
	}
}
