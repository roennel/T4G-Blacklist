<?php

$event->addCallback(function()
{
	$reqController = alxRequestHandler::getController();
	$reqAction = alxRequestHandler::getAction();

	$clController = ucFirst($reqController) . 'Controller';
		
	$controller = new $clController;
		
	$stackGET 	= (object) alxRequestHandler::getStack('GET');
	$stackPOST 	= (object) alxRequestHandler::getStack('POST');
		
	if(alxRequestHandler::getHTTPState() == 'GET')
	{
		if(!method_exists($controller, $reqAction))
		{ 
			$controller->redirectDefault(array
			(
				'error' => 'badInput',
				'redir' => $_SERVER["REQUEST_URI"]
			));
		}
			
		$controller->$reqAction($stackGET);
	}
	else
	{
    $ac = "post_{$reqAction}";
		$controller->$ac($stackPOST, $stackGET);
	}
		
	return true;
});