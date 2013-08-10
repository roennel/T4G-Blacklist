<?php


exit;
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

$time = time();

$servers = alxDatabaseManager::fetchMultiple
("
  SELECT serverId FROM plugins WHERE plugin = 'ITEM_LIMITER' AND active = '1'
");

foreach($servers as $server)
{
  $cmd = "nice php /var/www/t4g_blacklist/bl/plugin_itemLimiter_fetchLoadout.php {$server->serverId} 2>&1 & echo $!";
  pclose(popen($cmd, 'r'));
  
  $banned = alxDatabaseManager::fetchMultiple
  ("
    SELECT * FROM plugin_itemLimiter_items WHERE serverId = '{$server->serverId}' AND active = '1'
  ");

  $loadouts = alxDatabaseManager::fetchMultiple
  ("
    SELECT * FROM plugin_itemLimiter_loadouts WHERE serverId = '{$server->serverId}'
  ");
  
  foreach($loadouts as $loadout)
  {
    foreach($banned as $ban)
    {
      if($loadout->itemId == $ban->itemId)
      {
        $name = getNiceWeaponName($GLOBALS['weaponIds'][$loadout->itemId]);
        $reason = 'T4G Item Limiter > Item [' . trim($name) . '] is not allowed on this Server';
        
        alxDatabaseManager::query
        ("
          INSERT INTO plugin_commandCache 
          SET 
            serverId = '{$server->serverId}',
            command = 'kick \"$loadout->playerIndex\" \"{$reason}\"',
            playerIndex = '{$loadout->playerIndex}',
            date = '{$time}'
        ");
        
        var_dump($GLOBALS['weaponIds'][$loadout->itemId]);
      }
    }
  }
}

