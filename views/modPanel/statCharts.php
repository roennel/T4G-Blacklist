<div class="sub extended">
  <h1>Statistics</h1>
  <div style="float:right">View: <a href="#" onclick="t4g.url.action = 'stats'; t4g.url.redirect()">User Stats</a> | <a href="#" onclick="t4g.url.action = 'statCharts'; t4g.url.redirect()">Activity Graphs</a></div>
</div>

<div class="sub extended">
  <div style="float:left">Users: <a href="#" onclick="t4g.url.query.set('users', 'mods'); t4g.url.redirect()">Moderators</a> | <a href="#" onclick="t4g.url.query.set('users', 'admins'); t4g.url.redirect()">Admins</a></div>
  <div style="float:right">Show: <a href="#" onclick="t4g.url.query.set('tp', '7day'); t4g.url.redirect()">Last 7 days</a> | <a href="#" onclick="t4g.url.query.set('tp', '1month'); t4g.url.redirect()">Last 1 Month</a> | <a href="#" onclick="t4g.url.query.set('tp', '2months'); t4g.url.redirect()">Last 2 Months</a></div>
</div>

<?php

$s_actions = "'setSubmissionState_Valid', 'setSubmissionState_Invalid'";
$a_actions = "'setAppealState_Valid', 'setAppealState_Invalid'";
  
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
    'c2' => 0
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

$getChartUrl = function($userId) use($startTime, $s_actions, $a_actions, &$dates, $counts)
{
  
  $votes = alxDatabaseManager::query("
      SELECT
        FLOOR(date/86400) * 86400 AS dt,
        COUNT(*) AS c,
        SUM(action IN ({$s_actions})) AS c1,
        SUM(action IN ({$a_actions})) AS c2
      FROM `log`
      WHERE
        userId IN ({$userId}) AND
        action IN ({$s_actions}, {$a_actions})
      GROUP BY dt
      HAVING dt > {$startTime}
      ORDER BY dt
  ");
  
  while($vd = $votes->fetch())
  {
    if(array_key_exists($vd->dt, $counts))
    {
      $counts[$vd->dt] = array(
        'c' => $vd->c,
        'c1' => $vd->c1,
        'c2' => $vd->c2
      );
    }
  }
  
  $chxl = implode('|', $dates);
  $chd = implode(',', array_pluck($counts, 'c1')) . '|' . implode(',', array_pluck($counts, 'c2')) . '|' . implode(',', array_pluck($counts, 'c'));
  
  if((strlen($chxl) + strlen($chd)) > 1500)
  {
    $chxl = "";
  }
  
  // &chds=0,300&chxr=1,0,300
  return "https://chart.googleapis.com/chart?cht=lc&chs=800x125&chco=71CEEF,EFCE71,FFFFFF&chf=bg,s,65432100&chxt=x,y&chds=a&chxtc=0,-250&chxl=0:|{$chxl}&chd=t:{$chd}";
};
?>

<?php

$users = array();
$userIds = array();

while($item = $data->moderators->fetch())
{
  $userIds[] = $item->userId;
  
  // Votes count
  $c = alxDatabaseManager::query("
    SELECT
      COUNT(*) AS count
    FROM `log`
    WHERE
      userId IN ({$item->userId}) AND
      action IN ({$s_actions}, {$a_actions}) AND
      (FLOOR(date/86400) * 86400) > {$startTime}
  ")->fetch();
  
  // All actions count (tagging, changing type, etc).
  // Certain actions like final votes might count twice since they get 2 log entries (setSubmissionState + setFinalSubmissionState)
  $c2 = alxDatabaseManager::query("
    SELECT
      COUNT(*) AS count
    FROM `log`
    WHERE
      userId IN ({$item->userId}) AND
      type IN ('mod', 'admin') AND
      (FLOOR(date/86400) * 86400) > {$startTime}
  ")->fetch();
  
  $item->c = $c->count;
  $item->c2 = $c2->count;
  
  // $item->vd = $item->c / ($item->joined ? min($days, (time()-$item->joined)/86400) : $days);
  $item->vd = $item->c / $days;
  $item->ad = $item->c2 / $days;
  
  $users[] = $item;
}
  
uasort($users, function($a, $b)
{
  return ($a->vd < $b->vd) ? 1 : ($a->vd == $b->vd ? 0 : -1);
});


// Totals for the main 'Moderators' or 'Admins' chart
$tc = alxDatabaseManager::query("
  SELECT
    COUNT(*) AS count
  FROM `log`
  WHERE
    userId IN (" . implode(',', $userIds) . ") AND
    action IN ({$s_actions}, {$a_actions}) AND
    (FLOOR(date/86400) * 86400) > {$startTime}
")->fetch()->count;

// All Actions
$tc2 = alxDatabaseManager::query("
  SELECT
    COUNT(*) AS count
  FROM `log`
  WHERE
    userId IN (" . implode(',', $userIds) . ") AND
    type IN ('mod', 'admin') AND
    (FLOOR(date/86400) * 86400) > {$startTime}
")->fetch()->count;

$tvd = $tc / $days;
$tad = $tc2 / $days;
?>

<div class="sub extended" id="total">
  
  <h2><?php echo $data->count ?> <?php echo $data->label ?></h2>
  <div style="font-size:0.9em; color: #ccc"><?php echo number_format($tc, 0, '.', '\''); ?> votes | <?php echo number_format($tvd, 0, '.', '\''); ?> V/D | <?php echo number_format($tad, 0, '.', '\''); ?> A/D</div>
  <br>  
  <div class="sub extended">
    <img src="<?php echo $getChartUrl(implode(',', $userIds)); ?>&chdl=Submissions|Appeals|Total" style="width:100%" />
  </div>
</div>

<div class="sub extended">
  <?php foreach($users as $user): ?>
  <div class="sub extended" id="user-<?php echo $user->userId; ?>">
    <h3 style="color: #FFA500;"><?php echo $user->username; ?> <a href="#user-<?= $user->userId ?>" style="color: #555">#</a></h3>
    <div style="font-size:0.9em; color: #ccc"><?php echo number_format($user->c, 0, '.', '\''); ?> votes | <?php echo number_format($user->vd, 0, '.', '\''); ?> V/D | <?php echo number_format($user->ad, 0, '.', '\''); ?> A/D</div>
    <br>
    <img src="<?php echo $getChartUrl($user->userId); ?>" style="width:100%" />
  </div>
  <?php endforeach; ?>
</div>
