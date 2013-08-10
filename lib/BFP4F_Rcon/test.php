<?php

header('content-type: text/plain');


require 'Base.php';
require 'Players.php';

use BFP4F_Rcon as rc;


$rc = new rc\Base();
$rc->ip = "";
$rc->port = 18866;
$rc->pwd = "";

$rc->init();

$rcp = new rc\Players();
$players = $rcp->fetch();

var_dump($players);
