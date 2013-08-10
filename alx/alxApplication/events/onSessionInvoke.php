<?php

$event->addCallback(function()
{
	$adp = alxApplication::getConfigVar('adapter', 'session');
	$adapter = "alxSessionAdapter_$adp";
		
	alx::load("Session/$adp", "SessionAdapter_$adp");

	alxSessionManager::setAdapter(new $adapter);
		
	alxSessionManager::$_config = alxApplication::getConfigVar('session');

	return alxSessionManager::startSession();
});