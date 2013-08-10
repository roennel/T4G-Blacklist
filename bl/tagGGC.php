<?php

// header('content-type: text/plain');

// set_time_limit(300);

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
// require_once '/var/www/t4g_blacklist/lib/statsConfig.php';
// require_once '/home/roennel/p4ftool/functions.php';

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

$doTag = @$_GET['do'] == '1' ? true : false;

$names = explode("\r\n", file_get_contents('../data/ggc_banlist.txt'));

$submissions = alxDatabaseManager::query("SELECT submissionId, name FROM submissions INNER JOIN profiles ON nucleusId = targetNucleusId WHERE done='0' AND submissionId NOT IN (SELECT submissionId FROM submission_tags WHERE tagId='1')");

$subIds = array();
$processed = 0;

while($item = $submissions->fetch())
{
  if(!in_array($item->submissionId, $subIds) and in_array($item->name, $names))
  {
    $subIds[] = $item->submissionId;
    
    if($doTag)
    {
      alxDatabaseManager::query("INSERT INTO submission_tags SET submissionId = '{$item->submissionId}', tagId = '1', userId = '0'");
      $processed++;
    }
  }
}

showResults($subIds, $processed);

if(@$_GET['checkBans'])
{
  $bans = $submissions = alxDatabaseManager::query("SELECT submissionId, name FROM bans AS b INNER JOIN profiles AS p ON p.nucleusId = b.nucleusId WHERE blacklistId != '1'");
  $subIds = array();
  $processed = 0;

  while($item = $submissions->fetch())
  {
    if(!in_array($item->submissionId, $subIds) and in_array($item->name, $names))
    {
      $subIds[] = $item->submissionId;
    }
  }

  showResults($subIds, $processed);
}

function showResults($subIds, $processed)
{
  $subIds = implode(',', $subIds);

  $submissions = alxDatabaseManager::query("
    SELECT *,
      (SELECT GROUP_CONCAT(name SEPARATOR ' / ') FROM profiles p WHERE p.nucleusId = targetNucleusId) AS names
    FROM submissions
    WHERE submissionId IN ({$subIds})
  ");
  $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submissions WHERE submissionId IN ({$subIds})")->fetch();

  $subCount = @$count->c ?: 0;
  
  echo "<div style=\"font-family: monospace\">\r\n";
  echo "Count: " . $subCount . "<br />\r\n";
  echo "Submissions:<br />\r\n";

  while($item = $submissions->fetch())
  {
    echo " - " . "<a href=\"http://blacklist.tools4games.com/en/modPanel/submissionDetail?submissionId={$item->submissionId}\">" . $item->submissionId . "</a>" . " " . $item->type . " " . $item->names . "<br />\r\n";
  }

  echo "Processed: " . $processed . "<br />\r\n";
  echo "<br />\r\n";
  echo "</div>\r\n";
}


$d = mtf() - $s;

var_dump($d);
