<?php

$start = $_SERVER['argv'][1] ?: 0;
$limit = $_SERVER['argv'][2] ?: 10;
$rankingId = $_SERVER['argv'][3] ?: 10;
$minGames = $_SERVER['argv'][4] ?: 50;
$sortKey = $_SERVER['argv'][5] ?: 'games';

header('content-type: text/plain');

set_time_limit(10000);

error_reporting(E_ALL);

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


$rank = $start + 1;
$additionalSort = '';
$time = time();

if($type == 'weapon')
{
  $items = alxDatabaseManager::fetchMultiple
    ("
      SELECT 
        ps.*, 
        p.name, 
        p.kit, 
        p.nucleusId,
        p.soldierId,
        p.level
      FROM 
        profile_weaponStats AS ps, 
        profiles AS p
      WHERE 
        ps.soldierId = p.soldierId  
      AND 
        ps.kills > 100 
      AND 
        NOT EXISTS
        (
          SELECT * FROM profile_weaponStats AS pw WHERE pw.soldierId = ps.soldierId AND pw.date > ps.date
        )
      AND 
        (SELECT COUNT(*) FROM bans AS b WHERE b.nucleusId = p.nucleusId AND b.active = '1') = 0  
      AND 
       ps.weaponId = '{$minGames}'
      GROUP BY 
        ps.soldierId 
      ORDER BY 
        ps.{$sortKey} DESC, 
        ps.date DESC 
      LIMIT {$start},{$limit}
    ");
}
else
{
$items = alxDatabaseManager::fetchMultiple
    ("
      SELECT 
        ps.*, 
        p.name, 
        p.kit, 
        p.nucleusId,
        p.soldierId,
        p.level
      FROM 
        profile_stats AS ps, 
        profiles AS p
      WHERE 
        ps.soldierId = p.soldierId 
      AND 
        ps.games > {$minGames}
      AND 
        ps.kills > 100 
      AND 
        NOT EXISTS
        (
          SELECT * FROM profile_stats AS pw WHERE pw.soldierId = ps.soldierId AND pw.date > ps.date
        )
      AND 
        (SELECT COUNT(*) FROM bans AS b WHERE b.nucleusId = p.nucleusId AND b.active = '1') = 0 
      GROUP BY 
        ps.soldierId 
      ORDER BY 
        (ps.{$sortKey}{$additionalSort}) DESC, 
        ps.date DESC 
      LIMIT {$start},{$limit}
    ");
}

foreach($items as $item)
{
  alxDatabaseManager::query
  ("
    INSERT INTO ranking_cache 
    SET 
      soldierId = '{$item->soldierId}',
      rankingId = '{$rankingId}',
      rank = '{$rank}',
      date = '{$time}'
  ");
   
  $rank++;
}

    