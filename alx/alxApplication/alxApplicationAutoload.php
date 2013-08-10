<?php

# Model/Controller Autoload
function __autoload($class)
{
	switch(true)
	{
		case (strpos($class, 'alxModule') !== false):
		
			list(,$module) = explode('_', $class);
			alx::load("Modules/{$module}", $class); 
			return;
			
		break;
		
		case (strpos($class, 'alxFile') !== false):

			alx::load('FileSystem/types', $class);
			return;

		break;
		
		case (stripos($class, 'Model') !== false):
		
			$file = alxApplication::getConfigVar('model_dir', 'app') . $class . '.php';
			$file = alxApplication::getConfigVar('path', 'app') . $file;

		break;
		
		case (stripos($class, 'Controller') !== false):
		
			$file = alxApplication::getConfigVar('controller_dir', 'app') . $class . '.php';
			$file = alxApplication::getConfigVar('path', 'app') . $file;
			
		break;
	}

	if(file_exists($file))
	{
		include_once $file;
	}
  else
  {
    // exit("Cannot load File '{$file}'");
    // trigger_error("Unable to load file: $file", E_USER_WARNING);
  }
}