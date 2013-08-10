<?php

require_once 'alxToolkit.php';

alx::load('SystemLog',	'SystemLog');
alx::load('Database',		'DatabaseManager');
alx::load('Session',		'SessionManager');
alx::load('Request',		'RequestHandler');
alx::load('Routing',		'RoutingManager');
alx::load('MVC',				'Model');
alx::load('MVC',				'View');
alx::load('MVC',				'Controller');
alx::load('Events',			'Events');
alx::load('Modules',		'ModuleCollection');
alx::load('FileSystem',	'FileSystem');
alx::load('Application', 	'ApplicationConfig');
alx::load('Application', 	'ApplicationConfigCollection');
alx::load('Application', 	'ApplicationAutoload');
alx::load('Application',	'ApplicationStage');
alx::load('Application/exceptions', 	'ApplicationException');

alx::load('Application','Application');
alx::load('Application','ApplicationInit');