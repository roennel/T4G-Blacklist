<?php

$data = array
(
  '1358168977' => 'headshotratio',
  '1358169818' => 'accuracy',
  '1358170619' => 'kpm',
  '1358172191' => 'bestRangedKill'
);

$scan = scandir('/var/www/t4g_blacklist/imgCache');
array_shift($scan);
array_shift($scan);


?>
<!DOCTYPE html>
<html>
<head>
  <title>Weapon Charts</title>
  <style>
    body
    {
      background-color: #222; 
    }
    
    h2
    {
      color: #eee;
      font-size: 18pt;
      font-family: verdana;
      float: left;
      clear: left;
      display: block;
      margin: 3px; 
      padding: 0;
    }
    
    div
    {
      margin: 0px;
      padding: 0px;
      float: left;
      clear: left;
    }
    
    img
    {
      margin: 1px;
      padding: 0px;
      float: left;
    }
  </style>
</head>
<body>
  <div>
    <?php 
    foreach($data as $stamp => $key):
    ?>
    <div>
      <h2><?php echo ucFirst($key) ?> / <?php echo date('d.m.Y H:i:s', $stamp) ?></h2>
      <br /><br />
    <?php
      foreach($scan as $file):
        
        $spl = explode('_', $file);
        
        if($spl[1] == $key && $spl[2] == $stamp):
        ?>
        <img src="/imgCache/<?php echo $file ?>" />
        <?php
        endif;
        
      endforeach;
    ?>
    </div>
    <?php
    endforeach;
    ?>
  </div>
</body>
</html>