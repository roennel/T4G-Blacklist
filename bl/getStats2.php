<?php

header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);

require_once '/home/roennel/p4ftool/alx/alxToolkit.php';
require_once '/home/roennel/p4ftool/alx/alxDatabase/alxDatabaseManager.php';
require_once '/home/roennel/p4ftool/alx/alxMVC/alxModel.php';
require_once '/home/roennel/p4ftool/functions.php';

$database = new stdClass;
$database->adapter = 'MySQL';
$database->host = 'localhost';
$database->user = 'bft';
$database->pwd = 'QGy5FfabVeuGrjR7';
$database->db = 'bft';

$adapter = "alxDatabaseAdapter_{$database->adapter}";
    
alx::load("Database/{$database->adapter}", "DatabaseAdapter_{$database->adapter}");

alxDatabaseManager::setAdapter(new $adapter);
alxDatabaseManager::$_config = $database;   
alxDatabaseManager::$_debug = true;
alxDatabaseManager::connect();

$start = @$_GET['start'] ?: 0;
    $limit = @$_GET['limit'] ?: 20;
    
?>
<!DOCTYPE html>
<html>
<head>
  <title>Get Stats</title>
  <script type="text/javascript" src="/js/mootools.js"></script>
</head>
<body>
  
  <div class="bl">Fetching Stats -> <span id="f">0</span> / <?php echo $limit ?> 
    | <span id="s">0</span> / s
    | <span id="m">0</span> / m
    | <span id="h">0</span> / h 
    | <span id="r">0</span>m remaining 
    | <span id="t">0</span>m total
  </div>
  
  <div id="result_1" class="result"></div>
  <div id="result_2" class="result"></div>
  <div id="result_3" class="result"></div>
  <div id="result_4" class="result"></div>
  
  
  <script>
  
  var nucleusIds = 
  ['<?php
    
    
    
    
    //$nucleusIds = alxDatabaseManager::query("SELECT p.nucleusId FROM profiles AS p WHERE p.nucleusId > 1000 AND p.nucleusId NOT IN (SELECT nucleusId FROM bans WHERE nucleusId = p.nucleusId) ORDER BY p.nucleusId LIMIT {$start},{$limit}");
    //$nucleusIds = alxDatabaseManager::query("SELECT nucleusId FROM bans WHERE blacklistId = '1' LIMIT {$start}, {$limit}");
    
    $nucleusIds = alxDatabaseManager::query("SELECT * FROM stats_soldiers WHERE nucleusId > 497880612 GROUP BY nucleusId LIMIT {$start},{$limit}");
    
    $d = array();
    
    while($nucleusId = $nucleusIds->fetch())
    {
      $d[] = $nucleusId->nucleusId;
    }
    
    echo implode('\',\'', $d);
    
    ?>'];
  
  var c = 4;
  var i = 0;
  var q = 1;
  var s = new Date().getTime();
  
  var t = 0;
  
  (function()
  {
    t++;
    
    $('t').set('text', (t / 60).toFixed(2));
  }).periodical(1000);

  function fetch(nucleusId, index, url, q)
  {
    new Request.JSONP
    ({
      url: url + 'fetchStatsDo.php',
      data:
      {
        n: nucleusId
      },
      callbackKey: 'k',
      onComplete: function(response)
      {
        var info = new Element('div');
        info.set('text', '#' + (index+<?php echo $start ?>) + ' -> ' + nucleusId + ' done');
        
        $('result_' + q).grab(info);
        
        $('f').set('text', i);
        
        info.scrollIntoView();
        
        var d = (i / (new Date().getTime() - s)) * 1000;
        
        $('s').set('text', d.toFixed(2));
        $('m').set('text', (d * 60).toFixed(2));
        $('h').set('text', (d * 3600).toFixed(2));
        
        var r = (<?php echo $limit ?> - i) / d;
        
        $('r').set('text', (r / 60).toFixed(2));
        
        i++;
      }
    }).send();
  }
  
  window.addEvent('domready', function()
  {
    nucleusIds.each(function(nucleusId, index)
    {
      (function()
      {
        if(q > 4) q = 1;
      
        if(q > 1)
        {
          url = 'http://blacklist' + q + '.tools4games.com/bl/';
        }
        else
        {
          url = 'http://blacklist.tools4games.com/bl/';
        }
        console.log(index, nucleusId, url);
        fetch(nucleusId, index, url, q);
      
        q++;
    }).delay(index * 100);
    
  });
  });

  </script>
  
  <style>
    body
    {
      font-family: verdana;
      font-size: 8pt;
      height: 100%;
      width: 100%;
      margin: 0px;
      padding: 0px;
    }
    
    div.bl
    {
      border-bottom: 1px solid black;
      width: 100%;
      padding: 10px;
    }
    
    div.result
    {
      width: 23%;
      height: 800px;
      float: left;
      border-right: 1px solid black;  
      overflow: auto; 
    }
  </style>
</body>
</html>