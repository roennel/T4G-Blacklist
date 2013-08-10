<?php

exit;

$s = $_SERVER['argv'][1] ?: 0;

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

$serverId = $s;
$time = time();

if($s > 0)
{
  $players = alxDatabaseManager::fetchMultiple
  ("
    SELECT * FROM plugin_itemLimiter_fetch WHERE serverId = '{$serverId}' 
  ");

  
  foreach($players as $player)
  {
    alxDatabaseManager::query
    ("
      DELETE FROM plugin_itemLimiter_fetch WHERE pluginItemLimiterFetchId = '{$player->pluginItemLimiterFetchId}' LIMIT 1
    ");
  
    $url = "http://battlefield.play4free.com/en/profile/loadout/{$player->nucleusId}/{$player->soldierId}?_={$time}";
      
    $loadout = json_decode(file_get_contents($url));

    $items = array();
    
    foreach($loadout->data->equipment as $item)
    {
      if(!array_key_exists($item->type, $items))
      {
        $items[$item->type] = array();
      }
      
      $items[$item->type][] = $item->id;
    }
    
    foreach($items as $itemType => $itemIds)
    {
      foreach($itemIds as $itemId)
      {
        alxDatabaseManager::query
        ("
          INSERT INTO 
            plugin_itemLimiter_loadouts 
          SET 
            serverId = '{$serverId}',
            nucleusId = '{$player->nucleusId}',
            soldierId = '{$player->soldierId}',
            type = '{$itemType}',
            itemId = '{$itemId}',
            date = '{$time}',
            playerIndex = '{$player->playerIndex}'
        ");
      }
    }
    
    $old = alxDatabaseManager::query
    ("SELECT `value` FROM plugin_itemLimiter_stats WHERE serverId = '{$serverId}' LIMIT 1")->fetch();
  
    $new = $old->value + 1;
  
    alxDatabaseManager::query
    ("UPDATE plugin_itemLimiter_stats SET `value` = {$new} WHERE serverId = '{$serverId}'");
  }
}

  $d2 = time() - 1800;
  
  alxDatabaseManager::query
  ("DELETE FROM plugin_itemLimiter_loadouts WHERE date <= {$d2}");
  
  alxDatabaseManager::query
  ("DELETE FROM plugin_commandCache WHERE date <= {$d2}");