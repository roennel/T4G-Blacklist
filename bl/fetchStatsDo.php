<?php require 'fetchStats.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

$n = @$_GET['n'];

if((int) $n <= 0)
{
  if((int) $_SERVER['argv'][1] > 0)
  {
    $n = $_SERVER['argv'][1];
  }
  else
  {
    exit('error');
  }
} 

check($n);

if(@$_GET['k'])
{
  echo $_GET['k'] . '(' . json_encode(array('success' => 'true')) . ')';
}
else
{
  echo json_encode(array('success' => 'true'));
}
