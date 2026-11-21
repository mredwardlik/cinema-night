<?php
/**
 * Renders a PHP template from the views directory.
 */
class View
{
	public static function render($fileName)
	{
		include ROOT . Config::getdirs('views') . '/' . $fileName;
	}
}
