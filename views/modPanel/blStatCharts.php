<div class="sub extended">
  <div style="float:right">Show: <a href="#" onclick="t4g.url.query.set('tp', '7day'); t4g.url.redirect()">Last 7 days</a> | <a href="#" onclick="t4g.url.query.set('tp', '1month'); t4g.url.redirect()">Last 1 Month</a> | <a href="#" onclick="t4g.url.query.set('tp', '2months'); t4g.url.redirect()">Last 2 Months</a></div>
</div>

<?php
  
$startTime = strtotime('-' . (@$_GET['tp'] ?: '1month')) ?: time();
$startTime = floor($startTime/86400)*86400;
$endTime = floor(time()/86400)*86400;
  
$days = ($endTime - $startTime) / 86400;
$dateSteps = 1;

if($days > 70) $dateSteps = 7;
else if($days > 31) $dateSteps = 5;
else if($days > 14) $dateSteps = 2;


$dates = array();
$counts = array();
  
for($i = $endTime, $j = 0; $i > $startTime; $i -= 86400, $j++)
{
  $dates[] = !($j % $dateSteps) ? date('M j', $i) : '';
  $counts[$i] = array(
    'c' => 0,
    'c1' => 0,
    'c2' => 0,
    'c3' => 0
  );
}

$dates = array_reverse($dates);
$counts = array_reverse($counts, true);

function array_pluck($data, $key)
{
  return array_map(function($array) use($key){
    return $array[$key];
  }, $data);
}

$getChartUrl = function(&$items) use(&$dates, $counts)
{
  while($vd = $items->fetch())
  {
    if(array_key_exists($vd->dt, $counts))
    {
      $counts[$vd->dt] = array(
        'c' => $vd->c,
        'c1' => $vd->c1,
        'c2' => $vd->c2,
        'c3' => $vd->c3
      );
    }
  }
  
  $chxl = implode('|', $dates);
  $chd = implode(',', array_pluck($counts, 'c1')) . '|' . implode(',', array_pluck($counts, 'c2')) . '|' . implode(',', array_pluck($counts, 'c3')) . '|' . implode(',', array_pluck($counts, 'c'));
  
  if((strlen($chxl) + strlen($chd)) > 1500)
  {
    $chxl = "";
  }
  
  // &chds=0,300&chxr=1,0,300
  return "https://chart.googleapis.com/chart?cht=lc&chs=800x125&chco=71CEEF,EFCE71,71EFCE,FFFFFF&chf=bg,s,65432100&chxt=x,y&chds=a&chxtc=0,-250&chxl=0:|{$chxl}&chd=t:{$chd}";
};
?>

<div class="sub extended">
  
  <h2>Submissions</h2>
  
  <div class="sub extended">
    <?php
      $c = alxDatabaseManager::query("SELECT COUNT(*) AS count FROM `submissions` WHERE (FLOOR(created/86400) * 86400) > {$startTime}")->fetch();
      
      $items = alxDatabaseManager::query("
          SELECT
            FLOOR(created/86400) * 86400 AS dt,
            COUNT(*) AS c,
            SUM(type='ch') AS c1,
            SUM(type='sp') AS c2,
            SUM(type='gl') AS c3
          FROM `submissions`
          GROUP BY dt
          HAVING dt > {$startTime}
          ORDER BY dt
      ");
    ?>
    <h3>Created Per Day: <?php echo number_format($c->count / $days, 0, '.', '\'') ?></h3>
    <img src="<?php echo $getChartUrl($items); ?>&chdl=Cheating|Statspadding|Glitching|Total" style="width:100%" />
  </div>
  
  <div class="sub extended">
    <?php
      $c = alxDatabaseManager::query("SELECT COUNT(*) AS count FROM `log` WHERE action IN ('setFinalSubmissionState_Valid', 'setFinalSubmissionState_Invalid', 'markedSubmissionInvalid') AND (FLOOR(date/86400) * 86400) > {$startTime}")->fetch();
  
      $items = alxDatabaseManager::query("
          SELECT
            FLOOR(date/86400) * 86400 AS dt,
            COUNT(*) AS c,
            SUM(action='setFinalSubmissionState_Valid') AS c1,
            SUM(action='setFinalSubmissionState_Invalid' OR action='markedSubmissionInvalid') AS c2,
            0 AS c3
          FROM `log`
          WHERE
            action IN ('setFinalSubmissionState_Valid', 'setFinalSubmissionState_Invalid', 'markedSubmissionInvalid')
          GROUP BY dt
          HAVING dt > {$startTime}
          ORDER BY dt
      ");
    ?>
    <h3>Closed Per Day: <?php echo number_format($c->count / $days, 0, '.', '\'') ?></h3>
    <img src="<?php echo $getChartUrl($items); ?>&chdl=Valid|Invalid||Total" style="width:100%" />
  </div>
</div>

<div class="sub extended">
  
  <h2>Appeals</h2>
  
  <div class="sub extended">
    <?php
      $c = alxDatabaseManager::query("SELECT COUNT(*) AS count FROM `appeals` WHERE (FLOOR(created/86400) * 86400) > {$startTime}")->fetch();
      
      $items = alxDatabaseManager::query("
          SELECT
            FLOOR(created/86400) * 86400 AS dt,
            COUNT(*) AS c,
            0 AS c1,
            0 AS c2,
            0 AS c3
          FROM `appeals`
          GROUP BY dt
          HAVING dt > {$startTime}
          ORDER BY dt
      ");
    ?>
    <h3>Created Per Day: <?php echo number_format($c->count / $days, 0, '.', '\'') ?></h3>
    <img src="<?php echo $getChartUrl($items); ?>" style="width:100%" />
  </div>
  
  <div class="sub extended">
    <?php
      $c = alxDatabaseManager::query("SELECT COUNT(*) AS count FROM `log` WHERE action IN ('setFinalAppealState_Valid', 'setFinalAppealState_Invalid') AND (FLOOR(date/86400) * 86400) > {$startTime}")->fetch();
  
      $items = alxDatabaseManager::query("
          SELECT
            FLOOR(date/86400) * 86400 AS dt,
            COUNT(*) AS c,
            SUM(action='setFinalAppealState_Valid') AS c1,
            SUM(action='setFinalAppealState_Invalid') AS c2,
            0 AS c3
          FROM `log`
          WHERE
            action IN ('setFinalAppealState_Valid', 'setFinalAppealState_Invalid')
          GROUP BY dt
          HAVING dt > {$startTime}
          ORDER BY dt
      ");
    ?>
    <h3>Closed Per Day: <?php echo number_format($c->count / $days, 0, '.', '\'') ?></h3>
    <img src="<?php echo $getChartUrl($items); ?>&chdl=Valid|Invalid||Total" style="width:100%" />
  </div>
</div>

<div class="sub extended">
  
  <h2>Bans</h2>
  
  <div class="sub extended">
    <?php
      $c = alxDatabaseManager::query("SELECT COUNT(*) AS count FROM `bans` WHERE (FLOOR(created/86400) * 86400) > {$startTime}")->fetch();
      
      $items = alxDatabaseManager::query("
          SELECT
            FLOOR(created/86400) * 86400 AS dt,
            COUNT(*) AS c,
            SUM(blacklistId='1') AS c1,
            SUM(blacklistId='2') AS c2,
            SUM(blacklistId='3') AS c3
          FROM `bans`
          WHERE
            active = '1'
          GROUP BY dt
          HAVING dt > {$startTime}
          ORDER BY dt
      ");
    ?>
    <h3>Created Per Day: <?php echo number_format($c->count / $days, 0, '.', '\'') ?></h3>
    <img src="<?php echo $getChartUrl($items); ?>&chdl=Cheating|Statspadding|Glitching|Total" style="width:100%" />
  </div>
</div>