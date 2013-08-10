<?php


function checkPBLog($serverId)
{
  echo "\nSERVER #{$serverId} - ";
  
  $path = "/home/roennel/pblogs/blacklist_{$serverId}.log";
  
  if(!file_exists($path)) 
  {
    echo 'ERROR';
    return;
  }
  
  $f = file_get_contents($path);

  $spl = explode("\n", $f);

  foreach($spl as $item)
  {
  if(strpos($item, '->') === false) continue;
  
  $sub = explode('->', $item);
  
  $date = $sub[0];
  $msg = $sub[1];
  
  if(trim(str_replace(' ', '', $msg)) == '') continue;
  
  $date = str_replace('[', '', $date);
  $date = str_replace(']', '', $date);
  
  $m = substr($date, 0, 2);
  $d = substr($date, 3, 2);
  $y = substr($date, 6, 4);
  
  $h = substr($date, 11, 2);
  $i = substr($date, 14, 2);
  $s = substr($date, 17, 2);
  
  $time = mktime($h, $i, $s, $m, $d, $y);

  $c = alxDatabaseManager::query
  ("
    SELECT 
      pbLogId 
    FROM 
      pb_log  
    WHERE 
      serverId = '{$serverId}' 
    AND 
      date = '{$time}' 
    AND 
      msg = '{$msg}'
  ")->fetch();
  
  if(@$c->pbLogId <= 0)
  {
    alxDatabaseManager::query
    ("
      INSERT INTO 
        pb_log 
      SET 
        serverId = '{$serverId}',
        date = '{$time}',
        msg = '{$msg}'
    ");
  }
  }
  
  echo 'OK [' . (count($spl)) . ']';
}

