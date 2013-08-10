<?php

#$s = $_SERVER['argv'][1] ?: 0;

header('content-type: text/plain');

set_time_limit(300);

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
require_once '/var/www/t4g_blacklist/lib/statsConfig.php';
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

$s = mtf();

$time = time();
$minGames = 50;

$sortKey = 'games';
$additionalSort = '';
$start = 0;
$limit = 1000;

// DELETE
alxDatabaseManager::query
("
  DELETE FROM ranking_cache
");

$count = alxDatabaseManager::query
("
  SELECT COUNT( DISTINCT soldierId ) AS c
  FROM `profiles`
  WHERE soldierId >100000
")->fetch()->c;

var_dump($count);

$per = 20000;

$diff = $count / $per;
$diff = ceil($diff);

var_dump($diff);

$proc = function($start, $limit, $rankingId, $minGames, $sortKey)
{
  $cmd = "nice php /var/www/t4g_blacklist/bl/ranking_cache_do.php {$start} {$limit} {$rankingId} {$minGames} {$sortKey} 2>&1 & echo $!";
  var_dump($cmd);
  pclose(popen($cmd, 'r'));
  usleep(500000); // 0.5 sec
};

for($i=0;$i<$diff;$i++)
{
  $start = $i * $per;
  $limit = $per;
  
  $proc($start, $limit, 'global_50_kills', 50, 'kills');
  $proc($start, $limit, 'global_50_games', 50, 'games');
  $proc($start, $limit, 'global_50_timePlayed', 50, 'timePlayed');
  $proc($start, $limit, 'global_50_vehicleKills', 50, 'vehicleKills');
  $proc($start, $limit, 'global_50_deaths', 50, 'deaths');
  $proc($start, $limit, 'global_50_killratio', 50, 'killratio');
  $proc($start, $limit, 'global_50_cpcaps', 50, 'cpaps');
  $proc($start, $limit, 'global_50_cpneut', 50, 'cpneut');
  $proc($start, $limit, 'global_50_ispm', 50, 'ispm');
  $proc($start, $limit, 'global_50_vspm', 50, 'vspm');
  $proc($start, $limit, 'global_50_tspm', 50, 'tspm');
  $proc($start, $limit, 'global_50_wins', 50, 'wins');
  $proc($start, $limit, 'global_50_losses', 50, 'losses');
  $proc($start, $limit, 'global_50_winratio', 50, 'winratio');
  $proc($start, $limit, 'global_50_accuracy', 50, 'accuracy');
  $proc($start, $limit, 'global_50_headshotratio', 50, 'headshotratio');
  $proc($start, $limit, 'global_50_meleeKills', 50, 'meleeKills');
  $proc($start, $limit, 'global_50_killedByMelee', 50, 'killedByMelee');
  $proc($start, $limit, 'global_50_roadKills', 50, 'roadKills');
  $proc($start, $limit, 'global_50_runover', 50, 'runover');
  $proc($start, $limit, 'global_50_suicides', 50, 'suicides');
  $proc($start, $limit, 'global_50_destroyedVehicles', 50, 'destroyedVehicles');
  $proc($start, $limit, 'global_50_bestScore', 50, 'bestScore');
  
  foreach($GLOBALS['weaponIds'] as $weaponId => $weaponCode)
  {
    $proc($start, $limit, "weapon_{$weaponId}_kills", $weaponId, 'kills');
  }
}

$d = mtf() - $s;

var_dump($d);
