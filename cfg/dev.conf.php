<?php

# Initalize Config
$config = new alxApplicationConfig('dev');
$config->addHost('blacklist.tools4games.com');
$config->addHost('5.9.124.165');
$config->addHost('t4g.alchemicality.com');
$config->addHost('alchemicality.com');

# Application
$config->app = new stdClass;
$config->app->id = 'test';
$config->app->path = '/var/www/t4g_blacklist/';
$config->app->prot = 'http';
$config->app->host = 'blacklist.tools4games.com';
$config->app->port = 80;
$config->app->root = '/';

$config->app->global_view 		= 'global';
$config->app->controller_dir 	= 'controllers/';
$config->app->model_dir 			= 'models/';
$config->app->view_dir 				= 'views/';
$config->app->shared_path     = 'views/shared';
$config->app->charset					= 'UTF-8';
$config->app->minifyCode			= false;

# Defaults
$config->default = new stdClass;
$config->default->controller = 'home';
$config->default->action = 'index';

# Database
$config->database = new stdClass;
$config->database->adapter = 'MySQL';
$config->database->host = 'localhost';
$config->database->user = 't4g_blacklist';
$config->database->pwd = '3fEJqMUtyXdVWzFJ';
$config->database->db = 't4g_blacklist';

/*
$config->database->user = 'tools4games';
$config->database->pwd = '4ynT4Y2nAn8bnuVN';
$config->database->db = 'tools4games';
*/

# Session
$config->session = new stdClass;
$config->session->adapter = 'PHP';
$config->session->name 		= 't4g_com';

# Request
$config->request = new stdClass;
$config->request->enabledStacks = array
(
  'GET' 	=> $_GET,
	'POST' 	=> $_POST
);

$config->request->vars = new stdClass;
$config->request->vars->controller = 'controller';
$config->request->vars->action 		= 'action';

# Routing
$config->routing = new stdClass;
$config->routing->enableRewrite = true;
$config->routing->hardRedirect = true;
