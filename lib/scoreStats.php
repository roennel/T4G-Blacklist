<?php

function getFreshStats($n, $p)
{
  $data = new stdClass;

  $url = (object) array
  (
    'soldiers' => array("http://battlefield.play4free.com/en/profile/soldiers/{$n}", null),
    'weapons' => array("http://battlefield.play4free.com/en/profile/stats/{$n}/{$p}?g=[%22WeaponStats%22]&_=1356810989717", 'WeaponStats'),
    'core' => array("http://battlefield.play4free.com/en/profile/stats/{$n}/{$p}?g=[%22CoreStats%22]&_=1357251943043", 'CoreStats'),
    'maps' => array("http://battlefield.play4free.com/en/profile/stats/{$n}/{$p}?g=[%22GameModeMapStats%22]&_=1357254663263", 'GameModeMapStats')
  );

  foreach($url as $label => $item)
  {
    $d = json_decode(file_get_contents($item[0]))->data;
    
    $data->{$label} = $item[1] ? @$d->{$item[1]} : $d;
  }
  
  return $data;
}

function getStatsFromSDB($n, $p, $date)
{
  $data = new stdClass;
  
  $q = (object) array
  (
    // 'soldiers' => "SELECT *, soldierId AS id FROM profiles WHERE nucleusId = '{$n}' AND soldierId = '{$p}' AND level > '0' LIMIT 1",
    'soldiers' => "SELECT IFNULL(ps.name, p.name) AS name, p.kit, ps.level AS level, p.soldierId AS id FROM profiles AS p LEFT JOIN profile_soldiers AS ps ON p.soldierId = ps.soldierId AND date = '{$date}' WHERE p.soldierId = '{$p}' LIMIT 1",
    'weapons' => "SELECT *, weaponId AS id FROM profile_weaponStats WHERE soldierId = '{$p}' AND date = '{$date}'",
    'core' => "SELECT * FROM profile_stats WHERE soldierId = '{$p}' AND date = '{$date}'",
    'maps' => "SELECT *, mapId AS id FROM profile_mapStats WHERE soldierId = '{$p}' AND date = '{$date}'",
  );
  
  foreach($q as $label => $item)
  {
    $d = alxDatabaseManager::query($item);
    while($si = $d->fetch())
    {
      $data->{$label}[] = $si;
    }
  }
  $data->core = @$data->core[0];
  
  /*
  if(!@$data->soldiers)
  {
    $data->soldiers = json_decode(file_get_contents("http://battlefield.play4free.com/en/profile/soldiers/{$n}"))->data;
  }
  */
  
  return $data;
}

function scoreStats($n, $p, $all, $date = null)
{
$data = $date ? getStatsFromSDB($n, $p, $date) : getFreshStats($n, $p);

$allKitCategories = array
(
  'pistol',
  'shotgun'
);

$kits = array
(
  0 => 'sniper_rifle',
  1 => 'assault_rifle',
  2 => 'lmg',
  3 => 'smg',
);

$kitNames = array
(
  0 => 'recon',
  1 => 'assault',
  2 => 'medic',
  3 => 'engineer'
);

$wepTypes = array
(
  'ar' => 'assault_rifle',
  'srifle' => 'sniper_rifle',
  'lmg' => 'lmg',
  'smg' => 'smg',
  'pistol' => 'pistol',
  'sg' => 'shotgun'
);

$activeSoldier = '';
$activeCategory = '';

foreach($data->soldiers as $soldier)
{
  $soldier->kitName = $kitNames[$soldier->kit];
  
  if($soldier->id == $p)
  {
    $activeSoldier = $soldier;
    $activeCategory = $kits[$soldier->kit];
  }
}

$killsNeeded = 100;

$hsRatioDefaultsByType = array
(
  '_' => array
  (
    30,
    35,
    40,
    100
  ),
  'pistol' => array
  (
    35,
    45,
    55,
    100
  ),
  // For bolt actions mainly. Limits for Semi-Autos and Full-Auto SRs get specified separately below
  'sniper_rifle' => array
  (
    80,
    85,
    95,
    100
  )
);

$hsRatioDefaultsById = array
(
  // 'MP-412 Rex'
  '3020' => array
  (
    45, 65, 75, 100
  ),
  // 'Deagle 50'
  '3115' => array
  (
    45, 65, 75, 100
  ),
  // 'Steel Deagle 50'
  '3118' => array
  (
    45, 65, 75, 100
  ),
  // 'SKS'
  '3119' => array
  (
    30, 40, 55, 100
  ),
  // M14 EBR
  '3065' => array
  (
    30, 35, 40, 100
  ),
  // M110
  '3066' => array
  (
    30, 40, 55, 100
  ),
  // SVD
  '3023' => array
  (
    30, 40, 50, 100
  ),
  // 'VSS'
  '3070' => array
  (
    30, 40, 50, 100
  ),
  // 'SVU-A'
  '3045' => array
  (
    30, 40, 45, 100
  ),
  // 'SV-98'
  '3004' => array
  (
    70, 75, 80, 100
  ),
  // 'L85A2'
  '3127' => array
  (
    35, 45, 55, 100
  ),
  // 'MP5'
  '3047' => array
  (
    35, 40, 45, 100
  ),
  // 'G53'
  '3129' => array
  (
    35, 40, 45, 100
  ),
  // 'AS-VAL'
  '3122' => array
  (
    35, 40, 45, 100
  ),
  // 'M249 SAW'
  '3003' => array
  (
    35, 40, 45, 100
  ),
  // 'QBB-95'
  '3121' => array
  (
    35, 40, 45, 100
  )
);

$accuracyDefaultsByType = array
(
  '_' => array
  (
    35, 
    45,
    50,
    100
  ),
  // For bolt actions mainly. Limits for Semi-Autos and Full-Auto SRs get specified separately below
  'sniper_rifle' => array
  (
    65,
    70,
    80,
    100
  )
);

$accuracyDefaultsById = array
(
  // 'MP-412 Rex'
  '3020' => array
  (
    40, 65, 75, 100
  ),
  // 'Deagle 50'
  '3115' => array
  (
    40, 65, 75, 100
  ),
  // 'Steel Deagle 50'
  '3118' => array
  (
    40, 65, 75, 100
  ),
  // 'Scattergun'
  '3123' => array
  (
    70, 85, 100, 200
  ),
  // 'Nosferatu'
  '3124' => array
  (
    70, 85, 100, 200
  ),
  // 'SKS'
  '3119' => array
  (
    30, 40, 60, 100
  ),
  // M14 EBR
  '3065' => array
  (
    30, 40, 60, 100
  ),
  // M110
  '3066' => array
  (
    30, 40, 60, 100
  ),
  // SVD
  '3023' => array
  (
    30, 40, 60, 100
  ),
  // 'VSS'
  '3070' => array
  (
    35, 40, 45, 100
  ),
  // 'SVU-A'
  '3045' => array
  (
    35, 40, 45, 100
  ),
  // 'SV-98'
  '3004' => array
  (
    60, 70, 75, 100
  ),
  // 'L85A2'
  '3127' => array
  (
    35, 45, 50, 100
  ),
  // 'MP5'
  '3047' => array
  (
    30, 35, 40, 100
  ),
  // 'G53'
  '3129' => array
  (
    30, 35, 40, 100
  )
);

$typeScores = array
(
  0 => -1,
  1 => 2,
  2 => 4,
  3 => 6
);

/*
 * "Karkand":"1","Oman":"2","Sharqi":"3","Basra":"4","Dragon Valley":"5","Dalian":"6","Mashtuur":"7","Myanmar":"8"}
 * */
 

$maxScores = array
(
  'global' => array
  (
    30000,40000,50000,99999999
  ),

  4 => array
  (
    1 => array(20000,25000,30000,9999999), // Karkand
    2 => array(25000,30000,40000,9999999), // Oman
    3 => array(15000,17500,20000,9999999), // Sharqi
    4 => array(20000,25000,30000,9999999), // Basra
    5 => array(25000,30000,50000,9999999), // Dragon Valley
    6 => array(25000,30000,40000,9999999), // Dalian
    7 => array(20000,25000,30000,9999999), // Mashtuur
    8 => array(20000,25000,30000,9999999)  // Myanmar
  ),
  5 => array
  (
    1 => array(20000,30000,40000,9999999), // Karkand
    2 => array(20000),
    3 => array(20000,30000,35000,9999999), // Sharqi
    4 => array(20000),
    5 => array(20000),
    6 => array(25000,35000,50000,9999999), // Dalian
    7 => array(20000),
    8 => array(20000)
  )
);

$killStreaks = array
(
  'global' => array
  (
    75, 150, 200, 99999
  )
);

$killStreak = null;

$ks = @$data->core->killStreak;

$k = 1;

foreach($killStreaks['global'] as $s)
{
  if($ks >= $s)
  { 
    $killStreak = array($k, $ks);
  }
  
  $k++;
}

$maxScore = array();

$msg = @$data->core->bestScore;

$k = 1;

foreach($maxScores['global'] as $s)
{
  if($msg >= $s)
  { 
    $maxScore['global'] = array($k, $msg);
  }
  
  $k++;
}

$mgs = (array) @$data->maps;

foreach($mgs as $item)
{
  $k = 1;
  
  $mx = (array) @$maxScores[$item->gameMode][$item->id];
  
  foreach($mx as $s)
  {
    if($item->bestScore >= $s)
    {
      $maxScore[$item->gameMode . '_' . $item->id] = array($k, $item->bestScore);
    }
  
    $k++;
  }
}

$prob = array();
$occur = array();
$consistency = array();

$ws = (array) @$data->weapons;

foreach($ws as $weapon)
{
  $wName = @$GLOBALS['weaponIds'][$weapon->id];
  
  $category = @$wepTypes[strtolower(preg_replace('/^(?:WEAPON_)?([^_]+)_.*$/', '$1', $wName))];
  
  if($weapon->kills == 0 or ($category != $activeCategory && !in_array($category, $allKitCategories))) continue;
  
  $name = getNiceWeaponName($wName);
  $hsRatio = $weapon->headshots / ($weapon->kills == 0 ? 1 : $weapon->kills) * 100;
  $hsRatioF = number_format($hsRatio, 0);
  
  $accuracy = $weapon->accuracy * 100;
  $accuracyF = number_format($accuracy, 0);
  
  $kdRatio = $weapon->deaths > 0 ? $weapon->kills / $weapon->deaths : $weapon->kills;
  $kdRatioF = number_format($kdRatio, 2);
  
  // $category = $weapon->description->category;
  
  $state = new stdClass;
  $state->hsRatio = 0;
  $state->accuracy = 0;
  
  $abs = new stdClass;
  $abs->hsRatio = $hsRatio;
  $abs->accuracy = $accuracy;
  
  $total = new stdClass;
  $total->hsRatio = 0;
  $total->accuracy = 0;
  
  $comb = new stdClass;
  $comb->hsRatio = "{$weapon->kills} Kills\nKpM: {$weapon->kpm}\nAcc: {$accuracyF}%\nRK: {$weapon->bestRangedKill}\nKD: {$kdRatioF}";
  //$comb->hsRatio = "{$weapon->kills} Kills\nKpM: {$weapon->kpm}";
  //$comb->accuracy = "ACCURACY";
  $comb->accuracy = "{$weapon->kills} Kills\nKpM: {$weapon->kpm}\nHSR: {$hsRatioF}%\nRK: {$weapon->bestRangedKill}\nKD: {$kdRatioF}";
  

  
  // HS Ratio
  $s = 0;
  $i = 0;
  
  if(!array_key_exists($category, $hsRatioDefaultsByType))
  {
    $category = '_';  
  }
  
  $t = $hsRatioDefaultsByType[$category];
  
  if(array_key_exists($weapon->id, $hsRatioDefaultsById))
  {
    $t = $hsRatioDefaultsById[$weapon->id];
  }
  
  foreach($t as $catch)
  {
    if($hsRatio > $s && $hsRatio <= $catch)
    {
      if($weapon->kills >= $killsNeeded)
      {
        $state->hsRatio = $i;
      }
    }
    
    $s = $catch;
    $i++;
  }
  
  // Accuracy
  $s = 0;
  $i = 0;
  
  if(!array_key_exists($category, $accuracyDefaultsByType))
  {
    $category = '_';  
  }
  
  $t = $accuracyDefaultsByType[$category];
  
  if(array_key_exists($weapon->id, $accuracyDefaultsById))
  {
    $t = $accuracyDefaultsById[$weapon->id];
  }
  
  foreach($t as $catch)
  {
    if($accuracy > $s && $accuracy <= $catch)
    {
      if($weapon->kills >= $killsNeeded)
      {
        $state->accuracy = $i;
      }
    }
    
    $s = $catch;
    $i++;
  }
  
  $stateArray = (array) $state;
  
  foreach($stateArray as $k => $s)
  {
      $prob[$k][] = array($name, $s, $abs->{$k}, $comb->{$k});
      
      if(!array_key_exists($k, $occur))
      {
        $occur[$k] = array();
      }
      
      if(!array_key_exists($s, $occur[$k]))
      {
        $occur[$k][$s] = 0;  
      }
      
      $occur[$k][$s]++;
  } 
}

$scores = array();

foreach($occur as $type => $data)
{
  $scores[$type] = 0;
  
  foreach($data as $typeId => $count)
  {
    $scores[$type]+= ($typeScores[$typeId] * $count);
  }
}

$return = new stdClass;
$prob2 = array();

$avg = new stdClass;
$avg->hsRatio = array();
$avg->accuracy = array();

$avg2 = new stdClass;
$avg2->hsRatio = array();
$avg2->accuracy = array();

$diff = new stdClass;
$diff->hsRatio = 0;
$diff->accuracy = 0;

foreach($prob as $item => $data)
{
  $prob2[$item] = $data;
  
  uasort($prob2[$item], function($a, $b)
  {
    if($a[1] == $b[1]) return 0;
  
    return ($a[1] < $b[1]) ? 1 : -1;
  });

  foreach($data as $sub)
  {
    $avg->{$item}[] = $sub[2];
    
    if($sub[1] > 0)
    {
      $avg2->{$item}[] = $sub[2];
    }
  }
}

foreach($avg as $avgKey => $avgValue)
{
  $c = count($avgValue);
  $avg->{$avgKey} = round(array_sum($avgValue) / ($c == 0 ? 1 : $c));
}

foreach($avg2 as $avgKey => $avgValue)
{
  $c = count($avgValue);
  $avg2->{$avgKey} = round(array_sum($avgValue) / ($c == 0 ? 1 : $c));
}

foreach($prob as $item => $data)
{
  foreach($data as $sub)
  {
    $diff->{$item}+= $avg->{$item} - $sub[2];
  }
}

$return->avg = $avg;
$return->avg2 = $avg2;
$return->diff = $diff;
$return->scores = $scores;
$return->occur = $occur;
$return->prob = $prob2;
$return->soldier = $activeSoldier;
$return->maxScore = $maxScore;
$return->killStreak = $killStreak;
return $return;
}



/**
object(stdClass)#41 (18) {
  ["timeUsed"]=>
  int(0)
  ["timesUsed"]=>
  int(0)
  ["kills"]=>
  int(0)
  ["kpm"]=>
  int(0)
  ["deaths"]=>
  int(0)
  ["deathsBy"]=>
  int(32)
  ["hits"]=>
  int(0)
  ["shots"]=>
  int(0)
  ["misses"]=>
  int(0)
  ["accuracy"]=>
  int(0)
  ["headshots"]=>
  int(0)
  ["headshotratio"]=>
  int(0)
  ["bestRangedKill"]=>
  int(0)
  ["damageDealt"]=>
  int(0)
  ["damageTaken"]=>
  int(5527)
  ["dpb"]=>
  int(0)
  ["id"]=>
  int(3001)
  ["description"]=>
  object(stdClass)#42 (23) {
    ["id"]=>
    string(4) "3001"
    ["type"]=>
    string(7) "weapons"
    ["name"]=>
    string(5) "M16A2"
    ["category"]=>
    string(13) "assault_rifle"
    ["categoryname"]=>
    string(13) "Assault Rifle"
    ["usecount"]=>
    NULL
    ["isnew"]=>
    bool(false)
    ["expiredate"]=>
    string(0) ""
    ["expireTS"]=>
    bool(false)
    ["expired"]=>
    bool(false)
    ["description"]=>
    string(122) "This version of the classic assault rifle with has 3-round burst and a large magazine providing with great fire endurance."
    ["owned"]=>
    bool(false)
    ["ownedPermanent"]=>
    NULL
    ["buyable"]=>
    bool(false)
    ["equippedSlot"]=>
    bool(false)
    ["validationGroup"]=>
    string(7) "primary"
    ["prices"]=>
    array(0) {
    }
    ["promotionType"]=>
    NULL
    ["isLocked"]=>
    bool(false)
    ["lockType"]=>
    string(5) "level"
    ["lockCriteria"]=>
    string(2) "12"
    ["attachments"]=>
    object(stdClass)#51 (5) {
      ["2"]=>
      NULL
      ["3"]=>
      NULL
      ["4"]=>
      NULL
      ["5"]=>
      NULL
      ["6"]=>
      NULL
    }
  }
}
 */
