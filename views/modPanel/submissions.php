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
    <a href="#" onclick="t4g.url.action = 'mySubmissions'; t4g.url.redirect()">My Submissions</a> | <a href="#" onclick="t4g.url.action = 'myVotedSubmissions'; t4g.url.redirect()">My Voted Submissions</a>
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
  <h2>Shown Submissions: <span id="subCount"></span></h2>
  <h2>Submissions: <?php echo $data->count ?></h2>
  
  <table class="data hoverData" style="width: 100%">
    <thead>
      <tr style="cursor:pointer">
        <th style="width: 15px" onclick="t4g.url.query.set('sort', '-submissionId'); t4g.url.redirect()">#</th>
        <th style="width: 120px" onclick="t4g.url.query.set('sort', '-created'); t4g.url.redirect()">Created</th>
        <th>Player</th>
        <th style="width: 120px" onclick="t4g.url.query.set('sort', 'type'); t4g.url.redirect()">Type</th>
        <th style="width: 40px" onclick="t4g.url.query.set('sort', 'sourceNucleusId'); t4g.url.redirect()">Resubmit</th>
        <th style="width: 40px" onclick="t4g.url.query.set('sort', '-votesCount'); t4g.url.redirect()">Votes</th>
        <?php if(@$data->customField){ ?>
        <th style="width: 105px" onclick="t4g.url.query.set('sort', '-<?= $data->customField[0] ?>'); t4g.url.redirect()"><?= $data->customField[1] ?></th>
        <?php } ?>
        <?php if(@$data->noContinue){ ?>
        <th style="width: 40px" onclick="t4g.url.query.set('sort', 'done'); t4g.url.redirect()">Done</th>
        <? } ?>
      </tr>
    </thead>
    <tbody>
      <?php $d = array(); $i = 0; while($item = $data->submissions->fetch()): 
        
        /*
        $x = new stdClass;
        
        $votes = alxDatabaseManager::query("SELECT userId, vote FROM submission_votes WHERE type = 'mod' AND submissionId = '{$item->submissionId}'");
        
        $v = 0;
        
        $voters = array();
        $votersYes = array();
        $votersNo = array();
        $votersHold = array();
        $votersSwitch = array();
        
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
          
          if($vote->vote == '4')
          {
            $votersSwitch[] = $vote->userId;
          }
          
          $v++;
        }
        
        $names = array();
        
        $profiles = alxDatabaseManager::query("SELECT name, level FROM profiles WHERE nucleusId = '{$item->targetNucleusId}' ORDER BY name ASC");
        
        
        
        while($profile = $profiles->fetch())
        {
          $names[] = $profile->name . " ({$profile->level})";
        }
        
        
        $voted = false;
        
        if(in_array(getUserId(), $voters))
        {
          $voted = true;
        }
        
        if($v >= $data->votesNeeded && !@$data->noContinue) continue;
        
        if($data->hideVoted && $voted) continue;
        if($data->hideVoted && $item->sourceMail != '' && @getUser(getUserId())->mail == $item->sourceMail) continue;
        
        $c = '';
        
        if(in_array(getUserId(), $votersYes))
        {
          $c = 'alreadyVotedYes';
        }
        elseif(in_array(getUserId(), $votersNo))
        {
          $c = 'alreadyVotedNo';
        }
        elseif(in_array(getUserId(), $votersHold))
        {
          $c = 'alreadyVotedHold';
        }
        elseif(in_array(getUserId(), $votersSwitch))
        {
          $c = 'alreadyVotedSwitch';
        }
        */
        
        // $item->v = $v;
        $item->votesNeeded = $data->votesNeeded;
        
        //$d[] = $x;
        $i++;
        /*
        endwhile;
        
        if(false){
          uasort($d, function($a, $b)
          {
            $a = $a->v;
            $b = $b->v;
            
            if($a == $b) return 0;
            
            return $a > $b ? -1 : 1;
          });
        }
        
        foreach($d as $item):
        */
        
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
      <tr <?php /* class="<?php echo $item->c ?>" */ ?>onclick="<?php $clickFn() ?>" style="cursor: pointer">
        <td><?php echo $item->submissionId ?></td>
        <td><?php echo date('d.m.Y H:i', $item->created) ?></td>
        <td><?php echo $item->names ?>
          <div class="summary"><?php echo substr(htmlspecialchars($item->msg), 0, 75); ?></div>
        </td>
        <td><?php echo $GLOBALS['types'][$item->type] ?></td>
        <td><?php echo $item->sourceNucleusId == '1' ? 'Yes' : 'No' ?></td>
        <td><?php if(@$item->votesCount >= '0') { echo "{$item->votesCount} / {$item->votesNeeded}"; } ?></td>
        <?php if(@$data->customField){ ?>
        <td>
        <?php
          $val = $item->{$data->customField[0]};
          switch($data->customField[2])
          {
            case "date":
              echo date('d.m.Y', $val);
              break;
            case "number":
              echo number_format($val, 0, '.', '\'');
              break;
            case "ratio":
              echo sprintf("%.0f%%", $val * 100);
              break;
            case "function":
              echo @call_user_func($data->customField[3], $val);
              break;
            default:
              echo $val;
          }
        ?></td>
        <? } ?>
        <?php if(@$data->noContinue){ ?>
        <td><?php echo $item->done ? 'Yes' : 'No' ?></td>
        <? } ?>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
</div>
<script type="text/javascript">
window.addEvent('domready', function()
{
  $('subCount').set('text', '<?php echo $i ?>');
});
</script>
