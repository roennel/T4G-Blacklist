<div class="sub extended">
  <h1>Statistics</h1>  
  <div style="float:right">View: <a href="#" onclick="t4g.url.action = 'stats'; t4g.url.redirect()">User Stats</a> | <a href="#" onclick="t4g.url.action = 'statCharts'; t4g.url.redirect()">Activity Graphs</a></div>
</div>

<div class="sub extended">
  <div style="float:left">Users: <a href="#" onclick="t4g.url.query.set('users', 'mods'); t4g.url.redirect()">Moderators</a> | <a href="#" onclick="t4g.url.query.set('users', 'admins'); t4g.url.redirect()">Admins</a></div>
  <div style="float:right">Type: 
    <a href="#" onclick="t4g.url.query.set('displayType', 'abs'); t4g.url.redirect()">Absolute</a> | 
    <a href="#" onclick="t4g.url.query.set('displayType', 'rel'); t4g.url.redirect()">Relative</a> | 
    <a href="#" onclick="t4g.url.query.set('displayType', 'con'); t4g.url.redirect()">Consistency</a>
  </div>
</div>

<?php

$type = @$_GET['displayType'];

$f = function($v, $top=false, $noperc=false) use($type)
{
  if($top && $type == 'rel')
  {
    return '';
  }
  
  if((float) $v <= 0.01)
  {
    return '<span style="color: #aaa !important">-</span>';
  }
  
  if(($type == 'rel' or $type == 'con') && !$noperc)
  {
    return number_format($v, 1, '.', '\'') . '%'; 
  }
  else
  {
    return number_format($v, 0, '.', '\'');
  }
};

$f2 = function($v, $dec=1)
{
  return number_format($v, $dec, '.', '\'');
};

?>

<div class="sub extended">
  
  <h2><?php echo $data->label ?> (<?php echo $data->count ?>)</h2>
  
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th style="width: 20%">Name</th>
        <th style="width: 10%">V/D</th>
        <th style="width: 10%">Total Votes</th>
        <th style="width: 10%">Submission: Approve</th>
        <th style="width: 10%">Submission: <br />Don't Approve</th>
        <th style="width: 10%">Submission: Hold</th>
        <th style="width: 10%">Submission: Switch</th>
        <th style="width: 10%">Appeal: <br />Don't Lift Ban</th>
        <th style="width: 10%">Appeal: <br />Lift Ban</th>
        <th style="width: 10%">Score</th>
      </tr>
      <?php
      
        $ts = alxDatabaseManager::query("
          SELECT
            COUNT(*) AS c,
            SUM(vote='1') AS sub1, SUM(vote='2') AS sub2, SUM(vote='3') AS sub3, SUM(vote='4') AS sub4
          FROM submission_votes
          WHERE
            type = '{$data->type}'
        ")->fetch();
        
        $ta = alxDatabaseManager::query("
          SELECT
            COUNT(*) AS c,
            SUM(vote='1') AS app1, SUM(vote='2') AS app2
          FROM appeal_votes
          WHERE
            type = '{$data->type}'
        ")->fetch();
        
        $tv = $ts->c + $ta->c;
        
      ?>
      <tr>
        <th>Total</td>
        <th><span id="vdCount"></span></th>
        <th><?php echo $f($tv, false, true) ?></th>
        <th><?php echo $f($ts->sub1, false, true) . '<br />&raquo; ' . $f2(($ts->sub1 / $tv) * 100)  . '%' ?></th>
        <th><?php echo $f($ts->sub2, false, true) . '<br />&raquo; ' . $f2(($ts->sub2 / $tv) * 100)  . '%'  ?></th>
        <th><?php echo $f($ts->sub3, false, true) . '<br />&raquo; ' . $f2(($ts->sub3 / $tv) * 100)  . '%'  ?></th>
        <th><?php echo $f($ts->sub4, false, true) . '<br />&raquo; ' . $f2(($ts->sub4 / $tv) * 100)  . '%'  ?></th>
        <th><?php echo $f($ta->app1, false, true) . '<br />&raquo; ' . $f2(($ta->app1 / $tv) * 100)  . '%'  ?></th>
        <th><?php echo $f($ta->app2, false, true) . '<br />&raquo; ' . $f2(($ta->app2 / $tv) * 100)  . '%'  ?></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php 
      
      $users = array();
      
      $t2 = 0;
      
      while($item = $data->moderators->fetch())
      {
        $s = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submission_votes WHERE type = '{$data->type}' AND userId = '{$item->userId}'")->fetch();
        $a = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM appeal_votes WHERE type = '{$data->type}' AND userId = '{$item->userId}'")->fetch();
        
        $item->c = $s->c + $a->c;
        $item->t = $item->c / (((time() - $item->joined) / 3600) / 24);
        
        $t2+= $item->t;
        
        $users[] = $item;
      }  
      
      uasort($users, function($a, $b)
      {
        if($a->t == $b->t) return 0;
  
        return ($a->t < $b->t) ? 1 : -1;
      });
      
      foreach($users as $item):
      
        $sub = alxDatabaseManager::query("
          SELECT
            SUM(vote='1') AS sub1, SUM(vote='2') AS sub2, SUM(vote='3') AS sub3, SUM(vote='4') AS sub4
          FROM submission_votes
          WHERE
            type = '{$data->type}' AND userId = '{$item->userId}'
        ")->fetch();
        
        $app = alxDatabaseManager::query("
          SELECT
            SUM(vote='1') AS app1, SUM(vote='2') AS app2
          FROM appeal_votes
          WHERE
            type = '{$data->type}' AND userId = '{$item->userId}'
        ")->fetch();
      
        
        if($type == 'rel')
        {
          $qsub1 = $ts->sub1 / $data->count;
          $qsub2 = $ts->sub2 / $data->count;
          $qsub3 = $ts->sub3 / $data->count;
          $qsub4 = $ts->sub4 / $data->count;
          
          $qapp1 = $ta->app1 / $data->count;
          $qapp2 = $ta->app2 / $data->count;
          
          $item->c = (($item->c > 0 ? $item->c : 1) / ($tv > 0 ? $tv : 10000)) * 100;
          $sub->sub1 = (($sub->sub1 > 0 ? $sub->sub1 : 1) / ($ts->sub1 > 0 ? $ts->sub1 : 10000)) * 100;
          $sub->sub2 = (($sub->sub2 > 0 ? $sub->sub2 : 1) / ($ts->sub2 > 0 ? $ts->sub2 : 10000)) * 100;
          $sub->sub3 = (($sub->sub3 > 0 ? $sub->sub3 : 1) / ($ts->sub3 > 0 ? $ts->sub3 : 10000)) * 100;
          $sub->sub4 = (($sub->sub4 > 0 ? $sub->sub4 : 1) / ($ts->sub4 > 0 ? $ts->sub4 : 10000)) * 100;
          
          $app->app1 = (($app->app1 > 0 ? $app->app1 : 1) / ($ta->app1 > 0 ? $ta->app1 : 10000)) * 100;
          $app->app2 = (($app->app2 > 0 ? $app->app2 : 1) / ($ta->app2 > 0 ? $ta->app2 : 10000)) * 100;
          
          $qsub1 = $qsub2 = $qsub3 = $qsub4 = $qapp1 = $qapp2 = $qt = 100 / $data->count;
          
        }

        if($type == 'con')
        {
          $st = alxDatabaseManager::query
          ("
            SELECT COUNT(b.banId) AS c FROM bans AS b, submissions AS s, submission_votes AS sv 
            WHERE 
              b.submissionId = s.submissionId 
            AND 
              sv.submissionId = s.submissionId 
            AND 
              sv.vote = '1' 
            AND 
              sv.userId = '{$item->userId}'
            AND
              sv.type = '{$data->type}'
          ")->fetch();
          
          $at = alxDatabaseManager::query
          ("
            SELECT COUNT(b.banId) AS c FROM bans AS b, appeals AS a, appeal_votes AS av 
            WHERE 
              b.banId = a.banId 
            AND 
              av.appealId = a.appealId 
            AND 
              av.vote = '1' 
            AND 
              av.userId = '{$item->userId}'
            AND
              av.type = '{$data->type}'
          ")->fetch();
          
          $item->c = ((($st->c + $at->c) / ($item->c == 0 ? 1 : $item->c)) * 100);
          $sub->sub1 = (($st->c / ($sub->sub1 == 0 ? 1 : $sub->sub1)) * 100);
          $sub->sub2 = 100 - $sub->sub1;
          $sub->sub3 = 0;
          $sub->sub4 = 0;
          $app->app1 = (($at->c / ($app->app1 == 0 ? 1 : $app->app1)) * 100);
          $app->app2 = 100 - $app->app1;
        }

        $qt = $t2 / $data->count;
        
        // class="<?php echo $item->c >= $qt ? 'statsValid' : 'statsInvalid' 
        
        $l = function($vote,$type='myVotedSubmissions') use($item)
        {
          $s = " onclick=\"t4g.url.action = '{$type}'; t4g.url.query.set('userId', '{$item->userId}');";
          $s.= "t4g.url.query.set('vote', '{$vote}'); t4g.url.redirect();\" style=\"cursor: pointer\"";
        
          echo $s;
        };
        
        $uvote = alxDatabaseManager::query
        ("
          SELECT SUM(score) AS s FROM user_votes WHERE userId = '{$item->userId}'
        ")->fetch();
        
        $lastVote = alxDatabaseManager::query
        ("
          SELECT date FROM submission_votes WHERE userId = '{$item->userId}' ORDER BY date DESC LIMIT 1
        ")->fetch();
        
        $lastVote2 = alxDatabaseManager::query
        ("
          SELECT date FROM appeal_votes WHERE userId = '{$item->userId}' ORDER BY date DESC LIMIT 1
        ")->fetch();
        
        if(@$lastVote2->date > @$lastVote->date)
        {
          $lastVote = $lastVote2;
        }
        
        if(@$lastVote->date == 0)
        {
          @$lastVote->date = 1356994800;
        }
        
        $clr = '#0a0';
        
        $diff = time() - $lastVote->date;
        
        if($diff >= 1209600 && $diff < 2419200)
        {
          $clr = '#a80';
        }
        
        if($diff >= 2419200)
        {
          $clr = '#a00';
        }
        
        ?>
      <tr>
        <td><?php echo $item->username ?>
          <br />
          <span style="font-size: 8pt;color: #ccc"><?php echo date('d.m.Y', $item->joined) ?></span>
           | <span style="font-size: 8pt;color: <?php echo $clr ?>"><?php echo date('d.m.Y', $lastVote->date) ?></span>
        </td>
        <td class="<?php echo $item->t >= 15 ? 'statsValid' : 'statsInvalid' ?>"><?php echo $f($item->t, false, true) ?></td>
        <td<?php $l('') ?>><?php echo $f($item->c) ?></td>
        <td<?php $l(1) ?>><?php echo $f($sub->sub1) ?></td>
        <td<?php $l(2) ?>><?php echo $f($sub->sub2) ?></td>
        <td<?php $l(3) ?>><?php echo $f($sub->sub3) ?></td>
        <td<?php $l(4) ?>><?php echo $f($sub->sub4) ?></td>
        <td<?php $l(1, 'appeals') ?>><?php echo $f($app->app1) ?></td>
        <td<?php $l(2, 'appeals') ?>><?php echo $f($app->app2) ?></td>
        <td><?php echo $uvote->s ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>
<script type="text/javascript">
  $('vdCount').set('text', "<?php echo $f($t2, false, true); ?>");
</script>
