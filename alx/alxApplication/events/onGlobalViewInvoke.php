<?php

$event->addCallback(function($event)
{
	if(alxRequestHandler::getHTTPState() != 'POST')
	{
		$globalView = new alxView(alxApplication::getConfigVar('global_view', 'app'));
			
		foreach(alxController::$views as $containerId => $data)
		{
			$view = new alxView($data[0]);

			if($data[2])
			{
				$data[2]($view);
			}

			$globalView->addContainer($containerId, $view, $data[1]);
		}
		
		$event->addVar('globalView', $globalView);

		if($globalView->render(true))
		{
			return true;
		}
	}
	else
	{
		return true;
	}
	
	return false;
});