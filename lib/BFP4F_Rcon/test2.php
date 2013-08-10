<?php

header('content-type: text/plain');


require 'Base.php';
require 'Players.php';
require 'Server.php';
require 'Chat.php';

use BFP4F_Rcon as rc;


$rc = new rc\Base();
$rc->ip = "";
$rc->port = 19166;
$rc->pwd = "";

$rc->init();


$chat = new rc\Chat();

$buffer = $chat->fetch(5);


var_dump($buffer);