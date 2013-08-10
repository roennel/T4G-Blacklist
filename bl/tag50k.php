<?php

set_time_limit(300);

error_reporting(E_ALL);

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

$i = 0;

$result = alxDatabaseManager::query
("
  SELECT s.submissionId, s.targetNucleusId, MAX(ps.bestScore) AS bestScore
  FROM submissions AS s
  LEFT JOIN profiles AS p ON s.targetNucleusId = p.nucleusId
  LEFT JOIN profile_stats AS ps ON p.soldierId = ps.soldierId
  WHERE s.postponed = '0' AND s.done = '0' AND bestScore >= '50000'
  GROUP BY s.submissionId
");
$result2 = alxDatabaseManager::query
("
  SELECT s.submissionId, s.targetNucleusId, MAX(ps.killStreak) AS killStreak
  FROM submissions AS s
  LEFT JOIN profiles AS p ON s.targetNucleusId = p.nucleusId
  LEFT JOIN profile_stats AS ps ON p.soldierId = ps.soldierId
  WHERE s.postponed = '0' AND s.done = '0' AND killStreak >= '200'
  GROUP BY s.submissionId
");

?>

<!DOCTYPE html>
<html>
<head>
  <title>Tag 50k+</title>
</head>
<body>
<table>
<tr><th>submissionId</th><th>nucleusId</th><th>bestScore</th><th>killStreak</th></tr>

<?php
  while($item = $result->fetch())
  {
	$tag = alxDatabaseManager::query
	("
	  SELECT COUNT(*) AS c
	  FROM submission_tags
	  WHERE submissionId = '{$item->submissionId}' AND tagId = '10'
	  LIMIT 1
	")->fetch();
	
	if($tag->c == 0)
	{
	  echo "<tr><td>{$item->submissionId}</td><td>{$item->targetNucleusId}</td><td>{$item->bestScore}</td><td>-</td></tr>";
	  
	  alxDatabaseManager::query
	  ("
	    INSERT INTO submission_tags SET
		  submissionId = '{$item->submissionId}',
		  tagId = '10',
		  userId = '0'
	  ");
	  
	  $i++;
	}
  }
  while($item = $result2->fetch())
  {
	$tag = alxDatabaseManager::query
	("
	  SELECT COUNT(*) AS c
	  FROM submission_tags
	  WHERE submissionId = '{$item->submissionId}' AND tagId = '10'
	  LIMIT 1
	")->fetch();
	
	if($tag->c == 0)
	{
	  echo "<tr><td>{$item->submissionId}</td><td>{$item->targetNucleusId}</td><td>-</td><td>{$item->killStreak}</td></tr>";
	  
	  alxDatabaseManager::query
	  ("
	    INSERT INTO submission_tags SET
		  submissionId = '{$item->submissionId}',
		  tagId = '10',
		  userId = '0'
	  ");
	  
	  $i++;
	}
  }
?>

</table>
<div>Count: <?php echo $i ?></div>
</body>
</html>