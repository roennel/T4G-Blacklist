<?php
  $this->addContainer
  (
    'userNotification',
    new alxView('userNotification'),
    array()
  );

 $this->insertContainer('userNotification');
?>

<div class="sub extended">
  <h2>Appeals: <span id="subCount"></span></h2>
  <a href="#" onclick="t4g.url.action = 'appeals'; t4g.url.redirect()">Open Appeals</a> | <a href="#" onclick="t4g.url.action = 'myVotedAppeals'; t4g.url.redirect()">My Voted Appeals</a>
</div>

<div class="sub extended">
  <table class="data hoverData" style="width: 100%">
    <thead>
      <tr>
        <th style="width: 15px">#</th>
        <th style="width: 120px">Created</th>
        <th>Player</th>
        <th style="width: 120px">Type</th>
        <th style="width: 40px">Votes</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 0; while($item = $data->appeals->fetch()): 
        
        $votes = alxDatabaseManager::query("SELECT * FROM appeal_votes WHERE type = 'mod' AND appealId = '{$item->appealId}'");
        
        $ban = alxDatabaseManager::query("SELECT * FROM bans WHERE banId = '{$item->banId}' LIMIT 1")->fetch();
        
        if(@$ban->banId <= 0) continue;
        
        $submission = alxDatabaseManager::query("SELECT * FROM submissions WHERE submissionId = '{$ban->submissionId}' LIMIT 1")->fetch();
        
        $v = 0;
        
        $voters = array();
        $votersYes = array();
        $votersNo = array();
        $votersHold = array();
        
        while($vote = $votes->fetch())
        {
          $voters[] = $vote->userId;
          
          if($vote->vote == '1')
          {
            $votersYes[] = $vote->userId;
          }
          
          if($vote->vote == '2')
          {
            $votersNo[] = $vote->userId;
          }
          
          if($vote->vote == '3')
          {
            $votersHold[] = $vote->userId;
          }
          
          $v++;
        }
        
        if($data->userId && $data->vote)
        {
          $valid = true;
          
          switch($data->vote)
          {
            case 1:
              if(!in_array($data->userId, $votersYes)) $valid = false;
            break;
              
            case 2:
              if(!in_array($data->userId, $votersNo)) $valid = false;
            break;  
          }
          
          if(!$valid) continue;
        }
        
        $profiles = alxDatabaseManager::query("SELECT name, level FROM profiles WHERE nucleusId = '{$ban->nucleusId}' ORDER BY name ASC");
        
        $names = array();
        
        while($profile = $profiles->fetch())
        {
          $names[] = $profile->name . " ({$profile->level})";
        }
        
        $subVotes = alxDatabaseManager::query("SELECT userId FROM submission_votes WHERE type = 'mod' AND submissionId = '{$submission->submissionId}'");
        $subVoters = array();
        
        while($vote = $subVotes->fetch())
        {
          $subVoters[] = $vote->userId;
        }
        
        $voted = false;
        
        if(in_array(getUserId(), $voters) or in_array(getUserId(), $subVoters) or ($submission->sourceMail != '' && @getUser(getUserId())->mail == $submission->sourceMail))
        {
          $voted = true;
        }
        
        if(!$data->userId && $v >= $data->votesNeeded && !@$data->noContinue) continue;
        
        if(!$data->userId && $data->hideVoted && $voted) continue;
        
        $c = '';
        
        if(in_array(getUserId(), $votersYes))
        {
          $c = 'alreadyVotedNo';
        }
        elseif(in_array(getUserId(), $votersNo))
        {
          $c = 'alreadyVotedYes';
        }
        elseif(in_array(getUserId(), $votersHold))
        {
          $c = 'alreadyVotedHold';
        }
        
        ?>
      <tr class="<?php echo $c ?>" onclick="t4g.url.action = 'appealDetail'; t4g.url.query.set('appealId', '<?php echo $item->appealId ?>'); t4g.url.redirect()" style="cursor: pointer">
        <td><?php echo $item->appealId ?></td>
        <td><?php echo date('d.m.Y H:i', $item->created) ?></td>
        <td><?php echo implode(' / ', $names) ?>
          <div class="summary"><?php echo substr(htmlspecialchars($item->appeal), 0, 75); ?></div>
        </td>
        <td><?php echo $GLOBALS['types'][$submission->type] ?></td>
        <td><?php echo $v ?> / <?php echo $data->votesNeeded ?></td>
      </tr>
      <?php $i++; endwhile ?>
    </tbody>
  </table>
</div>
<script type="text/javascript">
window.addEvent('domready', function()
{
  $('subCount').set('text', '<?php echo $i ?>');
});
</script>
