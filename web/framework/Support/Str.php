<?php
/**
 * String helpers.
 */
class Str
{
	/**
	 * Random string: N uppercase letters + N lowercase letters + N digits,
	 * e.g. Str::random(2, 2, 6) -> "KQxe274913"
	 */
	public static function random($STR_UP_LENGTH = 0, $STR_DOWN_LENGTH = 0, $STR_NUMBER_LENGTH = 0)
	{
		$alphabets = [
			[$STR_UP_LENGTH,     'ABCDEFGHIJKLMNOPQRSTUVWXYZ'],
			[$STR_DOWN_LENGTH,   'abcdefghijklmnopqrstuvwxyz'],
			[$STR_NUMBER_LENGTH, '1234567890']
		];

		$RandomString = '';

		foreach ($alphabets as [$length, $chars])
		{
			for ($i = 0; $i < $length; $i++)
			{
				$RandomString .= $chars[random_int(0, strlen($chars) - 1)];
			}
		}

		return $RandomString;
	}
}
