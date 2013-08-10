<?php

require_once '/home/roennel/p4ftool/alx/alxToolkit.php';

require_once '/home/roennel/p4ftool/alx/alxDatabase/alxDatabaseManager.php';
require_once '/home/roennel/p4ftool/alx/alxMVC/alxModel.php';

require_once '/home/roennel/p4ftool/functions.php';

$database = new stdClass;
$database->adapter = 'MySQL';
$database->host = 'localhost';
$database->user = 't4g_blacklist';
$database->pwd = '3fEJqMUtyXdVWzFJ';
$database->db = 't4g_blacklist';

$adapter = "alxDatabaseAdapter_{$database->adapter}";
    
alx::load("Database/{$database->adapter}", "DatabaseAdapter_{$database->adapter}");

alxDatabaseManager::setAdapter(new $adapter);
alxDatabaseManager::$_config = $database;   
alxDatabaseManager::$_debug = true;
alxDatabaseManager::connect();

$w = 500;
$h = 130;

$img = imageCreateTrueColor($w, $h);

$clr = array
(
  imageColorAllocate($img, 0, 0, 0),
  imageColorAllocate($img, 255, 255, 255),
  imageColorAllocate($img, 246, 176, 40),
  imageColorAllocate($img, 150, 150, 150)
);

if(!@$_GET['devel'])
{
  imageColorTransparent($img, $clr[0]);
}

$s = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM servers")->fetch();

$servers = number_format($s->c, 0, '.', '\'');

imageString($img, 4, 10, 10, "Servers Registered", $clr[1]);
imageString($img, 4, 160, 10, ">", $clr[1]);

imageString($img, 5, 180, 10, $servers, $clr[2]);

imageString($img, 4, 10, 40, "Blacklist Entries by Type:", $clr[2]);

$types = array
(
  '1' => 'Cheating',
  '2' => 'Glitching',
  '3' => 'Statspadding'
);

$s = 60;
$n = 20;
$i = 0;

foreach($types as $code => $label)
{
  $c = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM bans WHERE blacklistId = '{$code}'")->fetch();
  
  $y = $s + ($n * $i);
  
  imageString($img, 4, 10, $y, $label, $clr[1]);
  
  imageString($img, 4, 160, $y, ">", $clr[1]);
  
  imageString($img, 5, 180, $y, number_format($c->c, 0, '.', '\''), $clr[2]);
  
  $i++;  
}


  $k = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM kickLog")->fetch();
  $kicks = number_format($k->c, 0, '.', '\'');
  
  imageLine($img, 243, 10, 243, $h - 10, $clr[3]);
  
  imageString($img, 4, 260, 10, "Kicks Executed", $clr[1]);
  imageString($img, 4, 410, 10, ">", $clr[1]);


  imageString($img, 5, 440, 10, $kicks, $clr[2]);

  imageString($img, 4, 260, 40, "Kicks Executed by Type:", $clr[2]);
  
  $s = 60;
  $n = 20;
  $i = 0;

  foreach($types as $code => $label)
  {
    $c = alxDatabaseManager::query("SELECT COUNT(kl.kickLogId) AS c FROM kickLog AS kl, bans AS b WHERE kl.banId = b.banId AND b.blacklistId = '{$code}'")->fetch();
  
    $y = $s + ($n * $i);
  
    imageString($img, 4, 260, $y, $label, $clr[1]);
  
    imageString($img, 4, 410, $y, ">", $clr[1]);
  
    imageString($img, 5, 440, $y, number_format($c->c, 0, '.', '\''), $clr[2]);
  
    $i++;  
  }


if(!@$_GET['debug'])
{
  header('content-type: image/png');
  imagePNG($img);
}
