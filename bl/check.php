<?php

$s = $_SERVER['argv'][1] ?: 0;
$e = $_SERVER['argv'][2] ?: 19;

header('content-type: text/plain');

$timeout = 2;

set_time_limit(19);

error_reporting(E_ALL);

ini_set('default_socket_timeout', $timeout);

function mtf()
{
    list($usec, $sec) = explode(" ", microtime());
    $ss = ((float)$usec + (float)$sec);
    
    return $ss;
}

require_once '/var/www/t4g_blacklist/alx/alxToolkit.php';
require_once '/var/www/t4g_blacklist/alx/alxDatabase/alxDatabaseManager.php';
require_once '/var/www/t4g_blacklist/alx/alxMVC/alxModel.php';

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
alxDatabaseManager::connect();

require_once '/var/www/t4g_blacklist/lib/BFP4F_Rcon/Base.php';
require_once '/var/www/t4g_blacklist/lib/BFP4F_Rcon/Players.php';
require_once '/var/www/t4g_blacklist/lib/BFP4F_Rcon/Server.php';
require_once '/var/www/t4g_blacklist/lib/BFP4F_Rcon/Chat.php';

require_once '/home/roennel/p4ftool/oxygen/server.php';
require_once '/home/roennel/p4ftool/oxygen/player.php';
require_once '/home/roennel/p4ftool/oxygen/player/netstat.php';

use T4G\Oxygen as Oxygen;
use BFP4F_Rcon as rc;

function check($serverId)
{
  $cacheTime = 300;
  $server = alxDatabaseManager::query("SELECT ip, port, pwd, serverId FROM servers WHERE serverId = '{$serverId}' LIMIT 1")->fetch();
  
  $_blacklists = alxDatabaseManager::query("SELECT blacklistId, serverBlacklistId, kicks FROM server_blacklists WHERE serverId = '{$serverId}'");
  $blacklists = array();
  
  while($item = $_blacklists->fetch())
  {
    $blacklists[$item->blacklistId] = $item;
  }

  $rc = new BFP4F_Rcon\Base();
  $rc->ip   = $server->ip;
  $rc->port = $server->port;
  $rc->pwd  = $server->pwd;

  $version = $rc->init();
 
  if(empty($version) or trim($version) == '')
  {
    alxDatabaseManager::query("UPDATE servers SET `online` = '0' WHERE serverId = '{$serverId}' LIMIT 1");
      
    return;
  }
  
  if(!$rc->login()) 
  {
    alxDatabaseManager::query("UPDATE servers SET `noLogin` = '1' WHERE serverId = '{$serverId}' LIMIT 1");
      
    return;
  }
  
  $time = time();
  
  alxDatabaseManager::query("UPDATE servers SET `online` = '1', lastOnline = '{$time}', noLogin = '0' WHERE serverId = '{$serverId}' LIMIT 1");
  
  $rcp = new BFP4F_Rcon\Players();
  $players = $rcp->fetch();
  
  $plugin_itemLimiter = alxDatabaseManager::query
  ("
    SELECT * FROM plugins WHERE plugin = 'ITEM_LIMITER' AND serverId = '{$serverId}' LIMIT 1
  ")->fetch();
  
  if(@$plugin_itemLimiter->active == '1')
  {
    alxDatabaseManager::query
    ("
      DELETE FROM 
        plugin_itemLimiter_fetch 
      WHERE 
        serverId = '{$serverId}'
    ");
  }
  
  foreach($players as $player)
  {
    // COMMAND CACHE
    if($player->connected == '1')
    {
    $cc = alxDatabaseManager::fetchMultiple
    ("
      SELECT * FROM plugin_commandCache WHERE serverId = '{$serverId}'
    ");
    
    foreach($cc as $command)
    {
      $valid = true;
      
      foreach($players as $player)
      {
        if($command->playerIndex == $player->index)
        {
          if($player->connected != '1') $valid = false; 
        }
      }
      
      if(!$valid) continue;
      
      BFP4F_Rcon\Base::query($command->command);
      
      alxDatabaseManager::query
      ("DELETE FROM plugin_commandCache WHERE pluginCommandCacheId = '{$command->pluginCommandCacheId}' LIMIT 1");
    }
    }
    
    // ITEM LIMITER
    if($player->nucleusId > 0 && $player->profileId > 0 && @$plugin_itemLimiter->active == '1')
    {
      $cl = alxDatabaseManager::query
      ("
        SELECT date FROM plugin_itemLimiter_loadouts WHERE nucleusId = '{$player->nucleusId}' AND soldierId = '{$player->profileId}' LIMIT 1
      ")->fetch();
      
      if(time() >= ((int) @$cl->date) + $cacheTime)
      {
        alxDatabaseManager::query
        ("
          INSERT INTO 
            plugin_itemLimiter_fetch 
          SET 
            serverId = '{$serverId}',
            nucleusId = '{$player->nucleusId}',
            soldierId = '{$player->profileId}',
            playerIndex = '{$player->index}'
        ");
      }
    }
    
    $c1 = alxDatabaseManager::query
    ("
      SELECT profileId FROM profiles WHERE soldierId = '{$player->profileId}' LIMIT 1
    ")->fetch();
    
    if(@$c1->profileId <= 0)
    {
      alxDatabaseManager::query
      ("
        INSERT INTO profiles SET nucleusId = '{$player->nucleusId}', soldierId = '{$player->profileId}', kit = '{$player->kit}', level = '{$player->level}', name = '{$player->name}'
      ");
    }
    
    // Update Last Seen date for this Soldier
    /* COMMENTED OUT FOR NOW
    $c2 = alxDatabaseManager::query
    ("
      SELECT soldierId FROM profile_lastSeen WHERE soldierId = '{$player->profileId}' LIMIT 1
    ")->fetch();
    
    if(@$c2->soldierId <= 0)
    {
      alxDatabaseManager::query
      ("
        INSERT INTO profile_lastSeen SET soldierId = '{$player->profileId}', date = '{$time}', serverId = '{$serverId}'
      ");
    }
    else
    {
      alxDatabaseManager::query
      ("
        UPDATE profile_lastSeen SET date = '{$time}', serverId = '{$serverId}' WHERE soldierId = '{$player->profileId}'
      ");
    }
    */
    /* Old Method - Leads to crazy values on the primary autoincrement column
      alxDatabaseManager::query
      ("
        INSERT INTO profile_lastSeen SET soldierId = '{$player->profileId}', date = '{$time}', serverId = '{$serverId}' ON DUPLICATE KEY UPDATE date = '{$time}'
      ");
    */
    
    
    $nucleus = array($player->nucleusId);
    /*
    $clones = alxDatabaseManager::query("SELECT nucleusIdOld FROM profile_clones WHERE nucleusIdNew = '{$player->nucleusId}'");
    
    while($item = $clones->fetch())
    {
      $nucleus[] = $item->nucleusIdOld;
    }
    */
    foreach($nucleus as $nucleusId)
    {
    
    $check = alxDatabaseManager::query("SELECT banId, blacklistId FROM bans WHERE nucleusId = '{$nucleusId}' AND active = '1' LIMIT 1")->fetch();
    
    if(@$check->banId > 0)
    { // Ban Found
      if(array_key_exists($check->blacklistId, $blacklists) && $player->connected == '1')
      { // Player Banned, Blacklist Active
    
        $label = alxDatabaseManager::query("SELECT label FROM blacklists WHERE blacklistId = '{$check->blacklistId}' LIMIT 1")->fetch();
        $reason = "|ccc| T4G Blacklist -> Match found in List: {$label->label}";
       
        // Kick Player
        $rcp->kick($player->index, $reason);
        
        // Update Kick Incremental
        $serverBlacklistId = $blacklists[$check->blacklistId]->serverBlacklistId;
        $newKick = $blacklists[$check->blacklistId]->kicks + 1;
        alxDatabaseManager::query("UPDATE server_blacklists SET kicks = '{$newKick}' WHERE serverBlacklistId = '{$serverBlacklistId}'");
        
        // Add Log
        $time = time();
        alxDatabaseManager::query("INSERT INTO kickLog SET serverId = '{$serverId}', banId = '{$check->banId}', date = '{$time}'");
      }
    }
    
    }
    
    // Glitch Protect
    if($player->connected == '1' && $player->level <= 0)
    {
      $reason = "|ccc| T4G Blacklist -> Glitch Protection Safety Kick (Level -1)";
       
      // Kick Player
      $rcp->kick($player->index, $reason);
      
      // Add Log
      $time = time();
      alxDatabaseManager::query("INSERT INTO kickLog SET serverId = '{$serverId}', nucleusId = '{$player->nucleusId}', banId = '0', type ='1', date = '{$time}'");  
      
      // Check Profile
      #alxDatabaseManager::query("DELETE FROM profiles WHERE nucleusId = '{$player->nucleusId}' AND soldierId = '{$player->profileId}' LIMIT 1")->fetch();
      #alxDatabaseManager::query("INSERT INTO profiles SET nucleusId = '{$player->nucleusId}', soldierId = '{$player->profileId}', level = '{$player->level}', name = '{$player->name}'");
      
    }
  }
}

// Start Execute Log
$start = mtf();
$date = time();
//alxDatabaseManager::query("INSERT INTO executeLog SET date = '{$date}', args = '{$s} - {$e}'");

$id = mysql_insert_id();

$totalServers = 0;

// Server Iteration
$servers = alxDatabaseManager::query("SELECT serverId FROM servers ORDER BY serverId ASC LIMIT $s,$e");


//alxDatabaseManager::query("DELETE FROM chainLog");

while($server = $servers->fetch())
{
  // Add Chain Log
  $now = mtf();
  //alxDatabaseManager::query("INSERT INTO chainLog SET serverId = '{$server->serverId}', `start` = '{$now}'");
  
  // Process Check
  check($server->serverId);
  
  // End Chain Log
  $now = mtf();
  //alxDatabaseManager::query("UPDATE chainLog SET `end` = '{$now}' WHERE serverId = '{$server->serverId}'");
  
  $totalServers++;
}

$duration = mtf() - $start;
$ram = round((memory_get_usage(true) / 1048576), 2);

//alxDatabaseManager::query("UPDATE executeLog SET duration = '{$duration}', ram = '{$ram}', servers = '{$totalServers}' WHERE executeLogId = '{$id}' LIMIT 1");


alxDatabaseManager::disconnect();
