<?php

$event->addCallback(function()
{
	// Create HtAccess File and add to Application File Collection
	$htaccess = new alxFile_HtAccess;
	
	alxApplication::$fileCollection->addFile($htaccess);
	
	return true;
});