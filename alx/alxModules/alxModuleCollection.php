<?php

alx::load('Modules', 'Module');

class alxModuleCollection
{
	protected static $_modules = array();
	
	public static function getModule($id)
	{
		return self::$_modules[$id];
	}
	
	public static function getModules()
	{
		return self::$_modules;
	}
	
	public static function addModule(alxModule $module)
	{
		self::$_modules[$module->getId()] = $module;
	}
}