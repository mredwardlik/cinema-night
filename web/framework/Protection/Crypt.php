<?php
/**
 * Cryptographically secure random tokens.
 */
class Crypt
{
	public static function bin2hexPHP($bytes = 32)
	{
		return bin2hex(random_bytes($bytes));
	}
}
