<?php

$event->addCallback(function()
{
	/** Default Routes
		*/
		
	$external = new alxRoute
	(
		'external',
		'%external',
		array
		(
			'external' => ''
		),
		ALX_ROUTE_EXTERNAL
	);
		
	$defaultCssRoute = new alxRoute
	(
		'defaultCssRoute',
		'css/%css',
		array
		(
			'css' => ''
		)
	);
	
	$defaultJsRoute = new alxRoute
	(
		'defaultJsRoute',
		'js/%js',
		array
		(
			'js' => ''
		)
	);
	
	$defaultImageRoute = new alxRoute
	(
		'defaultImageRoute',
		'img/%img',
		array
		(
			'img' => ''
		)
	);

	$defaultControllerAction = new alxRoute
	(
		'defaultControllerAction',
		'%lang/%controller/%action',
		array
		(
			'lang' => '',
			'controller' => '',
			'action' => ''
		)
	);
		
	$defaultController = new alxRoute
	(
		'defaultController',
		'%lang/%controller',
		array
		(
			'lang' => '',
			'controller' => ''
		)
	);

	
	if(alxApplication::getConfigVar('enableRewrite', 'routing'))
	{
		$htaccess = alxApplication::$fileCollection->getFile('.htaccess');

		alxRoutingManager::addRewriteRules($htaccess);
	}
		
	alxRoutingManager::addRoutes
	(
		$external,
		
		$defaultCssRoute,
		$defaultJsRoute,
		$defaultImageRoute,

		$defaultControllerAction,
		$defaultController
	);
	
	return true;
});
