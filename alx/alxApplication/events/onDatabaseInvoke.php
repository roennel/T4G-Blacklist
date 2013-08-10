<?php

$event->addCallback(function()
{
		$adp = alxApplication::getConfigVar('adapter', 'database');
		
		if($adp)
		{
			$adapter = "alxDatabaseAdapter_$adp";
		
			alx::load("Database/$adp", "DatabaseAdapter_$adp");

			alxDatabaseManager::setAdapter(new $adapter);
		
			alxDatabaseManager::$_config = alxApplication::getConfigVar('database');
		
			return alxDatabaseManager::connect();
		}
});