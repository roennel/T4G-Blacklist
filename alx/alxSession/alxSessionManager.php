<?php

class alxSessionManager
{
	public static $_config;
	
	protected static $adapter;
	
	public static function setAdapter(alxSessionAdapter $adapter)
	{
		self::$adapter = $adapter;
	}

	public static function startSession()
	{
		return self::$adapter->startSession();
	}
	
	public static function stopSession()
	{	
		return self::$adapter->stopSession();
	}
	
	public static function getVar($var)
	{
		return self::$adapter->getVar($var);
	}
	
	public static function setVar($var, $value)
	{
		self::$adapter->setVar($var, $value);
	}
	
	public static function getSessionId()
	{
		return session_id();
	}
}

alx::load('Session', 'SessionAdapter');