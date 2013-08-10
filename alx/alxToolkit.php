<?php

class alx
{
	const VERSION = 'alpha 0.6.9';

	public static function load($dir, $file, $prefix='alx', $ext='php')
	{
		$prefix2 = $prefix;
		
		if(strpos($file, $prefix) !== false)
		{
			$prefix2 = null;
		}
		
		$proc = dirname(__FILE__)  . "/{$prefix}{$dir}/{$prefix2}{$file}.{$ext}";
		
                if(!file_exists($proc))
		{
		 echo "cannot load {$proc}";
		}

		require_once $proc;
	}
	
	public static function loadShared($path, $ext='php')
	{
		require_once dirname(__FILE__)  . "/alxApplication/shared/{$path}.{$ext}";
	}
	
	public static function getToolkitPath()
	{
		return dirname(__FILE__) . '/';
	}
}
