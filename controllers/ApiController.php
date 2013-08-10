<?php
header('content-type: text/plain');
require 'lib/awards.php';
class ApiController extends alxController
{
  function getAwardsForSoldierId($get)
  {
    if(!@$get->soldierId) return;
    
    if(!@$get->language)
    {
      $lang = 'en';
    }
    else
    {
      $lang = mysql_real_escape_string($get->language);
    }
    
    $lang = strToLower($lang);
    
    $result = [];
    
    $nucleusId = mysql_real_escape_string($get->nucleusId);
    $soldierId = mysql_real_escape_string($get->soldierId);
    
    $fetch = false;
    $updated = alxDatabaseManager::query
    ("SELECT date FROM profile_stats WHERE soldierId = '{$soldierId}' ORDER BY date DESC LIMIT 1")->fetch();

    if(!@$updated->date or $updated->date < (time()-86400)){
      // @file_get_contents('http://blacklist.tools4games.com/bl/fetchStatsDo.php?n=' . $nucleusId);
      
      // update for future requests
      $fetch = true;
      $cmd = "nice php /var/www/t4g_blacklist/bl/fetchStatsDo.php {$nucleusId} 2>&1 & echo $!";
      pclose(popen($cmd, 'r'));
    }

    $result['soldierId'] = $soldierId;
    $result['updated'] = @date('d.m.Y H:i', $updated->date);
    $result['fetch']  = $fetch;
    $result['awards'] = Awards::getForSoldierId($lang, $nucleusId, $soldierId);
    
    $this->respondString(json_encode($result));
  }
  
  function getServerList($get)
  {
    $servers = array();
    
    $_servers = alxDatabaseManager::query("SELECT serverId, bookmarkLink, online FROM servers ORDER BY name ASC");

    $kicks = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM kickLog WHERE type = '0'")->fetch();
    
    while($server = $_servers->fetch())
    {
      $hacktivity = 0;
      
      $sKicks = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM kickLog WHERE serverId = '{$server->serverId}' AND type = '0'")->fetch();
      
      $hacktivity = number_format(($sKicks->c / $kicks->c) * 100, 1, '.', '\'');
      
      $blacklists = array();
      $_blacklists = alxDatabaseManager::query("SELECT b.label AS label, sb.kicks AS kicks FROM blacklists AS b, server_blacklists AS sb WHERE sb.serverId = '{$server->serverId}' AND b.blacklistId = sb.blacklistId");
      
      while($bl = $_blacklists->fetch())
      {
        $blacklists[] = $bl->label;
      }
      $s = new stdClass;
      $s->blacklists = $blacklists;
      $s->hacktivity = $hacktivity;
      $s->online = $server->online == '1' ? true : false;
      
      $servers[$server->bookmarkLink] = $s;
    }
    
    
    
    $this->respondString(json_encode($servers));
  }
}
