<?php

header('content-type: text/plain');

error_reporting(E_ALL);

function getJSON($url) {
  $con = file_get_contents($url);
  return json_decode($con);
}

$date = time();
$items = getJSON("http://battlefield.play4free.com/en/profile/stats/2790975769/794463307?g=[%22WeaponStats%22]&_={$date}");

foreach($items->data->WeaponStats as $item)
	echo $item->description->name . "\r\n";
	
?>