<?php
  $this->addContainer
  (
    'userNotification',
    new alxView('userNotification'),
    array()
  );

 $this->insertContainer('userNotification');
?>

<?php 
  $userNucleusIds = array();
  
  $userNucleusIds_ = alxDatabaseManager::query("SELECT nucleusId FROM user_profiles WHERE userId = '" . getUserId(). "'");
  while($userNucleusId = $userNucleusIds_->fetch())
  {
    $userNucleusIds[] = $userNucleusId->nucleusId;
  }
  
  $blocked = false;
  
  if(count($userNucleusIds))
  {
    $banCheck = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM bans WHERE nucleusId IN (" . implode(',', $userNucleusIds) . ") AND active = '1'")->fetch();
    
    if(in_array($data->submission->targetNucleusId, $userNucleusIds) or $banCheck->c > 0)
    {
      $blocked = true;
    }
  }
  
  if($blocked and !isTech())
  {
  ?>
    <div class="sub extended">
      You are not allowed to see this appeal.
    </div>
  <?php
    return;
  }
?>

<?php if(@$data->source[0] == 'G-Ban Import'): ?>

<div class="sub extended">

  <div id="step1">
  <h3>This is a G-Ban Import, our Policy includes unbanning those immediately and automatically resubmit.</h3>
  <h3>Would you like to do so?</h3>
  
  <input type="button" value="Yes" id="yes" />
  </div>
  
  <div id="step2">
  <h3>The Ban has been lifted, Mail sent to Player and the case has been Resubmitted.</h3>  
  </div>
  
  <script type="text/javascript">
  window.addEvent('domready', function()
  {
    $('step2').hide();
    
    $('yes').addEvent('click', function()
    {
      new Request.JSON
      ({
        url: '/en/modPanel/validateImportedBan',
        onSuccess: function(response)
        {
          $('step1').hide();
          $('step2').show();
        }
      }).get('appealId=<?php echo $data->appeal->appealId ?>');
    });
  });
  </script>
  
</div>

<?php else: ?>
 

<div class="sub extended">
  
  <table class="data submissionTable" style="width: 100%">
    <thead>
      <tr>
        <th colspan="99">Appeal Detail</th>
      </tr>
    </thead>
    <tbody>
      <?php if(isAdmin() or isTech() or in_array($data->submission->sourceNucleusId, $userNucleusIds)): ?>
      <tr>
        <td>Submitted By</td>
        <td><?php echo implode(' / ', $data->source) ?><?php if($data->submission->sourceNucleusId){ ?> <a href="submissions?sourceNucleusId=<?php echo $data->submission->sourceNucleusId; ?>">&raquo;</a><?php } ?></td>
      </tr>
      <?php endif; ?>
      <tr>
        <td>Submitted At</td>
        <td><?php echo date('d.m.Y H:i:s', $data->submission->created) ?></td>
      </tr>
      <tr>
        <td>Appealed At</td>
        <td><?php echo date('d.m.Y H:i:s', $data->appeal->created) ?></td>
      </tr>
      <tr>
        <td>Target Player</td>
        <td><?php echo implode(' / ', $data->target) ?><?php if($data->submission->targetNucleusId){ ?> <a href="search?q=<?php echo $data->submission->targetNucleusId; ?>">&raquo;</a><?php } ?></td>
      </tr>
      <?php if($data->prevSubCount > 0 or $data->prevAppCount > 0): ?>
      <tr>
        <td>History</td>
        <td><a href="search?q=<?php echo $data->submission->targetNucleusId; ?>"><?= $data->prevSubCount ?> Submission<?= $data->prevSubCount > 1 ? 's' : '' ?>, <?= $data->prevAppCount ?> Appeal<?= $data->prevAppCount > 1 ? 's' : '' ?></a></td>
      </tr>
      <?php endif ?>
      <tr>
        <td>Type</td>
        <td><?php echo $GLOBALS['types'][$data->submission->type] ?></td>
      </tr>
      <tr>
        <td>Links</td>
        <td>
          <?php
            $this->addContainer
            (
              'submissionLinks',
              new alxView('submissionLinks'),
              array
              (
                'nucleusId' => $data->submission->targetNucleusId,
                'names' => $data->targetNames,
                'soldierIds' => $data->targetSoldierIds
              )
            );
          
            $this->insertContainer('submissionLinks');
          ?>
        </td>
      </tr>
      <tr>
        <td>Submission Message</td>
        <td class="message"><?php echo nl2br(process($data->submission->msg)) ?></td>
      </tr>
      
      <tr>
        <td style="vertical-align: top;margin-bottom: 20px">Score
          <br />
          <span style="font-weight: bold; color: #bb0000">
            These are only Indicators! 
            <br />
            Not concrete evidence.
          </span>
          
          </td>
        <td>
          
          <div style="float: left;">
            
            <div style="float: left; margin-right: 15px">
              <div class="score score_0"></div> Legit
            </div>
            
            <div style="float: left; margin-right: 15px">
              <div class="score score_1"></div> Suspicious
            </div>
            
            <div style="float: left; margin-right: 15px">
              <div class="score score_2"></div> Very Suspicious
            </div>
              
            <div style="float: left; margin-right: 15px">
              <div class="score score_3"></div> Nearly Definite
            </div>
          </div>
          <?php
            $sdbDates = array();
            
            $sd = alxDatabaseManager::query("SELECT DISTINCT(date) AS date FROM profile_stats WHERE soldierId IN ('" . implode(',', $data->targetSoldierIds) . "') ORDER BY date DESC");
            
            while($sdi = $sd->fetch())
            {
              $sdbDates[] = $sdi->date;
            }
            
            $statsTS = @$_GET['statsTS'];
            
            if($statsTS != '0' and !in_array($statsTS, $sdbDates))
            {
              $statsTS = ($sdbDates[0] >= (time()-604800)) ? $sdbDates[0] : null;
            }
          ?>
          <div style="float: right;">
            <select id="sdbSelect">
              <option value="0">Current</option>
            <?php $i = 0;
              foreach($sdbDates as $d): ?>
              <option value="<?php echo $d; ?>"<?php echo ($d == $statsTS) ? ' selected="selected"' : ''; ?>>SDB: <?php echo date('d.m.Y', $d); ?></option>
            <?php $i++;
              endforeach; ?>
            </select>
          </div>
          
          <div style="clear: both;margin-bottom: 20px"></div>
          
          <?php
            $this->addContainer
            (
              'scoreStats',
              new alxView('scoreStats'),
              array
              (
                'targets' => $data->targets,
                'statsTS' => $statsTS
              )
            );
          ?>
          <div id="scoreStats">
            <?php $this->insertContainer('scoreStats'); ?>
          </div>
        </td>  
      </tr>
      
      <tr>
        <td style="vertical-align: middle">Tags</td>
        <td>
          <?php
          $tags = alxDatabaseManager::fetchMultiple("SELECT * FROM tags");
          
          foreach($tags as $tag)
          {
            $la = '';
            
            if(!in_array($tag->tagId, $data->submissionTags))
            {
              $la = ' style="display:none"';
            }
            
            echo "<label class='tagItem' id='tag-{$tag->tagId}' {$la}><input type='checkbox' name='tag' value='{$tag->tagId}' disabled='disabled' checked='checked'>{$tag->label}</label>";
          }
          ?>
        </td>
      </tr>
      
      <tr>
        <td style="vertical-align: top">Additional Evidence
          <div style="font-size:0.9em; color:#aaa; width:180px; margin-top:1em;">A Punkbuster Screenshot, video or any other evidence not already available in the submission.</div>
        </td>
        <td>
          <div>
            <?php
              $c = '';
              if(true or $data->submission->done == '1')
              {
                $c = 'disabled="disabled"';
              }
            ?>
            <textarea id="submissionNote" style="width: 83%;height: 35px" <?= $c ?>></textarea>
            <input type="button" id="addNote" style="width:15%; height: 42px; float:right"  <?= $c ?> value="Add" />
          </div>
          <table class="data" style="width: 100%">
            <tbody>
              <?php foreach($data->submissionNotes as $item):
               ?>
              <tr>
                <td style="width: 15%; vertical-align: middle">
                  <?= $item->userId ? getUser($item->userId)->username : "<a target=\"_blank\" href=\"http://battlefield.play4free.com/en/profile/{$item->sourceNucleusId}\">" . implode(' / ', $item->sourceNames) . "</a>"; ?>
                  <br>
                  <span style="font-size: 9pt; color: #aaa" title="<?php echo $item->date ? date('d.m.Y H:i:s', $item->date) : '' ?>"><?php echo $item->date ? date('d.m.Y', $item->date) : '' ?></span>
                </td>
                <td><?php echo nl2br(process($item->note)); ?></td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </td>
      </tr>
      
      <tr>
        <td>Submission Moderator Votes</td>
        <td><?php echo $data->verified ?></td>
      </tr>
      
      <tr>
        <td>Submission Admin Votes</td>
        <td><?php echo $data->adminVotes ?></td>
      </tr>
      
      
      <tr>
        <td>Submission Voting Reasons</td>
        <td>
          <table class="data" style="width: 100%">
            <thead>
              <tr>
                <th colspan="99">Moderators</th>
              </tr>
            </thead>
            <tbody>
              <?php while($item = $data->modVoteReasons->fetch()): 
      
                  $m = nl2br(process($item->message));
                
               ?>
              <tr>
                <td style="width: 12.5%; vertical-align: top" title="<?php echo $item->date ? date('d.m.Y H:i:s', $item->date) : '' ?>"><?php echo $item->date ? date('d.m.Y', $item->date) : '' ?></td>
                <td style="width: 15%; vertical-align: top"><?php echo getUser($item->userId)->username ?></td>
                <td><?php echo empty($item->message) ? 'No Message' : $m ?></td>
              </tr>
              <?php endwhile ?>
            </tbody>
          </table>

          <table class="data" style="width: 100%; margin-top: 10px">
            <thead>
              <tr>
                <th colspan="99">Admins</th>
              </tr>
            </thead>
            <tbody>
              <?php while($item = $data->adminVoteReasons->fetch()): ?>
              <tr>
                <td style="width: 12.5%; vertical-align: top" title="<?php echo $item->date ? date('d.m.Y H:i:s', $item->date) : '' ?>"><?php echo $item->date ? date('d.m.Y', $item->date) : '' ?></td>
                <td style="width: 15%; vertical-align: top"><?php echo getUser($item->userId)->username ?></td>
                <td><?php echo empty($item->message) ? 'No Message' : nl2br(process($item->message)) ?></td>
              </tr>
              <?php endwhile ?>
            </tbody>
          </table>
          
        </td>
      </tr>
      
      <tr>
        <td>Appeal Message</td>
        <td class="message"><?php echo nl2br(process($data->appeal->appeal)) ?></td>
      </tr>
      
      <tr>
        <td>Appeal Moderator Votes</td>
        <td><?php echo $data->appealVerified ?></td>
      </tr>
      
      <tr>
        <td>Appeal Admin Votes</td>
        <td><?php echo $data->appealAdminVotes ?></td>
      </tr>
      
      <tr>
        <td>Appeal Voting Reasons</td>
        <td>
          <table class="data modVotes" style="width: 100%">
            <thead>
              <tr>
                <th colspan="99">Moderators</th>
              </tr>
            </thead>
            <tbody>
              <?php while($item = $data->appealModVoteReasons->fetch()): 
      
                  $m = 'Hidden';
                  
                  if(!$data->valid)
                  {
                     $m = nl2br(process($item->message));
                  }
                  if($data->appeal->done == '1' or $data->appealModVotes >= $data->votesNeeded)
                  {
                     $m = nl2br(process($item->message));
                  }
               ?>
              <tr>
                <td style="width: 12.5%; vertical-align: top" title="<?php echo $item->date ? date('d.m.Y H:i:s', $item->date) : '' ?>"><?php echo $item->date ? date('d.m.Y', $item->date) : '' ?></td>
                <td style="width: 15%; vertical-align: top"><?php echo getUser($item->userId)->username ?></td>
                <td><?php echo empty($item->message) ? 'No Message' : $m ?></td>
              </tr>
              <?php endwhile ?>
            </tbody>
          </table>

          <table class="data modVotes" style="width: 100%; margin-top: 10px">
            <thead>
              <tr>
                <th colspan="99">Admins</th>
              </tr>
            </thead>
            <tbody>
              <?php while($item = $data->appealAdminVoteReasons->fetch()): if($data->appeal->done != '1') { $msg = 'Hidden'; } else { $msg = $item->message; } ?>
              <tr>
                <td style="width: 12.5%; vertical-align: top" title="<?php echo $item->date ? date('d.m.Y H:i:s', $item->date) : '' ?>"><?php echo $item->date ? date('d.m.Y', $item->date) : '' ?></td>
                <td style="width: 15%; vertical-align: top"><?php echo getUser($item->userId)->username ?></td>
                <td><?php echo empty($msg) ? 'No Message' : nl2br(process($msg)) ?></td>
              </tr>
              <?php endwhile ?>
            </tbody>
          </table>
          
        </td>
      </tr>
      
      <?php if($data->appeal->done != '1' and $data->valid): ?>
      <tr>
        <td style="vertical-align: top">Your Voting Message</td>
        <td>
          <textarea id="voteMessage" style="width: 100%;height: 100px"></textarea>
        </td>
      </tr>
      <?php elseif($data->appeal->done == '1'): ?>
      <tr>
        <td>Result</td>
        <td><?php echo $data->banned ? 'Banned' : 'Lifted' ?></td>
      </tr>
      <?php endif ?>
      
      <?php if($data->appeal->done != '1' && (isMod() && !isAdmin())): ?>
      <tr>
        <td colspan="99">
          <div class="buttons-wrap">
            <div class="buttons buttons-main" style="display: inline-block;">
              <input type="button" id="yes" value="Don't Lift Ban" />
              <input type="button" id="no" value="Lift Ban" />
            </div>
            
            <?php if(!$data->valid): ?>
            You already voted on that case.
            <?php endif ?>
          
            <?php if(isAdmin()): ?>
            You can't vote on a Moderator case as Admin.
            <?php endif ?>
          </div>
        </td>
      </tr>
      <?php endif ?>
    </tbody>
  </table>
  
</div>

<script type="text/javascript">
window.addEvent('domready', function()
{
  var votes = new Hash
  ({
    'yes': [1, 'Don\'t Lift Ban'],
    'no': [2, 'Lift Ban']
  });
  
  <?php if(!$data->valid or isAdmin() or $data->appeal->done == '1'): ?>
  votes.each(function(data, tag)
  {
    if($(tag)) $(tag).hide();
  });
  <?php endif ?>
  
  <?php if($data->submission->type != 'ch'): ?>
  // hide 'Obvious Stats' tag for non-cheating submissions
  var tagOpt = $$('#addTag option[value="4"]')[0];
  tagOpt && tagOpt.dispose();
  <?php endif ?>

  votes.each(function(data, tag)
  {
    if($(tag))
    {
      $(tag).addEvent('click', function()
      {
        var conf = confirm("You sure? Vote: " + data[1]);
        if(!conf) return;
        
        $('voteMessage').removeClass('error');
        
        if($('voteMessage').get('value') == '')
        {
          $('voteMessage').addClass('error');
          return;
        }
        
        $$('.buttons input[type="button"]').set('disabled', true);
        
        new Request.JSON
        ({
          url: 'setAppealState',
          onSuccess: function(response)
          {
            t4g.url.action = 'appeals';
            t4g.url.redirect();
          }
        }).get('appealId=<?php echo $_GET['appealId'] ?>&state=' + data[0] + '&msg=' + encodeURIComponent($('voteMessage').get('value')));
      }); 
    }
  });
  
  
  $('sdbSelect').addEvent('change', function()
  {
    $$('#scoreStats table').setStyle('opacity', '0.75');
    $('scoreStats').load('scoreStats?statsTS=' + this.value + '&nucleusId=<?php echo $data->submission->targetNucleusId; ?>');
  });
});
</script>
<?php endif ?>
