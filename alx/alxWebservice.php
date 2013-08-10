<?php

require_once 'alxToolkit.php';

alx::load('Webservice',	'Webservice');
alx::load('Database',		'DatabaseManager');
alx::load('Session',		'SessionManager');
alx::load('Request',		'RequestHandler');
alx::load('Routing',		'RoutingManager');

$app = new alxApplication;