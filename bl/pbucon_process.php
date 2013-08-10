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

$now = time();

// GUID MATCHING
$guids = alxDatabaseManager::query
("
  SELECT pbGuidId, GUID, name FROM pb_guids WHERE processed = '0' ORDER BY pbGuidId
");

$processed = [0, 0];

while($guid = $guids->fetch())
{
  $c = alxDatabaseManager::query
  ("
    SELECT nucleusId FROM pb_guid_match WHERE GUID = '{$guid->GUID}' LIMIT 1
  ")->fetch();
   
  if(@$c->nucleusId <= 0)
  {
    $nucleusId = alxDatabaseManager::query
    ("
      SELECT nucleusId FROM profiles WHERE name = '{$guid->name}' LIMIT 1
    ")->fetch();   
    
    if((int) @$nucleusId->nucleusId > 0)
    {
      alxDatabaseManager::query
      ("
        INSERT INTO pb_guid_match SET GUID = '{$guid->GUID}', nucleusId = '{$nucleusId->nucleusId}'
      ");
    }
  }
  
  // $processed[] = "pbGuidId = '{$guid->pbGuidId}'";
  
  if(!$processed[0])
  {
    $processed[0] = $guid->pbGuidId;
  }
  $processed[1] = $guid->pbGuidId;
}

sleep(1);

// $t = implode(' OR ', $processed);

alxDatabaseManager::query
("
  UPDATE pb_guids SET processed = '1' 
  WHERE 
  (
    pbGuidId BETWEEN {$processed[0]} AND {$processed[1]}
  )
");

// LOG PROCESSING
$logs = alxDatabaseManager::query
("
  SELECT pbLogId, serverId, date, msg FROM pb_log WHERE processed = '0' ORDER BY date ASC
");

$processed = [0, 0];

while($log = $logs->fetch())
{
  switch(true)
  {
    case strpos($log->msg, ' by GGC-Stream.NET!') !== false:
      # PBSV: PB UCON "ggc_85.114.136.131"@85.114.136.131:6584 [game.sayAll "simpopo got banned for MD5TOOL #9002 by GGC-Stream.NET!"]
      
      $s = explode(' got banned for ', $log->msg);
      
      $name = explode('"', $s[0]);
      $name = $name[count($name)-1];
      $name = trim($name);
      $type = str_replace(' by GGC-Stream.NET!', '', $s[1]);
      $type = explode('"', $type)[0];
      
      // "Violation (AIMBOT) #50729" -> array("Violation", "(AIMBOT) #50729")
      $type = explode(' ', $type, 2);
      
      $banned = alxDatabaseManager::query
      ("
        SELECT b.banId FROM bans AS b, profiles AS p WHERE p.nucleusId = b.nucleusId AND p.name = '{$name}' LIMIT 1
      ");
      
      $banId = (int) @$banned->banId;
        
      // $check = alxDatabaseManager::query
      // ("
      //  SELECT pbGgcBanId FROM pb_ggc_bans WHERE type = '{$type[0]}' AND type2 = '{$type[1]}' AND name = '{$name}' AND banId = '{$banId}' LIMIT 1
      // ")->fetch();
      
      // if(@$check->pbGgcBanId <= 0)
      // {
      alxDatabaseManager::query
      ("
        INSERT INTO pb_ggc_bans SET date = '{$now}', type = '{$type[0]}', type2 = '{$type[1]}', name = '{$name}', banId = '{$banId}'
      ");
      // }
      
    break;
    
    case strpos($log->msg, 'PBSV: Player GUID Computed') !== false:
    
      $msg = str_replace('PBSV: Player GUID Computed ', '', $log->msg);
      
      $s = explode(' ', $msg);
      
      $guid = str_replace('(-)', '', $s[0]);
      $ip = explode(':', $s[3])[0];
      $name = $s[4];
      
      alxDatabaseManager::query
      ("
        INSERT INTO pb_guids SET GUID = '{$guid}', name = '{$name}'
      ");
      
      alxDatabaseManager::query
      ("
        INSERT INTO pb_guid_ips SET GUID = '{$guid}', ip = '{$ip}', date = '{$now}' 
      ");
      
    break; 
  }

  // $processed[] = "pbLogId = '{$log->pbLogId}'";
  
  if(!$processed[0])
  {
    $processed[0] = $log->pbLogId;
  }
  $processed[1] = $log->pbLogId;
}

sleep(1);

// $t = implode(' OR ', $processed);

alxDatabaseManager::query
("
  UPDATE pb_log SET processed = '1' 
  WHERE 
  (
    pbLogId BETWEEN {$processed[0]} AND {$processed[1]}
  )
");


