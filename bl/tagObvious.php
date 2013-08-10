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
$weapons = array(
	'3001' => ['M16A2', '0.40'],
	'3005' => ['G3A4', '0.40'],
	'3007' => ['AEK971', '0.40'],
	'3011' => ['SCARL', '0.40'],
	'3046' => ['STG77AUG', '0.40'],
	'3062' => ['M4A1', '0.40'],
	'3067' => ['AN94', '0.40'],
	'3071' => ['416CARBINE', '0.40'],
	'3075' => ['XM8', '0.40'],
	'3110' => ['AK47', '0.40'],
	'3114' => ['F2000', '0.40'],
	'3120' => ['FAMAS', '0.40'],
	'3127' => ['L85A2', '0.55'],
	
	'3003' => ['M249SAW', '0.45'],
	'3013' => ['MG3', '0.40'],
	'3014' => ['PKM', '0.40'],
	'3015' => ['M60', '0.40'],
	'3048' => ['MG36', '0.40'],
	'3063' => ['M240', '0.40'],
	'3068' => ['QJY88', '0.40'],
	'3072' => ['FNPARA', '0.40'],
	'3076' => ['XM8AR', '0.40'],
	'3113' => ['RPK74M', '0.40'],
	'3116' => ['M27IAR', '0.40'],
	'3121' => ['QBB95', '0.45'],
	'3128' => ['PECHENEG', '0.40'],
	
	'3012' => ['UMP', '0.40'],
	'3016' => ['MP7', '0.40'],
	'3017' => ['FNP90', '0.40'],
	'3018' => ['PP2000', '0.40'],
	'3047' => ['MP5', '0.45'],
	'3064' => ['UZI ', '0.40'],
	'3069' => ['AKS74U', '0.40'],
	'3073' => ['9A91', '0.40'],
	'3078' => ['XM8C', '0.40'],
	'3112' => ['PP19', '0.40'],
	'3117' => ['PDWR', '0.40'],
	'3122' => ['ASVAL', '0.45'],
	'3129' => ['G53', '0.45'],
	
	'3004' => ['SV98', '0.90'],
	'3022' => ['M95', '0.95'],
	'3023' => ['SVD', '0.50'],
	'3024' => ['M24', '0.95'],
	'3045' => ['SVU', '0.45'],
	'3065' => ['M14', '0.40'],
	'3066' => ['M110', '0.55'],
	'3077' => ['GOL', '0.95'],
	'3111' => ['L96', '0.95'],
	'3119' => ['SKS', '0.55'],
	'3126' => ['M82A3', '0.95'],
	'3070' => ['VSS', '0.50'],
	
	'3006' => ['M9', '0.80'],
	'3019' => ['M1911', '0.80'],
	'3020' => ['MP412', '0.80'],
	'3021' => ['MP443', '0.80'],
	'3037' => ['M1911 Veteran', '0.80'],
	'3041' => ['MP443 Veteran', '0.80'],
	'3043' => ['P226', '0.80'],
	'3051' => ['MP443 Elite', '0.80'],
	'3052' => ['M1911 Elite', '0.80'],
	'3115' => ['DEAGLE', '0.80'],
	'3118' => ['DEAGLE STEEL', '0.80'],
	'3123' => ['SAWEDOFF', '0.80']
);

foreach($weapons as $wepaonId => $data)
	$limits[$data[1]][] = $wepaonId;

foreach($limits as $limit => $data)
{
	$ids = implode(',', $data);
	$result[] = alxDatabaseManager::query
	("
		SELECT s.submissionId, s.targetNucleusId, ws.weaponId, MAX(ws.headshotratio) AS headshotratio
		FROM submissions AS s
		LEFT JOIN profiles AS p ON s.targetNucleusId = p.nucleusId
		LEFT JOIN profile_weaponStats AS ws ON p.soldierId = ws.soldierId
		WHERE s.postponed = '0' AND s.done = '0' AND ws.kills >= '100' AND ws.weaponId IN ({$ids}) AND ROUND(headshotratio, 2) > '{$limit}'
		GROUP BY s.submissionId
		ORDER BY ws.weaponId, s.submissionId
	");
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Tag Obvious</title>
</head>
<body>
<table>
<tr><th>submissionId</th><th>nucleusId</th><th>weaponId</th><th>weaponName</th><th>headshotratio</th></tr>

<?php
  foreach($result as $query)
  {
	while($item = $query->fetch())
	{
		$tag = alxDatabaseManager::query
		("
		  SELECT COUNT(*) AS c
		  FROM submission_tags
		  WHERE submissionId = '{$item->submissionId}' AND tagId = '4'
		  LIMIT 1
		")->fetch();
		
		if($tag->c == 0)
		{
		  $hsrate = number_format($item->headshotratio * 100, 0);
		  $weaponName = $weapons[$item->weaponId][0];
		  echo "<tr><td>{$item->submissionId}</td><td>{$item->targetNucleusId}</td><td>{$item->weaponId}</td><td>{$weaponName}</td><td>{$hsrate}%</td></tr>";
		  
		  alxDatabaseManager::query
		  ("
			INSERT INTO submission_tags SET
			  submissionId = '{$item->submissionId}',
			  tagId = '4',
			  userId = '0'
		  ");

		  $i++;
		}
	}
  }
?>

</table>
<div>Count: <?php echo $i ?></div>
</body>
</html>