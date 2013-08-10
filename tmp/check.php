<?php

include '../lib/scoreStats.php';

header('content-type: text/plain');

$n = $_GET['n'];
$p = $_GET['p'];

$s = scoreStats($n, $p);

var_dump($s);
