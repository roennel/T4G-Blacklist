<?php 

class SubmitController extends alxController
{
  function index()
  {
    $this->render();
  }
  
  function checkPlayerBan($get)
  {
    if(!@$get->nucleusId) return;
    
    $result = array
    (
      'success' => true,
      'valid' => false,
      'existingBan' => false,
      'existingSubmission' => false,
      'blacklistLabel' => ''
    );
    
    $nucleusId = mysql_real_escape_string($get->nucleusId);
    
    $url = "http://battlefield.play4free.com/en/profile/soldiers/{$nucleusId}";
    
    $con = @file_get_contents($url);
    
    $json = json_decode($con);
    
    if(@count($json->data) > 0)
    {
      $result['valid'] = true;
      
			foreach($json->data as $player)
			{
				$profileId = $player->id;
				$kit = $player->kit;
				$level = $player->level;
				$name = $player->name;

				alxDatabaseManager::query("DELETE FROM profiles WHERE nucleusId = '{$nucleusId}' AND soldierId = '{$profileId}'");
				alxDatabaseManager::query("INSERT INTO profiles SET nucleusId = '{$nucleusId}', soldierId = '{$profileId}', kit = '{$kit}', level = '{$level}', name = '{$name}'");
				
				$result['names'][] = $name;
			}
	}

    $check = alxDatabaseManager::query("SELECT b.*, bl.label FROM bans AS b, blacklists AS bl WHERE b.active = '1' AND b.nucleusId = '{$nucleusId}' AND bl.blacklistId = b.blacklistId LIMIT 1")->fetch();
    
    if(@$check->banId > 0)
    {
      $result['banId'] = $check->banId;
      $result['existingBan'] = true;
      $result['banLabel'] = $check->label;
      
      if($check->blacklistId != 1)
      {
        $result['allowResubmit'] = true;
      }
    }
    
    $check2 = alxDatabaseManager::query("SELECT * FROM submissions WHERE targetNucleusId = '{$nucleusId}' AND done = '0' LIMIT 1")->fetch();
    
    if(@$check2->submissionId > 0)
    {
      $result['submissionId'] = $check2->submissionId;
      $result['existingSubmission'] = true;
      $result['submissionLabel'] = $GLOBALS['types'][$check2->type];
      
      $check3 = alxDatabaseManager::query("SELECT COUNT(submissionVoteId) AS c FROM submission_votes WHERE submissionId = '{$check2->submissionId}'")->fetch();
      
      if($check3->c <= 3)
      {
        $result['addEvidence'] = true;
      }
    }
    
    if(!isTech() && !isAdmin())
    {
      $check3 = alxDatabaseManager::query("SELECT s.submissionId, s.created, MAX(sv.date) AS doneDate FROM submissions AS s INNER JOIN submission_votes AS sv ON sv.submissionId = s.submissionId WHERE s.targetNucleusId = '{$nucleusId}' AND s.done = '1' GROUP BY s.submissionId ORDER BY s.created DESC LIMIT 1")->fetch();
      
      if(@$check3->submissionId > 0 && $check3->doneDate > (time() - (24*7*60*60)))
      {
        $result['recentlySubmitted'] = true;
      }
    }
    
    $this->respondString(json_encode($result));
  }
  
  function checkPlayer($get)
  {
    if(!@$get->nucleusId) return;
    
    $nucleusId = mysql_real_escape_string($get->nucleusId);
    
    $url = "http://battlefield.play4free.com/en/profile/soldiers/{$nucleusId}";
    
    $con = file_get_contents($url);
    
    $json = json_decode($con);
    
    foreach($json->data as $player)
    {
      $profileId = $player->id;
      $kit = $player->kit;
      $level = $player->level;
      $name = $player->name;
      alxDatabaseManager::query("DELETE FROM profiles WHERE nucleusId = '{$nucleusId}' AND soldierId = '{$profileId}'");
      alxDatabaseManager::query("INSERT INTO profiles SET nucleusId = '{$nucleusId}', soldierId = '{$profileId}', kit = '{$kit}', level = '{$level}', name = '{$name}'");
    }
    
    $this->respondString($con);
  }

  function post_submitData($post, $get)
  {
    if(!@$post->sourceNucleusId or !@$post->sourceMail or !@$post->targetNucleusId or !@$post->type or !@$post->msg or !@$post->csrf_token) return;
    
    $result = array
    (
      'state' => false,
      'ticket' => null,
      'error' => null
    );
    
    if($post->csrf_token != $_SESSION['csrf_token'] or $_SESSION['csrf_token_time'] <= (time()-3600))
    {
      $result['error'] = 'csrf_mismatch';
      $this->respondJSON($result);
      return;
    }
    
    $sourceIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
    
    // flood protection
    if(isLogged())
    { // max 60 subs in a timespan of 60 minutes, 30 minutes penalty
      $fp_period = 60 * 60; // in seconds
      $fp_maxSubsInPeriod = 60;
      $fp_waitingPeriod = 30 * 60; // in seconds
    }
    else
    { // max 30 subs in a timespan of 60 minutes, 2 hour penalty
      $fp_period = 60 * 60; // in seconds
      $fp_maxSubsInPeriod = 30;
      $fp_waitingPeriod = 120 * 60; // in seconds
    }
    
    if(!isMod() and !isAdmin())
    {
      $fp_check = alxDatabaseManager::query
      ("
        SELECT
          COUNT(*) AS c, MAX(created) AS lastSubTime
        FROM submissions
        WHERE
          ip = '{$sourceIp}' AND
          created > (SELECT created-{$fp_period} FROM submissions WHERE ip = '{$sourceIp}' ORDER BY created DESC LIMIT 1)
      ")->fetch();
      
      if($fp_check->c >= $fp_maxSubsInPeriod and @$fp_check->lastSubTime > (time() - $fp_waitingPeriod))
      {
        $result['error'] = 'flood_protection';
        $this->respondJSON($result);
        return;
      }
    }
    
    // check if submission for target player already exists
    $check2 = alxDatabaseManager::query("SELECT * FROM submissions WHERE targetNucleusId = '{$nucleusId}' AND done = '0' LIMIT 1")->fetch();
    
    if(@$check2->submissionId > 0)
    {
      return;
    }
    
    $msg = $post->msg;
    // $msg = str_replace('[__]', '+', $post->msg);
    // $msg = str_replace('[___]', '#', $msg);
    // $msg = str_replace('[_]', '&', $msg);
    
    $submissionModel = new SubmissionModel;
    $submissionModel->created = time();
    $submissionModel->sourceNucleusId = $post->sourceNucleusId;
    $submissionModel->sourceMail = $post->sourceMail;
    $submissionModel->targetNucleusId = $post->targetNucleusId;
    $submissionModel->type = $post->type;
    $submissionModel->msg = mysql_real_escape_string($msg);
    $submissionModel->ip = $sourceIp;
    
    if(in_array($sourceIp, array('90.222.111.186', '5.64.5.100', '90.214.35.162')))
    {
      $submissionModel->postponed = 1;
    }
    
    $_SESSION['sourceNucleusId'] = $post->sourceNucleusId;
    $_SESSION['sourceMail'] = $post->sourceMail;
    
    $result['state'] = $submissionModel->create();
    $result['ticket'] = $submissionModel->submissionId;
    
    if(isMod() or isAdmin())
    {
      $result['link'] = '/modPanel/submissionDetail?submissionId=' . $submissionModel->submissionId;
    }
    
    // Check for 'Staff Submitted' Tag
    if(isMod() or isAdmin())
    {
      $userId = getUserId();
      
      alxDatabaseManager::query
      ("
        INSERT INTO 
          submission_tags 
        SET 
          submissionId = '{$submissionModel->submissionId}',
          tagId = '5',
          userId = '{$userId}'
      ");
    }
    
    // Check for 'Bad Submitter' Tag
    $check = alxDatabaseManager::query
    ("
      SELECT nucleusId FROM bad_submitters WHERE nucleusId = '{$post->sourceNucleusId}'
    ")->fetch();
    
    if(@$check->nucleusId > 0)
    {
      alxDatabaseManager::query
      ("
        INSERT INTO 
          submission_tags 
        SET 
          submissionId = '{$submissionModel->submissionId}',
          tagId = '8',
          userId = '0'
      ");
    }
    
    // Check for 'Video Evidence' Tag
    if(strpos($msg, 'youtube.com/') !== false or strpos($msg, 'youtu.be/') !== false)
    {
      alxDatabaseManager::query
      ("
        INSERT INTO 
          submission_tags 
        SET 
          submissionId = '{$submissionModel->submissionId}',
          tagId = '9',
          userId = '0'
      ");
    }
    
    // 'Dirty PBSS' Tag
    if(strpos($msg, 'pbssv.herokuapp.com/rgbm#!player/') !== false)
    {
      alxDatabaseManager::query
      ("
        INSERT INTO 
          submission_tags 
        SET 
          submissionId = '{$submissionModel->submissionId}',
          tagId = '3',
          userId = '0'
      ");
    }
    
    // check($post->targetNucleusId);
  
    $cmd = "nice php /var/www/t4g_blacklist/bl/fetchStatsDo.php {$post->targetNucleusId} 2>&1 & echo $!";
    pclose(popen($cmd, 'r'));
    
    $this->respondJSON($result);
  }
  
 
  function post_addEvidence($post, $get)
  {
    if(!@$post->sourceNucleusId or !@$post->targetNucleusId or !@$post->msg or !@$post->csrf_token) return;
    
    $result = array
    (
      'state' => false,
      'ticket' => null,
      'error' => null
    );
    
    if($post->csrf_token != $_SESSION['csrf_token'] or $_SESSION['csrf_token_time'] <= (time()-3600))
    {
      $result['error'] = 'csrf_mismatch';
      $this->respondJSON($result);
      return;
    }
    
    $nucleusId = $post->targetNucleusId;
    
    $submission = alxDatabaseManager::query("SELECT * FROM submissions WHERE targetNucleusId = '{$nucleusId}' AND done = '0' LIMIT 1")->fetch();
    
    if(!@$submission->submissionId)
    {
      $result['error'] = 'not_found';
      $this->respondJSON($result);
      return;
    }
    
    $msg = $post->msg;
    $note = mysql_real_escape_string($msg);
    $now = time();
    $userId = getUserId() ?: 0;
    
    $_SESSION['sourceNucleusId'] = $post->sourceNucleusId;
    
    alxDatabaseManager::query("INSERT INTO submission_notes SET submissionId = '{$submission->submissionId}', date = '{$now}', userId = '{$userId}', note = '{$note}', sourceNucleusId = '{$post->sourceNucleusId}'");
    
    $result['state'] = true;
    $result['ticket'] = $submission->submissionId;
    
    if(isMod() or isAdmin())
    {
      $result['link'] = '/modPanel/submissionDetail?submissionId=' . $submission->submissionId;
    }
    
    $this->respondJSON($result);
  }
  
  function preview($get)
  {
    if(!isMod()) return;
    
    if(!@$get->nucleusId) return;
    
    $nucleus = $get->nucleusId;
        
    $target = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$nucleus}'");
    $targetNames = array();
    $targetNames2 = array();
    $targetSoldierIds = array();
    $targets = array();
    
    while($item = $target->fetch())
    {
      $targets[] = $item;
      $targetNames[] = "{$item->name} ({$item->level})";  
      $targetNames2[] = $item->name;
      $targetSoldierIds[] = $item->soldierId;
    }
    
    $this->add('nucleusId', $nucleus);
    $this->add('target', $targetNames);
    $this->add('targetNames', $targetNames2);
    $this->add('targets', $targets);
    $this->add('targetSoldierIds', $targetSoldierIds);
    
    $this->respond('../modPanel/submissionPreview');
  }
}
