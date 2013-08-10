<?php

class alxRequestHandler
{
	public static $_config;
	
	protected static $stacks = array();
	
	public static function initialize()
	{
		if(!self::$_config)
		{
			return false;
		}
		
		foreach(self::$_config->enabledStacks as $stackID => $stack)
		{
			self::enableStack($stackID, $stack);
		}

		return true;
	}
	
	public static function getHTTPState()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}
	
	public static function enableStack($stackID, $stack)
	{
		self::$stacks[$stackID] = $stack;
	}
	
	public static function getStack($stackID)
	{
		return self::$stacks[$stackID];
	}
	
	public static function getStacks()
	{
		return self::$stacks;
	}
	
	public static function getVar($var)
	{
		foreach(self::$stacks as $stackID => $stack)
		{
			if(array_key_exists($var, $stack))
			{
				$val = $stack[$var];
				$val = rawurldecode($val);
				$val = urldecode($val);
				
				return $val;
			}
		}
		
		return false;
	}
	
	public static function setVar($var, $value, $stack='internal')
	{
		self::$stacks[$stack][$var] = $value;
	}
	
	public static function getController()
	{
		$default = alxApplication::getConfigVar('default');
		$routing = alxApplication::getConfigVar('routing');
		
		if($routing->hardRedirect && !self::getVar(self::$_config->vars->controller))
		{
			header('location: ' . alxLinkString(array(self::$_config->vars->controller => $default->controller)));
			
			return;
		}
		
		return self::getVar(self::$_config->vars->controller)
			?: $default->controller;
	}
	
	public static function getAction()
	{
		$default = alxApplication::getConfigVar('default');
		
		return self::getVar(self::$_config->vars->action)
			?: $default->action;
	}
}