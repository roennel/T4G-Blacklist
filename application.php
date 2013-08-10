<?php
/* Safeties */
if(!@$_GET['controller'])
{
  header('location: /en/home');
  exit('foo');
}

if($_SERVER['HTTP_HOST'] != 'ranking.tools4games.com' && $_GET['controller'] == 'ranking')
{
  header('location: http://ranking.tools4games.com/');
  exit();
}


if($_SERVER['HTTP_HOST'] == 'ranking.tools4games.com' && $_GET['controller'] != 'ranking')
{
  header('location: /en/ranking');
  exit();
}

set_time_limit(120);

/* PHP Init */
header('charset: UTF-8');

@session_start();
error_reporting(E_ALL);

ini_set('default_socket_timeout', 5);

/* Debug */
if(@$_GET['debug'])
{
  $_SESSION['auth'] = 'true';
  $_SESSION['debug'] = true;
  
  error_reporting(E_ALL);
}

/* Libraries */
require 'lib/functions.php';
require 'lib/language.php';
require 'lib/scoreStats.php';
require 'lib/fetchStats.php';
require 'lib/statsConfig.php';
include 'lib/PHPMailer_v5.1/class.phpmailer.php';
 
/* Language */
loadLanguage(getLang());

/* alxToolkit Init */
switch($_SERVER['HTTP_HOST'])
{
	default:
		
		define('ALX_APP_PATH', '/var/www/t4g_blacklist');
    
		require_once 'alx/alxFramework.php';
}

register_shutdown_function(function()
{
  alxDatabaseManager::disconnect();
});
