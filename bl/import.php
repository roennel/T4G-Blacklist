<?php

$sql1 = mysql_connect('localhost', 'bft', 'QGy5FfabVeuGrjR7');
mysql_select_db('bft', $sql1);

$sql2 = mysql_connect('localhost', 't4g_blacklist', '3fEJqMUtyXdVWzFJ');
mysql_select_db('t4g_blacklist', $sql2);


$ex = array();

$bans = mysql_query("SELECT * FROM submissions ORDER BY targetNucleusId ASC", $sql2);

while($item = mysql_fetch_object($bans))
{
  $c = $item->targetNucleusId . '_' . $item->type;
  
  if(in_array($c, $ex))
  {
    mysql_query("DELETE FROM submissions WHERE submissionId = '{$item->submissionId}' LIMIT 1", $sql2);
  }
  
  $ex[] = $c;
}

/*
$profiles = mysql_query("SELECT * FROM stats_soldiers", $sql1);

while($item = mysql_fetch_object($profiles))
{
  $nucleusId = $item->nucleusId;
  $soldierId = $item->profileId;
  $level = 0;
  $name = $item->name;
  
  switch(true)
  {
    
    case stripos($item->class, 'Recon') !== false:
      $kit = '0';
    break;
    
    case stripos($item->class, 'Medic') !== false:
      $kit = '2';
    break;
    
    case stripos($item->class, 'Engineer') !== false:
      $kit = '3';
    break;
    
    case stripos($item->class, 'Assault') !== false:
      $kit = '1';
    break;
  }
  
  mysql_query("INSERT INTO profiles SET nucleusId = '{$nucleusId}', soldierId = '{$soldierId}', level = '{$level}', name = '{$name}', kit = '{$kit}'", $sql2);
}
*/
/*
$gbans = mysql_query("SELECT * FROM stats_module_ban WHERE global = '1' AND reason LIKE '%glitch%' ORDER BY statModuleBanId ASC", $sql1);

while($item = mysql_fetch_object($gbans))
{
  // Create Submission
  $created = time();
  $sourceNucleusId = 0;
  $sourceMail = '';
  $targetNucleusId = $item->nucleusId;
  $targetName = '';
  $type = 'gl';
  $msg = $item->reason;
  $modValidation1 = 99999;
  $modValidation2 = 99999;
  $adminValidation = 99999;
  $done = 1;
  
  mysql_query("INSERT INTO submissions SET modValidation1 = '$modValidation1', modValidation2 = '$modValidation2', adminValidation = '$adminValidation', done = '$done', created = '$created', sourceNucleusId = '$sourceNucleusId', sourceMail = '$sourceMail', targetNucleusId = '$targetNucleusId', targetName = '', type = '$type', msg = '$msg'", $sql2);

  $id = mysql_insert_id();
  $created = time();
  $nucleusId = $item->nucleusId;
  $blacklistId = 2;
  
  mysql_query("INSERT INTO bans SET nucleusId = '$nucleusId', blacklistId = '$blacklistId', submissionId = '$id', created = '$created'", $sql2);

  echo "Moved Ban{$item->statModuleBanId}\n<br />";
}
*/