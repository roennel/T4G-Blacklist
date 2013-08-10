<?php $noImage = true; require 'img_init.php';

error_reporting(E_ALL);
header('content-type: text/plain');

$banIds = array(94);

$fields = array
(
  'timeUsed',
  'timesUsed',
  'kills',
  'kpm',
  'deaths',
  'deathsBy',
  'hits',
  'shots',
  'misses',
  'accuracy',
  'headshots',
  'headshotratio',
  'bestRangedKill',
  'damageDealt',
  'damageTaken',
  'dpb'
);

$op = array
(
  function($a, $b)
  {
    return $a + $b;
  },
  function($a, $b)
  {
    return $a - $b;
  },
  function($a, $b)
  {
    return $a / $b;
  },
  function($a, $b)
  {
    return $a * $b;
  }
);

$opl = array
(
  '+', '-', '/', '*'
);

$ops = array();

$opc = 0;

for($i=0,$c=count($fields);$i<$c;$i++)
{
  $ops[$i] = 0;
}

$c = count($fields);
$it = count($fields) * count($fields) * count($op);     
$it = 128;
foreach($banIds as $banId)
{
  $ban = alxDatabaseManager::query("SELECT * FROM bans WHERE banId = '{$banId}' LIMIT 1")->fetch();
  
  $profiles = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$ban->nucleusId}'");
  
  while($profile = $profiles->fetch())
  {
    $sid = $profile->soldierId;
    
    $lastDate = alxDatabaseManager::query("SELECT date FROM profile_weaponStats WHERE soldierId = '{$sid}' ORDER BY date DESC LIMIT 1")->fetch();
    
    $stats = alxDatabaseManager::query("SELECT * FROM profile_weaponStats WHERE soldierId = '{$sid}' AND date = '{$lastDate->date}' ORDER BY weaponStatId DESC");
    
    while($stat = $stats->fetch())
    {
      for($i = 0;$i<$it;$i++)
      {
        $result = 0;
        $opx = 0;
      
        $e = '#' . $i . ' -> ';

        for($x=0;$x<$c-1;$x++)
        {
          $opq = $opl[$ops[$x]];
          
          $e.= $stat->{$fields[$x]} . ' ' . $opq . ' ' . $stat->{$fields[$x+1]};
          
          //echo $stat->weaponId . ' - ' . $stat->{$fields[$x]} . ' - ' . $stat->{$fields[$x+1]} . "\n";
          $result+= $op[$opx]($stat->{$fields[$x]}, $stat->{$fields[$x+1]});  
        }
        
        if($i % $c != 0)
        {
          if($opc+1 >= $c)
          {
            $opc = 0;
          }
          else
          {
            $opc++;
          }
        }
        
        $ops[$opc]++;
        
        echo $e . "\n";
      
        echo $result . "\n";
      }
    }
  }
}
