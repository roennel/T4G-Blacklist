<?php

$event->addCallback(function()
{
	alxRequestHandler::$_config = alxApplication::getConfigVar('request');
	
	$init = alxRequestHandler::initialize();
	
	alxApplication::$systemLog->addLog('Request Vars', alxRequestHandler::getStacks());

	return $init;
});