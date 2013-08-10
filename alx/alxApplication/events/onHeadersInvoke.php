<?php

$event->addCallback(function()
{
  // Hide App Details
	header('X-Powered-By: ALX Toolkit');
	header('Server: -');
        
	// Set Charset
	header('Charset: ' . alxApplication::getConfigVar('charset', 'app'));
	
	return true;
});