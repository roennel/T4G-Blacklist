<?php

error_reporting(E_ALL);

header('content-type: text/plain');

require_once '/home/roennel/p4ftool/alx/alxToolkit.php';
require_once '/home/roennel/p4ftool/alx/alxDatabase/alxDatabaseManager.php';
require_once '/home/roennel/p4ftool/alx/alxMVC/alxModel.php';

require_once '/home/roennel/p4ftool/functions.php';

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

require 'pbucon.php';

$servers = alxDatabaseManager::query
("
  SELECT serverId FROM servers
");


while($server = $servers->fetch())
{
  checkPBLog($server->serverId);
}
