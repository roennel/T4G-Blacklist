<?php

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

?>
<!DOCTYPE html>
<html>
<head>
  <title>Get Stats</title>
  <script type="text/javascript" src="/js/mootools.js"></script>
</head>
<body>
  
  <div id="result"></div>
  
  <script>
  var nucleusIds = 
  ['<?php
    
    $start = @$_GET['start'] ?: 0;
    $limit = @$_GET['limit'] ?: 20;
    
    
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
  
  
  
  var get = function(nucleusId, index)
  {
    new Request
    ({
      method: 'get',
      url: 'fetchStatsDo.php',
      onSuccess: function(response)
      {
        var info = new Element('div');
        info.set('text', '#' + (index+<?php echo $start ?>) + ' -> ' + nucleusId + ' done');
        
        $('result').grab(info);
      }
    }).send('n=' + nucleusId);
  };
  
  window.addEvent('domready', function()
  {
    var delay = 50;
    nucleusIds.each(function(nucleusId, index)
    {
      (function()
      {
        get(nucleusId, index);
      }).delay(index * delay);
    });
  });
  </script>
</body>
</html>