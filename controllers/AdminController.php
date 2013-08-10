<?php

require_once '/home/roennel/p4ftool/BFP4F_Rcon/Base.php';
require_once '/home/roennel/p4ftool/BFP4F_Rcon/Players.php';
require_once '/home/roennel/p4ftool/BFP4F_Rcon/Server.php';
require_once '/home/roennel/p4ftool/BFP4F_Rcon/Chat.php';

class AdminController extends alxController
{
  function index()
  {
    $this->redirect('admin', 'servers');
  }
  
  function plStats()
  {
    header('content-type: text/plain');
    
    $s = alxDatabaseManager::fetchMultiple
    ("
      SELECT * FROM plugin_itemLimiter_stats
    ");
    
    $f = function($v, $dec=2)
    {
      return number_format($v, $dec, '.', '\'');
    };
    
    $t = 0;
    
    echo "Servers: " . count($s) . "\n\n";
    
    foreach($s as $e)
    {
      $server = alxDatabaseManager::query
      ("SELECT name FROM servers WHERE serverId = '{$e->serverId}' LIMIT 1")->fetch();
      
      $diff = time() - $e->start;
      $d2 = $f($diff / 86400);
      
      $a = $e->value / $diff;
      $h = $a * 3600;
      $d = $h * 24;
      $w = $d * 7;
      $m = $w * 4;
      
      $t+= $a;
      
      $a = $f($a);
      $h = $f($h);
      $d = $f($d, 0);
      $w = $f($w, 0);
      $m = $f($m, 0);
      
      echo "\n{$server->name} [running {$d2} days]\n  > {$a} / second\n  > {$h} / hour\n  > {$d} / day\n  > {$w} / week\n  > {$m} / month\n";
    }
    
    $th = $t * 3600;
    $td = $th * 24;
    $tw = $td * 7;
    $tm = $tw * 4;
    
    $t = $f($t);
    $th = $f($th);
    $td = $f($td, 0);
    $tw = $f($tw, 0);
    $tm = $f($tm, 0);
    
    echo "\nTotal \n  > {$t} / second\n  > {$th} / hour\n  > {$td} / day\n  > {$tw} / week\n  > {$tm} / month";
    
    exit();
  }
  
  function plugins($get)
  {
    if(!isLogged() or !@$get->serverId) return;
    
    $serverId = (int) $get->serverId;
    
    $server = alxDatabaseManager::query
    ("
      SELECT * FROM servers WHERE serverId = '{$serverId}' LIMIT 1
    ")->fetch();
    
    if($server->userId != getUserId()) return;
    
    $this->add('serverId', $serverId);
    
    $items = alxDatabaseManager::fetchMultiple
    ("
      SELECT * FROM plugin_itemLimiter_items WHERE serverId = '{$serverId}'
    ");
    
    $this->add('items', $items);
    
    $itemLimiter = alxDatabaseManager::query
    ("
      SELECT * FROM plugins WHERE serverId = '{$serverId}' AND plugin = 'ITEM_LIMITER' LIMIT 1
    ")->fetch();
    
    $this->add('itemLimiter', $itemLimiter);
    
    $this->render();
  }
  
  function servers($get)
  {
    $serverModel = new ServerModel;
    
    $servers = $serverModel->getItems(array
    (
      'userId' => getUserId()
    ));
    
    $this->add('servers', $servers);
    
    $this->render();
  }
  
  function addServer($get)
  {
    if(!isLogged()) return;
    
    $banlists = alxDatabaseManager::query("SELECT * FROM blacklists ORDER BY label ASC");
    
    $this->add('banlists', $banlists);
    
    $this->render();
  }
  
  function editServer($get)
  {
    if(!@$get->serverId) return;
    
    $serverModel = new ServerModel;
    $serverModel->loadById($get->serverId);
    
    if($serverModel->userId != getUserId())
    {
      return;
    }
    
    $serverBanlists = array();
    
    $_banlists = alxDatabaseManager::query("SELECT blacklistId FROM server_blacklists WHERE serverId = '{$serverModel->serverId}'");
    
    while($item = $_banlists->fetch())
    {
      $serverBanlists[] = $item->blacklistId;
    }
    
    $this->add('server', $serverModel);
    $this->add('serverBanlists', $serverBanlists);
    
    $banlists = alxDatabaseManager::query("SELECT * FROM blacklists ORDER BY label ASC");
    
    $this->add('banlists', $banlists);
    
    $this->render('addServer');
  }
  
  function checkServerData($get)
  {  
    $result = array
    (
      'success' => true,
      'valid' => false
    );
    
    if(!@$get->country or !@$get->name or !@$get->ip or !@$get->port or !@$get->pwd or !@$get->blacklists)
    {
      $this->respondString(json_encode($result));
      return;
    }
    
    $name = str_replace('[___]', '#', str_replace('[__]', '+', trim($get->name)));
    $pwd = str_replace('[___]', '#', str_replace('[__]', '+', trim($get->pwd)));
    
    $rc = new BFP4F_Rcon\Base();
    $rc->ip   = $get->ip;
    $rc->port = $get->port;
    $rc->pwd  = $pwd;
    
    if($rc->init())
    {
      $serverModel = new ServerModel;
      
      if(@$get->serverId)
      {
        $serverModel->loadById($get->serverId);
      }
      
      $serverModel->name = mysql_real_escape_string($name);
      $serverModel->ip = trim($get->ip);
      $serverModel->port = trim($get->port);
      $serverModel->pwd = mysql_real_escape_string($pwd);
      $serverModel->bookmarkLink = trim($get->bookmarkLink);
      $serverModel->country = trim($get->country);
      $serverModel->userId = getUserId();
    
      $state = @$get->serverId ? $serverModel->update() : $serverModel->create();
      
      if($state)
      {
        $result['valid'] = true;
        
        alxDatabaseManager::query("DELETE FROM server_blacklists WHERE serverId = '{$serverModel->serverId}'");
        
        $blacklists = explode(';', $get->blacklists);
      
        foreach($blacklists as $blacklistId)
        {
          alxDatabaseManager::query("INSERT INTO server_blacklists SET serverId = '{$serverModel->serverId}', blacklistId = '{$blacklistId}'");
        }
      }
    }
    
    $this->respondString(json_encode($result));
  }
  
  function checkServerId($get)
  {
    if(!@$get->serverId) return;
    
    $result = array
    (
      'success' => true
    );
    
    $con = @file_get_contents('http://p4f.herokuapp.com/servers/api/serverInfo/' . $get->serverId);

    if($con)
    {
      $json = json_decode($con);
    }
    
    if(!empty($json->sid))
    {
      /*
      $serverModel = new ServerModel;
      $exists = $serverModel->load(array('bookmarkLink' => $get->serverId, 'ip' => $json->ip));
      
      if($exists)
      {
        $result['error'] = true;
        $result['success'] = false;
        $result['exists'] = true;
        $result['serverInfo'] = array
        (
          'name' => $serverModel->name,
          'online' => $serverModel->online
        );
      }
      */
      $result['name'] = $json->name;
      $result['ip'] = $json->ip;
      $result['country'] = $json->country;
      $result['continent'] = $json->region;
    }
    else
    {
      $result['error'] = true;
      $result['success'] = false;
    }
    
    $this->respondString(json_encode($result));
  }
  
  function getServerDataByServerId($get)
  {
    // PInfo API KEY: 50e6b746a1b8bdf7734c0cd105a17f6d36cbde74924f5c8565c751f7c2eba951
    if(!@$get->serverId) return;
    
    $result = array
    (
      'success' => true
    );
    
   // $con = file_get_contents('data/serverList.txt');
    
    $con = @file_get_contents('http://p4f.nodester.com/servers/api/serverInfo/' . $get->serverId);
    
    $found = false;
    
    if($con)
    {
      $json = json_decode($con);
    }
    /*
    foreach($json->data as $server)
    {
      if($server->persistentId == $get->serverId)
      {
        $found = true;
        
        $qry = "http://api.ipinfodb.com/v3/ip-country/?format=json&key=50e6b746a1b8bdf7734c0cd105a17f6d36cbde74924f5c8565c751f7c2eba951&ip=" . $server->ip;
        
        $res = json_decode(file_get_contents($qry));
        
        $result['name'] = $server->name;
        $result['country'] = $res->countryCode;
        $result['continent'] = $server->pingSite;
        $result['ip'] = $server->ip;
      }
    }*/
    
    if(!empty($json->sid))
    {
      $found = true;
    
      $result['name'] = $json->name;
      $result['ip'] = $json->ip;
      $result['country'] = $json->country;
      $result['continent'] = $json->region;
    }
    
    if(!$found)
    {
      $result['error'] = true;
      $result['success'] = false;
    }
    
    $this->respondString(json_encode($result));
  }
  
  function post_addServer($post, $get)
  {
    if(!isLogged()) return;
    
    if(!@$post->name or !@$post->ip or !@$post->port or !@$post->pwd or !@$post->bookmarkLink) return false;
    
    $serverModel = new ServerModel;
    $serverModel->name = trim($post->name);
    $serverModel->ip = trim($post->ip);
    $serverModel->port = trim($post->port);
    $serverModel->pwd = trim($post->pwd);
    $serverModel->bookmarkLink = trim($post->bookmarkLink);
    $serverModel->userId = getUserId();
    
    $state = $serverModel->create();
    
    $this->respondJSON(array
    (
      'state' => $state
    ));
  }
  
  function post_deleteServer($post, $get)
  {
    if(!@$post->serverId) return;
    
    $state = false;
    
    $serverModel = new ServerModel;
    $serverModel->loadById($post->serverId);
    
    if($serverModel->userId == getUserId())
    {
      $state = true;
      
      $serverModel->delete();
    }
    
    $this->respondJSON(array
    (
      'state' => $state
    ));
  }
  
  function kickLog($get)
  {
    if(!isLogged()) return;
    
    $serverId = (int) $get->serverId;
    
    $check = alxDatabaseManager::query("SELECT userId FROM servers WHERE serverId = '{$serverId}' LIMIT 1")->fetch();
    
    if($check->userId != getUserId() && !isTech()) return;
    
    $w = '';
    
    if(@$get->type or @$get->type == '0')
    {
      $w = " AND type = '{$get->type}'"; 
    }

    $kicks = alxDatabaseManager::query("SELECT * FROM kickLog WHERE serverId = '{$serverId}'{$w} ORDER BY kickLogId DESC LIMIT 200");
    
    $this->add('kicks', $kicks);
    $this->add('noBanId', true);
    $this->add('serverChoose', false);
    
    $this->render('../adminPanel/kickLog');
  }

  function t()
  {
    $s = alxDatabaseManager::query("SELECT * FROM servers");
    
    while($item = $s->fetch())
    {
      $h = gethostbyaddr($item->ip);
      
      alxDatabaseManager::query("UPDATE servers SET hostname = '{$h}' WHERE serverId = '{$item->serverId}' LIMIT 1");
    }
    
    $this->respondString('done');
  }
}
