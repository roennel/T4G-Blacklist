<?php

if(!@$_GET['debug'] && !@$noImage)
{
  header('content-type: image/png');
}

require_once '/home/roennel/p4ftool/alx/alxToolkit.php';
require_once '/home/roennel/p4ftool/alx/alxDatabase/alxDatabaseManager.php';
require_once '/home/roennel/p4ftool/alx/alxMVC/alxModel.php';

require_once '../lib/functions.php';
require_once '../lib/statsConfig.php';

$database = new stdClass;
$database->adapter = 'MySQL';
$database->host = 'localhost';
$database->user = 't4g_blacklist';
$database->pwd = '3fEJqMUtyXdVWzFJ';
$database->db = 't4g_blacklist';

$adapter = "alxDatabaseAdapter_{$database->adapter}";
    
alx::load("Database/{$database->adapter}", "DatabaseAdapter_{$database->adapter}");

alxDatabaseManager::setAdapter(new $adapter);
alxDatabaseManager::$_config = $database;   
alxDatabaseManager::$_debug = true;
alxDatabaseManager::connect();