<?php

class alxSessionAdapter_PHP extends alxSessionAdapter
{
	public static function startSession()
	{
		return @session_start();
	}
	
	public static function stopSession()
	{	
		return session_destroy();
	}
	
	public static function getVar($var)
	{
		return $_SESSION[$var];
	}
	
	public static function setVar($var, $value)
	{
		$_SESSION[$var] = $value;
	}
}