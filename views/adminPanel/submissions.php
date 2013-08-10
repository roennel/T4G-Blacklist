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
  <div style="float:left">
    <a href="#" onclick="t4g.url.controller = 'modPanel'; t4g.url.action = 'mySubmissions'; t4g.url.redirect()">My Submissions</a> | <a href="#" onclick="t4g.url.controller = 'modPanel'; t4g.url.action = 'myVotedSubmissions'; t4g.url.redirect()">My Voted Submissions</a>
  </div>
  <div style="float:right">
    Tags: 
    <select onchange="t4g.url.action=this.value ? 'submissionsByTags' : 'submissions'; t4g.url.query.set('tagId', this.value); t4g.url.redirect()">
    <option value=''>Select a tag..</option>
    <?php
     $tags = alxDatabaseManager::fetchMultiple("SELECT * FROM tags");
     
     foreach($tags as $tag)
     {
        $opts = '';
        if(@$data->activeTagId == $tag->tagId)
        {
          $opts = 'selected="selected"';
        }
        echo "<option value='{$tag->tagId}' {$opts}>" . preg_replace('/\s\[.*?\]$/', '', $tag->label) . (@$data->tagCounts[$tag->tagId] ? '*' : '') . '</option>';
     }
     ?>
    </select>
  </div>
</div>

<div class="sub extended">
  
  <h2>Open Submissions: <?php echo $data->modCount ?></h2>
  <h2>Open Admin Submissions: <?php echo $data->adminCount ?> (Own: <?php echo $data->ownCount ?>)</h2>
  <!--<h2>Open Delayed Submissions: <?php echo $data->delayCount ?></h2>-->

  <table class="data" style="width: 100%">
    <thead>
      <tr style="cursor:pointer">
        <th style="width: 15px" onclick="t4g.url.query.set('sort', '-submissionId'); t4g.url.redirect()">#</th>
        <th style="width: 120px" onclick="t4g.url.query.set('sort', '-created'); t4g.url.redirect()">Created</th>
        <th>Player</th>
        <th style="width: 120px" onclick="t4g.url.query.set('sort', 'type'); t4g.url.redirect()">Type</th>
        <th style="width: 100px" onclick="t4g.url.query.set('sort', 'modVotesYes'); t4g.url.redirect()">Mod Votes (Yes / No)</th>
        <th style="width: 40px" onclick="t4g.url.query.set('sort', '-votesCount'); t4g.url.redirect()">Votes</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 0; while($item = $data->submissions->fetch()): 
        
        /*
        $modVotes = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submission_votes WHERE type = 'mod' AND submissionId = '{$item->submissionId}'")->fetch();
        $votes = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE type = 'admin' AND submissionId = '{$item->submissionId}'");
        
        $valid = 0;
        $needed = $data->votesNeeded;
        $voters = array();
        $votersYes = array();
        $votersNo = array();
        $a = 0;
        
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

          $a++;
        }
        
        if($modVotes->c >= 6)
        {
          $i++;
        }
        else
        {
          continue;
        }
        
        if($data->hideVoted && in_array(getUserId(), $voters)) continue;
        if($data->hideVoted && $item->sourceMail != '' && @getUser(getUserId())->mail == $item->sourceMail) continue;
        
        $profiles = alxDatabaseManager::query("SELECT name, level FROM profiles WHERE nucleusId = '{$item->targetNucleusId}' ORDER BY name ASC");
        
        $names = array();
        
        while($profile = $profiles->fetch())
        {
          $names[] = $profile->name . " ({$profile->level})";
        }
        
        $c = '';
        
        if(in_array(getUserId(), $votersYes))
        {
          $c = 'alreadyVotedYes';
        }
        elseif(in_array(getUserId(), $votersNo))
        {
          $c = 'alreadyVotedNo';
        }
        */
        $i++;
        
        $clickFn = function() use($item, $i)
        {
          $s = "t4g.url.action = 'submissionDetail';";
          $s.= "t4g.url.query.set('redirTo', '" . alxRequestHandler::getAction() . "');";
          $s.= "t4g.url.query.set('submissionId', '{$item->submissionId}');";
          $s.= "t4g.url.query.set('i', '{$i}');";
          $s.= "event.ctrlKey ? window.open(t4g.url.build(), '_blank') : t4g.url.redirect()";
          
          echo $s;
        };
        
        ?>
      <tr class="<?php echo $c ?>" onclick="<?php $clickFn() ?>" style="cursor: pointer">
        <td><?php echo $item->submissionId ?></td>
        <td><?php echo date('d.m.Y H:i', $item->created) ?></td>
        <td><?php echo $item->names ?></td>
        <td><?php echo $GLOBALS['types'][$item->type] ?></td>
        <td><?php if(@$item->modVotesCount >= '0') { echo "{$item->modVotesYes} / {$item->modVotesNo}"; } ?></td>
        <td><?php if(@$item->votesCount >= '0') { echo "{$item->votesCount} / {$data->votesNeeded}"; } ?></td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
</div>

<script type="text/javascript">
window.addEvent('domready', function()
{
  $('ovs').set('text', '<?php echo $i ?>');
});
</script>
