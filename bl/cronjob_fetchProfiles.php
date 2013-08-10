<?php

header('content-type: text/plain');

require_once '/var/www/t4g_blacklist/alx/alxToolkit.php';
require_once '/var/www/t4g_blacklist/alx/alxDatabase/alxDatabaseManager.php';
require_once '/var/www/t4g_blacklist/alx/alxMVC/alxModel.php';

require_once '/var/www/t4g_blacklist/lib/functions.php';
require_once '/var/www/t4g_blacklist/lib/statsConfig.php';

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
alxDatabaseManager::connect();

$upd = time() - (86400 * 7);

$get = alxDatabaseManager::fetchMultiple
("
  SELECT * FROM profiles WHERE updated <= {$upd} LIMIT 10
");


