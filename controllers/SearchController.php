<?php

class SearchController extends alxController
{
  function index()
  {
    $this->render();
  }
  
  function result($get)
  {
    if(!@$get->name && !@$get->nucleusId) return;
    
    header('content-type: text/plain');
    
    $result = array
    (
      'success' => true,
      'data' => array()
    );
    
    if(@$get->name)
    {
      $s = mysql_real_escape_string($get->name);
    
      if(@$get->exact)
      {
        $e = "= '{$s}'";  
      }
      else
      {
        $e = "LIKE '%{$s}%'";  
      } 
       
      $search = alxDatabaseManager::query("SELECT b.submissionId, bl.label, b.blacklistId, b.created, p.name FROM bans AS b, profiles AS p, blacklists AS bl 
      WHERE p.name {$e} AND b.active = '1' AND bl.blacklistId = b.blacklistId AND b.nucleusId = p.nucleusId ORDER BY p.name ASC");
    
      $search2 = alxDatabaseManager::query("SELECT s.submissionId, s.created, p.name, s.type, s.targetNucleusId AS nucleusId FROM submissions AS s, profiles AS p 
      WHERE p.name {$e} AND s.targetNucleusId = p.nucleusId AND s.done = '0' AND s.postponed = '0' ORDER BY p.name ASC");
    }
    else 
    {
      $s = mysql_real_escape_string($get->nucleusId);
      
      $search = alxDatabaseManager::query("SELECT b.submissionId, bl.label, b.blacklistId, b.created, p.name FROM bans AS b, profiles AS p, blacklists AS bl 
      WHERE p.nucleusId = '{$s}' AND b.active = '1' AND bl.blacklistId = b.blacklistId AND b.nucleusId = p.nucleusId ORDER BY p.name ASC");
    
      $search2 = alxDatabaseManager::query("SELECT s.submissionId, s.created, p.name, s.type, s.targetNucleusId AS nucleusId FROM submissions AS s, profiles AS p 
      WHERE p.nucleusId = '{$s}' AND s.targetNucleusId = p.nucleusId AND s.done = '0' AND s.postponed = '0' ORDER BY p.name ASC");
    }
    
    $ex = array();
    
    while($item = $search->fetch())
    {
      if(in_array("{$item->name}_{$item->blacklistId}", $ex)) continue;
      
      $item->date = date('d.m.Y H:i', $item->created);
      
      $result['data'][] = $item;
    
      if(isMod() && isAdmin())
        $item->link = '/adminPanel/submissionDetail?submissionId=' . $item->submissionId;
      elseif(isMod())
        $item->link = '/modPanel/submissionDetail?submissionId=' . $item->submissionId;
      unset($item->submissionId);
      
      $ex[] = "{$item->name}_{$item->blacklistId}";
    }
    
    while($item = $search2->fetch())
    {
      $s = new stdClass;
      $s->created = $item->created;
      $s->date = date('d.m.Y H:i', $item->created);
      $s->label = $GLOBALS['types'][$item->type];
      
      $names = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$item->nucleusId}'");
      $_names = array();
      
      while($i = $names->fetch())
      {
        $_names[] = $i->name;  
      }
      
      $s->names = $_names;
      $s->name = $item->name;
      
      if(isMod() && isAdmin())
        $s->link = '/adminPanel/submissionDetail?submissionId=' . $item->submissionId;
      elseif(isMod())
        $s->link = '/modPanel/submissionDetail?submissionId=' . $item->submissionId;
      
      $result['submissions'][] = $s;
    }
    
    $r = json_encode($result);
    
    if(@$get->callback)
    {
      $r = $get->callback . "({$r});";
    }
    
    $this->respondString($r);
  }
  
  // accepts multiple names - separated by ";"
  function resultMultiple($get)
  {
    if(!@$get->names) return;
    
    header('content-type: text/plain');
    
    $result = array
    (
      'success' => true,
      'data' => array()
    );

    $spl = explode(';', @$get->names);
    
    foreach($spl as $name)
    {
      $name = mysql_real_escape_string($name);
      
      $search = alxDatabaseManager::query("SELECT bl.label, b.blacklistId, b.created, p.name FROM bans AS b, profiles AS p, blacklists AS bl 
      WHERE p.name = '{$name}' AND b.active = '1' AND bl.blacklistId = b.blacklistId AND b.nucleusId = p.nucleusId ORDER BY p.name ASC");
    
      $search2 = alxDatabaseManager::query("SELECT s.created, p.name, s.type, s.targetNucleusId AS nucleusId FROM submissions AS s, profiles AS p 
      WHERE p.name = '{$name}' AND s.targetNucleusId = p.nucleusId AND s.done = '0' ORDER BY p.name ASC");
      
      
      $ex = array();
      
      while($item = $search->fetch())
      {
        if(in_array("{$item->name}_{$item->blacklistId}", $ex)) continue;
        
        $item->date = date('d.m.Y H:i', $item->created);
        
        $result['data'][] = $item;
      
        $ex[] = "{$item->name}_{$item->blacklistId}";
      }
      
      while($item = $search2->fetch())
      {
        $s = new stdClass;
        $s->created = $item->created;
        $s->date = date('d.m.Y H:i', $item->created);
        $s->label = $GLOBALS['types'][$item->type];
        
        $names = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$item->nucleusId}'");
        $_names = array();
        
        while($i = $names->fetch())
        {
          $_names[] = $i->name;  
        }
        
        $s->names = $_names;
        $s->name = $item->name;
        
        $result['submissions'][] = $s;
      }
    }
    
    $r = json_encode($result);
    
    if(@$get->callback)
    {
      $r = $get->callback . "({$r});";
    }
    
    $this->respondString($r);
  }
  
}
