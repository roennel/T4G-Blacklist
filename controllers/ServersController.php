<?php

class ServersController extends alxController
{
  function index()
  {
    $servers = array();
    $servers2[] = array();
    
    $_servers = alxDatabaseManager::query("SELECT * FROM servers WHERE disabled = '0' ORDER BY online DESC, noLogin DESC, name ASC");
    $_servers2 = alxDatabaseManager::query("SELECT * FROM servers WHERE disabled = '1' ORDER BY online DESC, noLogin DESC, name ASC");
    
    $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM servers WHERE disabled = '0'")->fetch();
    
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
      
      $server->blacklists = $blacklists;
      $server->hacktivity = $hacktivity;
      $servers[] = $server;
    }
    
    while($server = $_servers2->fetch())
    {
      $blacklists = array();
      $_blacklists = alxDatabaseManager::query("SELECT b.label AS label, sb.kicks AS kicks FROM blacklists AS b, server_blacklists AS sb WHERE sb.serverId = '{$server->serverId}' AND b.blacklistId = sb.blacklistId");
      
      while($bl = $_blacklists->fetch())
      {
        $blacklists[] = $bl->label;
      }
      
      $server->blacklists = $blacklists;
      $servers2[] = $server;
    }
    
    $this->add('servers', $servers);
    $this->add('servers2', $servers2);
    $this->add('serverCount', $count->c);
    
    $this->render();
  }
  
  
  
}
