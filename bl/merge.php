<?php

$sql1 = mysql_connect('localhost', 'bft', 'QGy5FfabVeuGrjR7');
mysql_select_db('bft', $sql1);

$sql2 = mysql_connect('localhost', 't4g_blacklist', '3fEJqMUtyXdVWzFJ');
mysql_select_db('t4g_blacklist', $sql2);


$bans = mysql_query("SELECT * FROM bans", $sql2);

while($ban = mysql_fetch_object($bans))
{
  mysql_query("DELETE FROM stats_module_ban WHERE global = '1' AND nucleusId = '{$ban->nucleusId}'", $sql1);
  echo "DELETED {$ban->nucleusId}";
}
