<?php require 'img_init.php';

$p = "../lib/pChart2/";

include($p . "class/pData.class.php");
include($p . "class/pDraw.class.php");
include($p . "class/pPie.class.php");
include($p . "class/pImage.class.php");

$sid = (int) $_GET['soldierId'];

$keys = array();

foreach($_GET['key'] as $key)
{
  $keys[] = $key;
}

$callbacks = array
(
  'headshotratio' => function($v)
  {
    return ($v * 100);
  },
  'accuracy' => function($v)
  {
    return ($v * 100);
  }
);

$stats = alxDatabaseManager::query
("
  SELECT * FROM profile_stats WHERE soldierId = '{$sid}' ORDER BY date ASC LIMIT 10
");

$profile = alxDatabaseManager::query
("
  SELECT * FROM profiles WHERE soldierId = '{$sid}' LIMIT 1
")->fetch();

$MyData = new pData();

$points = array();

while($item = $stats->fetch())
{
  $points[] = date('d.m.Y H:i:s', $item->date);
  
  foreach($keys as $key)
  {
    $value = $item->{$key};
    
    if(array_key_exists($key, $callbacks))
    {
      $value = $callbacks[$key]($value);
    }
  
    $MyData->addPoints($value, $key);
  }
}


$MyData->setSerieTicks("Probe 2",4);
$MyData->setAxisName(0, 'Value');

$MyData->addPoints($points,"Labels");
$MyData->setSerieDescription("Labels","Months");
$MyData->setAbscissa("Labels");

$myPicture = new pImage(930,230,$MyData);

$myPicture->Antialias = true;

$myPicture->drawGradientArea(0,0,930,230,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
$myPicture->drawGradientArea(0,0,930,230,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));

$myPicture->drawRectangle(0,0,929,229,array("R"=>0,"G"=>0,"B"=>0));

$myPicture->setFontProperties(array("FontName"=>"{$p}fonts/Forgotte.ttf","FontSize"=>11));
$myPicture->drawText(60,35,"Profile Stats for '{$profile->name}'",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMLEFT));

$myPicture->setFontProperties(array("FontName"=>"{$p}fonts/pf_arma_five.ttf","FontSize"=>6));

$myPicture->setGraphArea(60,40,850,200);

$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"GridAlpha"=>100,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
$myPicture->drawScale($scaleSettings);

$myPicture->drawLegend(850,20,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_VERTICAL));

$myPicture->Antialias = TRUE;

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

$Threshold = array();
$Threshold[] = array("Min"=>0,"Max"=>9999,"R"=>255,"G"=>255,"B"=>255,"Alpha"=>15);

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
$myPicture->drawAreaChart(array("Threshold"=>$Threshold));

$myPicture->drawLineChart(array("ForceColor"=>TRUE,"ForceR"=>0,"ForceG"=>0,"ForceB"=>0));

$myPicture->drawSplineChart(array("PlotBorder"=>TRUE,"BorderSize"=>1,"Surrounding"=>-255,"BorderAlpha"=>80));

if(!@$_GET['debug'])
{
  $myPicture->autoOutput('tmp/lol.png');
}
