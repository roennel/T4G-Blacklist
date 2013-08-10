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
      You are not allowed to see this submission.
    </div>
  <?php
    return;
  }
?>

<div class="sub extended">
  
  <table class="data submissionTable" style="width: 100%">
    <thead>
      <tr>
        <th colspan="99">Submission Detail</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Submitted At</td>
        <td><?php echo date('d.m.Y H:i:s', $data->submission->created) ?></td>
      </tr>
      <?php if(isAdmin() or isTech() or in_array($data->submission->sourceNucleusId, $userNucleusIds)): ?>
      <tr>
        <td>Submitted By</td>
        <td><?php echo implode(' / ', $data->source) ?><?php if($data->submission->sourceNucleusId){ ?> <a href="submissions?sourceNucleusId=<?php echo $data->submission->sourceNucleusId; ?>">&raquo;</a><?php } ?></td>
      </tr>
      <?php endif; ?>
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
        <td>PB GUID</td>
        <td>
          <?php
          
            $n = alxDatabaseManager::query
            ("
              SELECT GUID FROM pb_guid_match WHERE nucleusId = '{$data->submission->targetNucleusId}' LIMIT 1
            ")->fetch();
            
            echo @$n ? $n->GUID : '?';
            
            $guid = @$n->GUID;
          ?>
        </td>
      </tr>
      <tr>
        <td>IP's</td>
        <td>
          <?php
          
            $ips = alxDatabaseManager::fetchMultiple
            ("
              SELECT date, ip FROM pb_guid_ips WHERE GUID = '{$guid}'
            ");
            
            $i = [];
            
            foreach($ips as $ip)
            {
              $ip->ip = explode(':', $ip->ip)[0];
              $i[] = "{$ip->ip} (" . (date('d.m.Y H:i', $ip->date)) . ")";
            }
            
            if(count($i) == 0) echo '?';
            
            echo implode(' / ', $i);
          ?>
        </td>
      </tr>
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
        <td>Message</td>
        <td class="message"><?php echo nl2br(process($data->submission->msg)) ?></td>
      </tr>
      <?php if($guid > 0): ?>
      <tr>
        <td>PB Log</td>
        <td>
          <div style="overflow: auto; width: 100%; height: 150px">
            <table>
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Message</th>
                </tr>
              </thead>
              <tbody>
              <?php
          
              $log = alxDatabaseManager::fetchMultiple
              ("
                SELECT date, msg FROM pb_log WHERE msg LIKE '%{$guid}%' ORDER BY date DESC LIMIT 100
              ");
          
              foreach($log as $item)
              {
                ?>
                <tr>
                  <td style="width: 120px"><?=date('d.m.Y H:i:s', $item->date) ?></td>
                  <td><?=$item->msg ?></td>
                </tr>
                <?php
              }
            
              ?>
              </tbody>
            </table>
          </div>
        </td>
      </tr>
      <?php endif ?>
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
              if($sdbDates[0] >= (time()-604800)){
                $statsTS = $sdbDates[0];
              }else{
                $statsTS = null;
                
                // Update SDB with fresh stats
                $cmd = "nice php /var/www/t4g_blacklist/bl/fetchStatsDo.php {$data->submission->targetNucleusId} 2>&1 & echo $!";
                pclose(popen($cmd, 'r'));
              }
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
          $tagsAdd = array();
          
          $tags = alxDatabaseManager::fetchMultiple("SELECT * FROM tags");
          
          foreach($tags as $tag)
          {
            $la = '';
            $ca = '';
            if(!in_array($tag->tagId, $data->submissionTags))
            {
              $tagsAdd[] = $tag;
              $la = ' style="display:none"';
            }
            if($tag->editable == '0')
            {
              $ca = ' disabled="disabled"';
            }
            echo "<label class='tagItem' id='tag-{$tag->tagId}' {$la}><input type='checkbox' name='tag' value='{$tag->tagId}' {$ca} checked='checked'>{$tag->label}</label>";
          }
          ?>
          
          <select id="addTag" style="float:right; margin:0">
            <option value=''>Add a tag..</option>
            <?php foreach($tagsAdd as $tag):?>
            <?php if($tag->editable == '1'){ ?><option value="<?= $tag->tagId ?>"><?= $tag->label ?></option><?php } ?>
            <?php endforeach; ?>
          </select>
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
              if($data->submission->done == '1')
              {
                $c = 'disabled="disabled"';
              }
            ?>
            <textarea id="submissionNote" style="width: 82%;height: 35px" <?= $c ?>></textarea>
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
        <td>Moderator Votes</td>
        <td><?php echo $data->verified ?></td>
      </tr>
      
      <?php if(!empty($data->adminVotes)): ?>
      <tr>
        <td>Admin Votes</td>
        <td><?php echo $data->adminVotes ?></td>
      </tr>
      <?php endif ?>
      
      <?php if($data->submission->done != '1' and $data->valid and !isAdmin()): ?>
      <tr>
        <td style="vertical-align: top">Your Voting Message</td>
        <td>
          <textarea id="voteMessage" style="width: 100%;height: 100px"></textarea>
        </td>
      </tr>
      <?php elseif($data->submission->done == '1'): ?>
      <tr>
        <td>Result</td>
        <td><?php echo $data->banned ? 'Banned (' . date('d.m.Y', $data->banned->created) . ')' : 'Invalid' ?></td>
      </tr>
      <?php endif ?>
      <tr>
        <td>Voting Reasons</td>
        <td>
          <table class="data modVotes" style="width: 100%">
            <thead>
              <tr>
                <th colspan="99">Moderators</th>
              </tr>
            </thead>
            <tbody>
              <?php while($item = $data->modVoteReasons->fetch()): 
              
                $m = 'Hidden';
                
                // if($item->userId == getUserId())
                if(!$data->valid or isAdmin() or $data->submission->done == '1')
                {
                  $m = nl2br(process($item->message));
                }
              
               ?>
              <tr id="vote<?= $item->submissionVoteId ?>">
                <td style="width: 10%; font-size: 9pt;" title="<?php echo $item->date ? date('d.m.Y H:i:s', $item->date) : '' ?>"><?php echo $item->date ? date('d.m.Y', $item->date) : '' ?></td>
                <td style="width: 15%; vertical-align: top">
                  <?php echo getUser($item->userId)->username ?>
                  <br>
                  <span style="font-size: 9pt; color: #aaa"><?= getClanName(getUser($item->userId)->clanId) ?></span>
                </td>
                <td style="width: 54%"><?php echo empty($item->message) ? 'No Message' : $m ?></td>
                <td style="text-align: right; width: 16.5%; font-size: 9pt">
                  <?php
                    if(isAdmin())
                    {
                      ?>
                      <a href="http://blacklist.tools4games.com/en/adminPanel/addUserVote?submissionId=<?php echo $data->submission->submissionId ?>&userId=<?php echo $item->userId ?>&userVoteType=submission&voteId=<?= $item->submissionVoteId ?>">[REPORT/VOTE]</a>
                      <?php
                    }
                  ?>
                </td>
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
              <?php while($item = $data->adminVoteReasons->fetch()): ?>
              <tr id="vote<?= $item->submissionVoteId ?>">
                <td style="width: 10%; font-size: 9pt;" title="<?php echo $item->date ? date('d.m.Y H:i:s', $item->date) : '' ?>"><?php echo $item->date ? date('d.m.Y', $item->date) : '' ?></td>
                <td style="width: 15%; vertical-align: top">
                  <?php echo getUser($item->userId)->username ?>
                  <br>
                  <span style="font-size: 9pt; color: #aaa"><?= getClanName(getUser($item->userId)->clanId) ?></span>
                </td>
                <td><?php echo empty($item->message) ? 'No Message' : nl2br(process($data->submission->done == '1' ? $item->message : 'Hidden')) ?></td>
              </tr>
              <?php endwhile ?>
            </tbody>
          </table>
          
        </td>
      </tr>
      
      <?php if($data->submission->done != '1'): ?>
      <tr>
        <td colspan="99">
          <div class="buttons-wrap">
            <div class="buttons buttons-main" style="display: inline-block;">
              <input type="button" id="yes" value="Approve" />
              <input type="button" id="no" value="Don't Approve" />
              <!-- <input type="button" id="delay" value="Hold" /> -->
              <input type="button" id="switch" value="Switch List Type" />
            </div>

            <div class="buttons buttons-misc" style="float:right">
              After Voting: 
              <select id="redirToNext">
                <?php if(@$_GET['redirTo']): ?>
                <option value="goToNext" data-url="<?= $_GET['redirTo'] ?>?<?= getQueryStr(array('redirToNext' => 1)) ?>">Go to Next</option>
                <?php endif; ?>
                <option value="goToList">Go Back to List</option>
                <option value="refreshSub">Refresh</option>
              </select>
           </div>
            
            <?php if(!$data->valid): ?>
            You already voted on that case.
            <?php endif ?>
          
            <?php if(isAdmin()): ?>
            You can't vote on a Moderator case as Admin. <a href="#" onclick="t4g.url.controller = 'adminPanel'; t4g.url.redirect()">View in Admin Panel</a>
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
  <?php if(!$data->valid or isAdmin() or $data->submission->done == '1'): ?>
  if($('yes')) $('yes').hide();
  if($('no')) $('no').hide();
  // if($('delay')) $('delay').hide();
  if($('switch')) $('switch').hide();
  <?php endif ?>
  
  <?php if($data->submission->type != 'ch'): ?>
  // hide 'Obvious Stats' tag for non-cheating submissions
  var tagOpt = $$('#addTag option[value="4"]')[0];
  tagOpt && tagOpt.dispose();
  <?php endif ?>
  
  var redirSelect = $('redirToNext');
  
  if(window.localStorage && redirSelect)
  {
    if(localStorage.bl_redirToNext)
    {
      redirSelect.set('value', localStorage.bl_redirToNext);
      if(redirSelect.get('value') == '')
      {
        redirSelect.getElement('option').set('selected', true);
      }
    }
    
    redirSelect.addEvent('change', function()
    {
      if(this.value != 'refreshSub')
      {
        localStorage.bl_redirToNext = this.value;
      }
    });
  }

  function successRedir()
  {
    var redirToNext = $('redirToNext').getSelected()[0];
    switch(redirToNext.value)
    {
      case 'goToNext':
        // t4g.url.action = t4g.url.query.get('redirTo') || 'submissions';
        // t4g.url.query.set('redirToNext', 1);
        location.href = redirToNext.get('data-url');
        return;
        break;
      case 'goToList':
        t4g.url.action = t4g.url.query.get('redirTo') || 'submissions';
        t4g.url.query.erase('redirTo');
        break;
      case 'refreshSub':
        break;
    }
      
    t4g.url.redirect();
  }
  
  function prefetch(url)
  {
    var link = new Element('link');
    link.set('rel', 'prerender');
    link.set('href', url);
    $$('head').grab(link);
  }
  
  if($('voteMessage'))
  {
    var prefetched = false;
    $('voteMessage').addEvent('focus', function()
    {
      if(prefetched)
        return;
      
      prefetched = true;
      
      var opt = $('redirToNext').getSelected()[0];
      if(opt.get('data-url'))
      {
        prefetch(opt.get('data-url'));
      }
    });
  }
  
  if($('yes') && $('no'))
  {
  $('yes').addEvent('click', function()
  {
    this.addClass('pressed');
    
    var conf = confirm("You sure? Vote: Approve");
    if(!conf)
    {
      this.removeClass('pressed');
      return;
    }
    
    $('voteMessage').removeClass('error');
    
    if($('voteMessage').get('value') == '')
    {
      $('voteMessage').addClass('error');
      return;
    }
    
    $$('.buttons input[type="button"]').set('disabled', true);
    
    new Request.JSON
    ({
      url: 'setSubmissionState',
      onSuccess: function(response)
      {
        successRedir();
      }
    }).get('submissionId=<?php echo $_GET['submissionId'] ?>&state=1&msg=' + encodeURIComponent($('voteMessage').get('value')));
  });
  
  $('no').addEvent('click', function()
  {
    this.addClass('pressed');
    
    var conf = confirm("You sure? Vote: Don't Approve");
    if(!conf)
    {
      this.removeClass('pressed');
      return;
    }
    
    $('voteMessage').removeClass('error');
    
    if($('voteMessage').get('value') == '')
    {
      $('voteMessage').addClass('error');
      return;
    }
    
    $$('.buttons input[type="button"]').set('disabled', true);
    
    new Request.JSON
    ({
      url: 'setSubmissionState',
      onSuccess: function(response)
      {
        successRedir();
      }
    }).get('submissionId=<?php echo $_GET['submissionId'] ?>&state=2&msg=' + encodeURIComponent($('voteMessage').get('value')));
  });
  
  /*
  $('delay').addEvent('click', function()
  {
    var conf = confirm("You sure? Vote: Hold");
    if(!conf) return;
    
    $('voteMessage').removeClass('error');
    
    if($('voteMessage').get('value') == '')
    {
      $('voteMessage').addClass('error');
      return;
    }
    
    new Request.JSON
    ({
      url: 'setSubmissionState',
      onSuccess: function(response)
      {
        successRedir();
      }
    }).get('submissionId=<?php echo $_GET['submissionId'] ?>&state=3&msg=' + encodeURIComponent($('voteMessage').get('value')));
  });
  */
  
  $('switch').addEvent('click', function()
  {
    var conf = confirm("You sure? Vote: Switch List Type");
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
      url: 'setSubmissionState',
      onSuccess: function(response)
      {
        successRedir();
      }
    }).get('submissionId=<?php echo $_GET['submissionId'] ?>&state=4&msg=' + encodeURIComponent($('voteMessage').get('value')));
  });
  }
  
  if($('yes') && $('no') && $('yes').getStyle('display') != 'none')
  {
    function keyHandler(e)
    {
      if((e.shift && e.alt) || this == buttonDiv)
      {
        switch(e.key)
        {
          case 'a':
            $('yes').fireEvent('click');
            break;
          case 'd':
            $('no').fireEvent('click');
            break;
          case 's':
            $('switch').fireEvent('click');
            break;
          case 'e':
            var textarea = $('voteMessage');
            textarea && textarea.focus();
            break;
          default:
            return;
        }
        return false;
      }
    }
    
    var buttonDiv = $$('div.buttons-wrap')[0];
    
    document.addEvent('keydown', keyHandler);
    if(buttonDiv)
    {
      buttonDiv.addEvent('keydown', keyHandler);
    }
  }
  
  $$('input[name="tag"]').addEvent('change', function()
  {
    var tag = this;
    
    new Request.JSON
    ({
      url: 'setSubmissionTags',
      onSuccess: function(response)
      {
        $(tag).getParent().dispose();
      }
    }).get('submissionId=<?php echo $_GET['submissionId'] ?>&tagId=' + tag.value + '&state=' + (tag.checked ? '1' : '0'));
    
    $(tag).set('disabled', true);
  });
  
  $('addTag').addEvent('change', function()
  {
    var select = this;
    
    if(select.value == '') return;
    
    new Request.JSON
    ({
      url: 'setSubmissionTags',
      onSuccess: function(response)
      {
        $('tag-' + select.value).setStyle('display', '');
        select.getSelected()[0].dispose();
        select.disabled = false;
      }
    }).get('submissionId=<?php echo $_GET['submissionId'] ?>&tagId=' + select.value + '&state=1');
    
    select.disabled = true;
  });
  
  $('addNote').addEvent('click', function()
  {
    $('submissionNote').removeClass('error');
    
    if($('submissionNote').get('value') == '')
    {
      $('submissionNote').addClass('error');
      return;
    }
    
    $('addNote').set('disabled', true);
    
    new Request.JSON
    ({
      url: 'addSubmissionNote',
      onSuccess: function(response)
      {
        t4g.url.redirect(); // refresh
      }
    }).get('submissionId=<?php echo $_GET['submissionId'] ?>&note=' + encodeURIComponent($('submissionNote').get('value')));
  });
  
  $('sdbSelect').addEvent('change', function()
  {
    $$('#scoreStats table').setStyle('opacity', '0.75');
    $('scoreStats').load('scoreStats?statsTS=' + this.value + '&nucleusId=<?php echo $data->submission->targetNucleusId; ?>');
  });
})
</script>