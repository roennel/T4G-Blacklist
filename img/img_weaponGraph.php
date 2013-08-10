<?php require 'img_init.php';

set_time_limit(500);

$weaponId = @$_GET['weaponId'] ?: 3000;
$weapon = getNiceWeaponName($GLOBALS['weaponIds'][$weaponId]);

$limit = @$_GET['limit'] ?: 99999999;
$limit2 = $limit * 2;

$key = @$_GET['key'] ?: 'headshotratio';

$diffs = @$_GET['steps'] ?: 10;

$kills = @$_GET['kills'] ?: 200;

$valModifiers = array
(
  'default' => function($v)
  {
    return number_format($v, 0, '.', '\'');
  },
  'headshotratio' => function($v)
  {
    return number_format($v * 100, 0, '.', '\'') . '%';
  },
  'accuracy' => function($v)
  {
    return number_format($v * 100, 0, '.', '\'') . '%';
  }
);

$keyWheres = array
(
  'headshotratio' => array("{$key} > '0'", "{$key} < '1'"),
  'accuracy' => array("{$key} > '0'", "{$key} < '1'"),
  'ownedPermanent' => array("1 = 1")
);

$width = @$_GET['width'] ?: 400;
$ratio = array(16, 9);
$height = ($width / $ratio[0]) * $ratio[1];

$img = imageCreateTrueColor($width + 40, $height + 20);

$base = imageColorAllocateAlpha($img, 10, 10, 10, 0);
imageFill($img, 0, 0, $base); 

$clr = array
(
  imageColorAllocateAlpha($img, 255, 255, 255, 0),
  imageColorAllocateAlpha($img, 255, 255, 255, 90),
  imageColorAllocateAlpha($img, 255, 255, 255, 90),
  imageColorAllocateAlpha($img, 255, 20, 20, 40),
  imageColorAllocateAlpha($img, 20, 255, 20, 40),
  imageColorAllocateAlpha($img, 100, 100, 100, 0)
);

$contentHeight = (0 + ($height-16) - 30) - 2;
$contentStartY = 46;

$getKeyWheres = function($where=true) use($key, $keyWheres)
{
  if(!array_key_exists($key, $keyWheres))
  {
    return $where ? " WHERE {$key} > '0'" : "{$key} > '0'";
  } 
  
  return $where ? ' WHERE ' . implode(' AND ', $keyWheres[$key]) : implode(' AND ', $keyWheres[$key]);
};


$data = alxDatabaseManager::query
("
  SELECT 
  COUNT(weaponStatId) AS c, 
  (SELECT {$key} FROM profile_weaponStats" . $getKeyWheres() . " AND weaponId = '{$weaponId}' ORDER BY {$key} ASC LIMIT 1) AS lowest, 
  (SELECT {$key} FROM profile_weaponStats" . $getKeyWheres() . " AND weaponId = '{$weaponId}' ORDER BY {$key} DESC LIMIT 1) AS highest 
  FROM profile_weaponStats 
  WHERE weaponId = '{$weaponId}' AND kills > '{$kills}' AND " . $getKeyWheres(false) . "
")->fetch();

$invalidCount = alxDatabaseManager::query
("
  SELECT COUNT(weaponStatId) AS c 
  FROM profile_weaponStats 
  " . $getKeyWheres() . " AND kills > '{$kills}' AND weaponId = '{$weaponId}' AND soldierId IN (SELECT p.soldierId FROM profiles AS p, bans AS b WHERE b.nucleusId = p.nucleusId AND b.blacklistId = '1')
")->fetch();

$invalidCount = $invalidCount->c;

$validCount = alxDatabaseManager::query
("
  SELECT COUNT(weaponStatId) AS c 
  FROM profile_weaponStats 
  " . $getKeyWheres() . " AND kills > '{$kills}' AND weaponId = '{$weaponId}' AND soldierId NOT IN (SELECT p.soldierId FROM profiles AS p, bans AS b WHERE b.nucleusId = p.nucleusId AND b.blacklistId = '1')
")->fetch();

$validCount = $validCount->c;

$step = $data->highest / $diffs;

imageString($img, 3, 10, 10, $weapon, $clr[0]);

imageString($img, 2, 150, 10, $invalidCount . ' Entries', $clr[3]);
imageString($img, 2, 250, 10, $validCount . ' Entries', $clr[4]);

imagerectangle($img, 9, 30, $width-9, $height-16, $clr[1]);

$steps = 10;
$top = 7;

for($q=$steps;$q>0;$q--)
{
  $p = number_format((100 / $steps) * $q, 0, '.', '\'') . '%';
  
  imageString($img, 2, $width, $height - ($top + (19 * $q)), $p, $clr[0]);
}

$ranges = array();

for($i=0;$i<=$diffs;$i++)
{
  $val = 0 + ($step * $i);
  $valRange = 0 + ($step * ($i + 1));
  
  if($i == 0) $val = $data->lowest;
  if($i == $diffs) $val = $data->highest;
  
  $val2 = $valModifiers['default']($val);
  $val3 = $valModifiers['default']($valRange);
  
  if(array_key_exists($key, $valModifiers))
  {
    $val2 = $valModifiers[$key]($val);
    $val3 = $valModifiers[$key]($valRange);
  }
  
  $y = $height - 12;
  $x = 20 + ($i * (($width-20) / ($diffs+1)));
  $sx = 10 + ($i * (($width-20) / ($diffs+1)));
  $sx2 = 10 + (($i + 0.5) * (($width - 20) / ($diffs+1)));
  $sx3 = 10 + (($i + 1) * (($width - 20) / ($diffs+1)));
  imageString($img, 1, $x, $y, $val2, $clr[0]);
  
  imageString($img, 1, $x, $y+10, $val3, $clr[0]);
  
  $sid_invalid = array();
  $sid_valid = array();
  $sid_total = array();
  $sid_invalid2 = array();
  $sid_valid2 = array();
  $sid_total2 = array();
  
  $sid_invalid_q = alxDatabaseManager::query
  ("
    SELECT soldierId 
    FROM profile_weaponStats 
    WHERE {$key} >= {$val} AND {$key} < {$valRange} 
    AND weaponId = '{$weaponId}' AND " . $getKeyWheres(false) . " AND kills > '{$kills}' 
    AND soldierId IN (SELECT p.soldierId FROM profiles AS p, bans AS b WHERE b.nucleusId = p.nucleusId AND b.blacklistId = '1')
    GROUP BY soldierId 
    ORDER BY date DESC 
    LIMIT {$limit}
  ");
  
  while($sid = $sid_invalid_q->fetch())
  {
    if(in_array($sid->soldierId, $sid_invalid2)) continue;
    
    $sid_invalid[] = "soldierId != '{$sid->soldierId}'";
  }
  
  $sid_valid_q = alxDatabaseManager::query
  ("
    SELECT soldierId 
    FROM profile_weaponStats 
    WHERE {$key} >= {$val} AND {$key} < {$valRange} 
    AND weaponId = '{$weaponId}' AND " . $getKeyWheres(false) . " AND kills > '{$kills}' 
    AND soldierId NOT IN (SELECT p.soldierId FROM profiles AS p, bans AS b WHERE b.nucleusId = p.nucleusId AND b.blacklistId = '1')
    GROUP BY soldierId 
    ORDER BY date DESC 
    LIMIT {$limit}
  ");
  
  while($sid = $sid_valid_q->fetch())
  {
    if(in_array($sid->soldierId, $sid_valid2)) continue;
    
    $sid_valid[] = "soldierId = '{$sid->soldierId}'";
  }
  
  $sid_total_q = alxDatabaseManager::query
  ("
    SELECT soldierId 
    FROM profile_weaponStats 
    WHERE {$key} >= {$val} AND {$key} < {$valRange} AND kills > '{$kills}' 
    AND weaponId = '{$weaponId}' AND " . $getKeyWheres(false) . "
    GROUP BY soldierId 
    ORDER BY date DESC 
    LIMIT {$limit2}
  ");
  
  while($sid = $sid_total_q->fetch())
  {
    if(in_array($sid->soldierId, $sid_total2)) continue;
    
    $sid_total[] = "soldierId = '{$sid->soldierId}'";
  }

  $subData = alxDatabaseManager::query
  ("
    SELECT soldierId, date, COUNT(weaponStatId) AS c 
    FROM profile_weaponStats 
    WHERE {$key} >= {$val} AND {$key} < {$valRange} 
    AND weaponId = '{$weaponId}' AND " . $getKeyWheres(false) . " AND kills > '{$kills}' 
    AND soldierId IN (SELECT p.soldierId FROM profiles AS p, bans AS b WHERE b.nucleusId = p.nucleusId AND b.blacklistId = '1')
    AND (" . implode(' OR ', $sid_invalid) . ") 
    ORDER BY date DESC 
    LIMIT {$limit}
  ")->fetch();
  
  $subDataValid = alxDatabaseManager::query
  ("
    SELECT soldierId, date, COUNT(weaponStatId) AS c 
    FROM profile_weaponStats 
    WHERE {$key} >= {$val} AND {$key} < {$valRange} 
    AND weaponId = '{$weaponId}' AND " . $getKeyWheres(false) . "  AND kills > '{$kills}' 
    AND soldierId NOT IN (SELECT p.soldierId FROM profiles AS p, bans AS b WHERE b.nucleusId = p.nucleusId AND b.blacklistId = '1')  
    AND (" . implode(' OR ', $sid_valid) . ") 
    ORDER BY date DESC 
    LIMIT {$limit}
  ")->fetch();

  $subDataAll = alxDatabaseManager::query
  ("
    SELECT soldierId, COUNT(weaponStatId) AS c 
    FROM profile_weaponStats 
    WHERE {$key} >= {$val} AND {$key} < {$valRange} 
    AND weaponId = '{$weaponId}' AND " . $getKeyWheres(false) . " AND kills > '{$kills}' 
    AND (" . implode(' OR ', $sid_total) . ") 
    ORDER BY date DESC 
    LIMIT {$limit2}
  ")->fetch();

  $ranges[$i] = array
  (
    @$subData->c ?: 0,
    @$subDataValid->c ?: 0,
    @$subDataAll->c ?: 1
  );

  //imageStringUp($img, 1, $sx + 5, $height-30, $subData->c / $data->c, $clr[1]);
  /*
  imageFilledRectangle($img, $sx, $height-16, $sx2, $y2, $clr[3]);
  imageFilledRectangle($img, $sx2+1, $height-16, $sx3-1, $y3, $clr[4]); 
  */
}

if(@$_GET['debug']) var_dump($ranges);

foreach($ranges as $i => $range)
{
  $y1 = ($height-16) - (($range[0] / $invalidCount) * ($contentHeight));
  $y2 = ($height-16) - (($range[1] / $validCount) * ($contentHeight));
  
  $y1 = floor($y1);
  $y2 = floor($y2);
  
  $p1 = @$_GET['abs'] ? $range[0] : number_format(($range[0] / $invalidCount) * 100, 1, '.', '\'') . '%';
  $p2 = @$_GET['abs'] ? $range[1] : number_format(($range[1] / $validCount) * 100, 1, '.', '\'') . '%';
  
  $sx = 10 + ($i * (($width-20) / ($diffs+1)));
  $sx2 = 10 + (($i + 0.5) * (($width - 20) / ($diffs+1)));
  $sx3 = 10 + (($i + 1) * (($width - 20) / ($diffs+1)));
  
  if($i > 0) imageLine($img, $sx, $height-16, $sx, 30, $clr[1]);
  
  imageFilledRectangle($img, $sx, $height-16, $sx2, $y1, $clr[3]);
  imageFilledRectangle($img, $sx2+1, $height-16, $sx3-1, $y2, $clr[4]); 
  
  imageStringUp($img, 2, $sx+3, 70, $p1, $clr[0]);
  imageStringUp($img, 2, $sx2+3, 70, $p2, $clr[0]);
}

if(!@$_GET['debug'])
{
  $date = @$_GET['date'] ?: time();
  
  imagePNG($img, "../imgCache/{$weaponId}_{$key}_{$date}.png");
  imagePNG($img);
  imageDestroy($img);
}
