<?php

class ModPanelController extends alxController
{
  public $votesNeeded = 5;
  public $votesNeeded_ggc = 2;
  public $adminVotesNeeded = 3;
  
  function index()
  {
    $this->render();
  }
  
  function submissions($get)
  {
    if(!isMod()) return;
    
    $userId = getUserId();
    $user = getUser($userId);
    
    $limit = 9999999;
    $showDone = false;
    $sort = '-lastSeen';
    
    $query = '';
    $join = '';
    $select = '';
    $join_profileStats = false;
    $customField = null;
    
    if(@$get->hideVoted == '1')
    {
      $query .= "
        AND s.submissionId NOT IN (SELECT submissionId FROM submission_votes WHERE userId = '{$userId}') 
        AND s.sourceMail != '{$user->mail}'
      ";
      $limit = 50;
    }
    
    if(@$get->type)
    {
      $type = mysql_real_escape_string($get->type);
      $query .= " AND s.type='{$type}'";
    }
    
    if(@$get->minLevel)
    {
      $minLevel = mysql_real_escape_string($get->minLevel);
      $query .= " AND p.level >= '{$minLevel}'";
      $select .= ", MAX(p.level) AS level";
      $customField = array("level", "Level", "number");
    }
    
    if(@$get->tagged)
    {
      if($get->tagged == 'true')
        $query .= " AND s.submissionId IN (SELECT submissionId FROM submission_tags)";
      else
        $query .= " AND s.submissionId NOT IN (SELECT submissionId FROM submission_tags)";
    }
    
    if(@$get->minVotes)
    {
      $minVotes = mysql_real_escape_string($get->minVotes);
      $query .= " AND (SELECT COUNT(submissionVoteId) FROM submission_votes WHERE submissionId = s.submissionId AND type = 'mod') >= '{$minVotes}'";
    }
    
    if(@$get->minHSRatio)
    {
      $hsRatio = mysql_real_escape_string($get->minHSRatio);
      
      $join_profileStats = true;
      $query .= " AND p.kit != '0' AND ps.kills > '200' AND ps.headshotratio >= '{$hsRatio}'";
      $select .= ", MAX(ps.headshotratio) AS hsRatio";
      $customField = array("hsRatio", "HS%", "ratio");
      // $limit = 50;
    }
    
    if(@$get->minBestScore)
    {
      $bestScore = mysql_real_escape_string($get->minBestScore);
      
      $join_profileStats = true;
      $query .= " AND ps.bestScore >= '{$bestScore}'";
      $select .= ", MAX(ps.bestScore) AS bestScore";
      $customField = array("bestScore", "Best Score", "number");
      // $limit = 50;
    }
  
    if(@$get->sourceNucleusId)
    {
      $nucleusId = mysql_real_escape_string($get->sourceNucleusId);
      
      $query .= " AND s.sourceNucleusId='{$nucleusId}'";
      $sort = '-created';
      $showDone = true;
    }
    
    if($join_profileStats)
    {
      $join .= " LEFT JOIN profile_stats AS ps ON p.soldierId = ps.soldierId";
    }

    if(true)
    {
      $sort = @$get->sort ?: $sort;
      
      $sortOrder = "ASC";
      $sort2 = '';
      
      if(substr($sort, 0, 1) == "-")
      {
        $sort = substr($sort, 1);
        $sortOrder = "DESC";
      }
      
      if($sort == 'lastSeen')
      {
        $join .= " LEFT JOIN profile_lastSeen AS pls ON p.soldierId = pls.soldierId";
        $select .= ", FLOOR(MAX(pls.date)/86400) * 86400 AS lastSeen";
        if(!$customField)
        {
          $customField = array("lastSeen", "Last Seen", "function", "time2str");
        }
        $sort2 .= ", s.submissionId ASC";
      }
      
      $sort = " ORDER BY " . mysql_real_escape_string($sort) . " " . $sortOrder . $sort2;
    }
    
    if(!$showDone)
    {
      $query = " AND s.done = '0' AND s.modDone = '0'" . $query;
    }
    
    $c = "
      SELECT COUNT(DISTINCT s.submissionId) AS c 
      FROM submissions AS s
      LEFT JOIN profiles AS p ON s.targetNucleusId = p.nucleusId
      {$join}
      WHERE s.postponed = '0'{$query}
      ";
    
    $s = "
    SELECT s.submissionId, s.created, s.sourceNucleusId, s.sourceMail, s.targetNucleusId, s.type, s.msg,
    GROUP_CONCAT(DISTINCT p.name SEPARATOR ' / ') AS names,
    (SELECT COUNT(submissionVoteId) FROM submission_votes WHERE submissionId = s.submissionId AND type = 'mod') AS votesCount
    {$select}
    FROM submissions AS s 
    LEFT JOIN profiles AS p ON s.targetNucleusId = p.nucleusId
    {$join}
    WHERE s.postponed = '0'{$query} 
    GROUP BY s.submissionId
    {$sort}
    LIMIT {$limit}
    ";
    
    if(@$get->redirToNext && $get->i)
    {
      $i = 0;
      $lastSubId = @$get->submissionId;
      $submissions = alxDatabaseManager::query($s);
      
      while($item = $submissions->fetch())
      {
        $i++;
        if($i >= $get->i && $lastSubId != $item->submissionId)
        {
          $params = getQueryStr(array
          (
            'redirToNext' => null,
            'submissionId' => $item->submissionId
          ));
          
          header('location: submissionDetail?' . $params);
          return;
        }
      }
    }
    
    $submissions = alxDatabaseManager::query($s);
    $count = alxDatabaseManager::query($c)->fetch(); 

    $tagCounts = array();
    $tags = alxDatabaseManager::query("SELECT tagId FROM tags");
    
    while($tag = $tags->fetch())
    {
      $tagCounts[$tag->tagId] = alxDatabaseManager::query("
        SELECT COUNT(*) AS c
        FROM submission_tags AS st, submissions AS s
        WHERE st.tagId = '{$tag->tagId}'
        AND s.submissionId = st.submissionId
        AND s.postponed = '0'
        AND s.done = '0'
        AND s.sourceMail != '{$user->mail}'
        AND s.submissionId NOT IN (SELECT submissionId FROM submission_votes WHERE userId = '{$userId}') 
        AND s.modDone = '0'
        LIMIT 1
      ")->fetch()->c;
    }
    
    $this->add('tagCounts', $tagCounts);
    
    $this->add('customField', $customField);
    
    $this->add('votesNeeded', $this->votesNeeded);
    $this->add('submissions', $submissions);
    $this->add('count', number_format($count->c, 0, '.', '\''));
    
    $this->render();
  }
  
  function myVotedSubmissions($get)
  {
    if(!isMod() && !isAdmin()) return;
    
    $userId = @$get->userId ?: getUserId();
    
    $w = "WHERE sv.userId = '{$userId}' AND s.submissionId = sv.submissionId";
    
    if(@$get->vote)
    {
      $w .= " AND sv.vote = '{$get->vote}'";
    }
    
    $submissions = alxDatabaseManager::query("
    SELECT s.submissionId, s.sourceNucleusId, s.targetNucleusId, s.type, s.done, s.postponed, sv.date AS created, IF(s.done='1', sv.message, '') AS msg,
    (SELECT GROUP_CONCAT(name SEPARATOR ' / ') FROM profiles p WHERE p.nucleusId = s.targetNucleusId) AS names,
    (SELECT COUNT(submissionVoteId) FROM submission_votes WHERE submissionId = s.submissionId AND type = 'mod') AS votesCount
    FROM submissions AS s, submission_votes AS sv {$w} ORDER BY sv.submissionVoteId DESC LIMIT 250");
    $count = alxDatabaseManager::query("SELECT COUNT(s.submissionId) AS c FROM submissions AS s, submission_votes AS sv {$w}")->fetch();
    
    $this->add('votesNeeded', $this->votesNeeded);
    $this->add('submissions', $submissions);
    $this->add('count', $count->c);
    $this->add('noContinue', true);
    
    $this->render('submissions');
  }
  
  function mySubmissions($get)
  {
    if(!isMod() && !isAdmin()) return;
    
    $userId = getUserId();
    $userEmail = getUser($userId)->mail;
    
    $submissions = alxDatabaseManager::query("SELECT s.*,
    (SELECT GROUP_CONCAT(name SEPARATOR ' / ') FROM profiles p WHERE p.nucleusId = s.targetNucleusId) AS names,
    (SELECT COUNT(submissionVoteId) FROM submission_votes WHERE submissionId = s.submissionId AND type = 'mod') AS votesCount
    FROM submissions AS s WHERE s.sourceMail = '{$userEmail}' ORDER BY s.created DESC");
    $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submissions WHERE sourceMail = '{$userEmail}'")->fetch();
    
    $this->add('votesNeeded', $this->votesNeeded);
    $this->add('submissions', $submissions);
    $this->add('count', $count->c);
    $this->add('noContinue', true);
    
    $this->render('submissions');
  }
  
  function submissionsByTags($get)
  {
    if(!isMod()) return;
    
    $s = ' ORDER BY type, created';
    
    if(@$get->sort)
    {
      $sort = $get->sort;
      $sortOrder = "ASC";
      
      if(substr($sort, 0, 1) == "-")
      {
        $sort = substr($sort, 1);
        $sortOrder = "DESC";
      }
      
      if($sort == 'lastSeen')
      {
      }
      else
      {
        $s = " ORDER BY " . mysql_real_escape_string($sort) . " " . $sortOrder;
      }
    }
    
    $t = '';
    
    if(@$get->tagId)
    {
      $tagIds = array_filter(explode(',', $get->tagId), 'is_numeric');
      
      $w_inc = array_filter($tagIds, function ($v) { return $v >= 0; });
      $w_exc = array_map('abs', array_diff($tagIds, $w_inc));
      
      if($w_inc)
        $t .= " AND s.submissionId IN (SELECT submissionId FROM submission_tags WHERE tagId IN (" . implode(',', $w_inc) . "))";
        
      if($w_exc)
        $t .= " AND s.submissionId NOT IN (SELECT submissionId FROM submission_tags WHERE tagId IN (" . implode(',', $w_exc) . "))";
    }
    
    $userId = getUserId();
    $user = getUser($userId);
    if(@$get->hideVoted == '1' or @$get->hideVoted == 'true')
    {
      $t .= " AND s.submissionId NOT IN (SELECT submissionId FROM submission_votes WHERE userId = '{$userId}') 
      AND s.sourceMail != '{$user->mail}'";
    }
    
    $s = "SELECT s.*,
    (SELECT GROUP_CONCAT(name SEPARATOR ' / ') FROM profiles p WHERE p.nucleusId = s.targetNucleusId) AS names,
    (SELECT COUNT(submissionVoteId) FROM submission_votes WHERE submissionId = s.submissionId AND type = 'mod') AS votesCount
    FROM submissions AS s 
    WHERE s.postponed = '0' AND s.done = '0' AND s.modDone = '0'{$t}
    {$s}
    LIMIT 250";
    
    if(@$get->redirToNext && $get->i)
    {
      $i = 0;
      $lastSubId = @$get->submissionId;
      $submissions = alxDatabaseManager::query($s);
      
      while($item = $submissions->fetch())
      {
        $i++;
        if($i >= $get->i && $lastSubId != $item->submissionId)
        {
          $params = getQueryStr(array
          (
            'redirToNext' => null,
            'submissionId' => $item->submissionId
          ));
          
          header('location: submissionDetail?' . $params);
          return;
        }
      }
    }
    
    $submissions = alxDatabaseManager::query($s);
    $count = alxDatabaseManager::query("SELECT COUNT(submissionId) AS c FROM submissions AS s WHERE s.postponed = '0' AND s.done = '0' AND s.modDone = '0'{$t}")->fetch();
    

    $tagCounts = array();
    $tags = alxDatabaseManager::query("SELECT tagId FROM tags");
    
    while($tag = $tags->fetch())
    {
      $tagCounts[$tag->tagId] = alxDatabaseManager::query("
        SELECT COUNT(*) AS c
        FROM submission_tags AS st, submissions AS s
        WHERE st.tagId = '{$tag->tagId}'
        AND s.submissionId = st.submissionId
        AND s.postponed = '0'
        AND s.done = '0'
        AND s.sourceMail != '{$user->mail}'
        AND s.submissionId NOT IN (SELECT submissionId FROM submission_votes WHERE userId = '{$userId}') 
        AND s.modDone = '0'
        LIMIT 1
      ")->fetch()->c;
    }
    
    $this->add('tagCounts', $tagCounts);
    
    $this->add('votesNeeded', $this->votesNeeded);
    $this->add('submissions', $submissions);
    $this->add('count', $count->c);
    $this->add('hideVoted', false);
    $this->add('noSort', true);
    
    $this->add('activeTagId', @$get->tagId);
    
    $this->render('submissions');
  }
  
  function submissionDetail($get)
  {
    if(!isMod()) return;
    
    if(!@$get->submissionId) return;
    
    $submissionId = (int) $get->submissionId;
    
    $submission = alxDatabaseManager::query("SELECT * FROM submissions WHERE submissionId = '{$submissionId}' LIMIT 1")->fetch();
    
    $nucleus = @$get->nucleusId ?: $submission->targetNucleusId;
    
    if($submission->sourceNucleusId == '0' && $submission->created <= 1354801000)
    {
      $sourceNames = array('T4G Server Tool G-Ban Import');
    }
    elseif(!$submission->sourceNucleusId)
    {
      $sourceNames = array('?');
    }
    else
    {
      $source = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$submission->sourceNucleusId}'");
      $sourceNames = array();
      
      while($item = $source->fetch())
      {
        $sourceNames[] = "{$item->name} ({$item->level})";  
      }
    }
    
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
    
    $prevSubCount = alxDatabaseManager::query("SELECT COUNT(DISTINCT s.submissionId) AS c FROM profiles as p 
    INNER JOIN submissions AS s ON p.nucleusId = s.targetNucleusId
    WHERE p.nucleusId = '{$nucleus}' AND s.created < {$submission->created}")->fetch()->c;
    
    $prevAppCount = alxDatabaseManager::query("SELECT COUNT(DISTINCT a.appealId) AS c FROM profiles as p
    INNER JOIN bans as b ON b.nucleusId = p.nucleusId
    INNER JOIN appeals as a ON a.banId = b.banId
    WHERE p.nucleusId = '{$nucleus}' AND a.created < {$submission->created}")->fetch()->c;
    
    $verified = array();
    
    for($i=0;$i<$this->votesNeeded;$i++)
    {
      $verified[] = '-';
    }
    
    $voteCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submission_votes WHERE type = 'mod' AND submissionId = '{$submission->submissionId}'")->fetch();
    $votes = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE type = 'mod' AND submissionId = '{$submission->submissionId}' ORDER BY submissionVoteId ASC");
    
    $voters = array();
    $a = 0;
    while($item = $votes->fetch())
    {
      if($submission->modDone == '1' or $submission->done == '1' or $item->userId == getUserId())
      {
        $x = '';
        if($item->vote == '1') $x = 'Yes';
        if($item->vote == '2') $x = 'No';
        if($item->vote == '3') $x = 'Delay';
        
        $verified[$a] = '<span class="vote' . ($x) . '">' . getUser($item->userId)->username . '</span>';
      }
      else
      {
        $verified[$a] = 'Hidden';
      } 
      
      $voters[] = $item->userId;
      
      $a++;
    }

    $valid = true;
    
    if(in_array(getUserId(), $voters) or ($submission->sourceMail != '' && @getUser(getUserId())->mail == $submission->sourceMail) or $submission->modDone == '1')
    {
      $valid = false;
    }

    $clanCheck = alxDatabaseManager::query("SELECT clanId FROM users INNER JOIN user_profiles AS up ON users.userId = up.userId WHERE up.nucleusId='{$submission->targetNucleusId}' LIMIT 1")->fetch();
    
    if($clanCheck && $clanCheck->clanId > 1 && $clanCheck->clanId == getUser(getUserId())->clanId)
    {
      $valid = false;
    }
    

    $submissionTags = array();
    
    $tags = alxDatabaseManager::query("SELECT tagId FROM submission_tags WHERE submissionId = '{$submission->submissionId}'");
    
    while($item = $tags->fetch())
    {
      $submissionTags[] = $item->tagId;
    }
    
    $adminVotes = array();
    $banned = false;
    
    if($submission->done == '1')
    {
      $banCheck = alxDatabaseManager::query("SELECT banId, created FROM bans WHERE submissionId = '{$submission->submissionId}' AND active = '1' LIMIT 1")->fetch();
      
      if(@$banCheck->banId > 0)
      {
        $banned = $banCheck;
      }
      
      $voteCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submission_votes WHERE type = 'admin' AND submissionId = '{$submission->submissionId}'")->fetch();
      $votes = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE type = 'admin' AND submissionId = '{$submission->submissionId}' ORDER BY submissionVoteId ASC");
  
      $a = 0;
      
      while($item = $votes->fetch())
      {
        $x = '';
        if($item->vote == '1') $x = 'Yes';
        if($item->vote == '2') $x = 'No';
        
        $adminVotes[$a] = '<span class="vote' . ($x) . '">' . getUser($item->userId)->username . '</span>';


        $a++;
      }
    }
    
    $modVoteReasons = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE submissionId = '{$submission->submissionId}' AND type = 'mod' ORDER BY submissionVoteId");
    $adminVoteReasons = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE submissionId = '{$submission->submissionId}' AND type = 'admin' ORDER BY submissionVoteId");
    
    $submissionNotes = alxDatabaseManager::fetchMultiple("SELECT * FROM submission_notes WHERE submissionId = '{$submission->submissionId}' ORDER BY submissionNoteId");
    
    foreach($submissionNotes as $sn)
    {
      $sn->sourceNames = array();
      
      if($sn->sourceNucleusId)
      {
        // only show 'main' or first soldier name
        $source = alxDatabaseManager::query("SELECT name FROM profiles AS p INNER JOIN profile_soldiers AS ps ON ps.soldierId = p.soldierId WHERE nucleusId = '{$sn->sourceNucleusId}' AND isMain = '1' LIMIT 1")->fetch();
        if(!@$source->name)
        {
          $source = alxDatabaseManager::query("SELECT name FROM profiles WHERE nucleusId = '{$sn->sourceNucleusId}' LIMIT 1")->fetch();
        }
        
        $sn->sourceNames[] = $source->name;
      }
    }
    
    $this->add('prevSubCount', $prevSubCount);
    $this->add('prevAppCount', $prevAppCount);
    
    $this->add('modVoteReasons', $modVoteReasons);
    $this->add('adminVoteReasons', $adminVoteReasons);
    $this->add('banned', $banned);
    $this->add('submission', $submission);
    $this->add('source', $sourceNames);
    $this->add('target', $targetNames);
    $this->add('targetNames', $targetNames2);
    $this->add('verified', implode(' / ', $verified));
    $this->add('adminVotes', implode(' / ', $adminVotes));
    $this->add('modVotes', count($voters));
    $this->add('valid', $valid);
    $this->add('votesNeeded', $this->votesNeeded);
    $this->add('targets', $targets);
    $this->add('targetSoldierIds', $targetSoldierIds);
    $this->add('submissionTags', $submissionTags);
    
    $this->add('submissionNotes', $submissionNotes);
    
    $this->render();
  }
  
  function scoreStats($get)
  {
    if(!isMod()) return;
    
    $target = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$get->nucleusId}'");
    $targets = array();
    
    while($item = $target->fetch())
    {
      $targets[] = $item;
    }
    
    $this->add('targets', $targets);
    $this->add('statsTS', $get->statsTS);
    
    $this->respond();
  }
  
  function setSubmissionState($get)
  {
    if(!isMod()) return;
    
    if(!@$get->submissionId or !@$get->state or !@$get->msg) return;
    
    $result = array
    (
      'success' => true
    );
    
    $id = getUserId();
    $now = time();
    $submissionId = (int) $get->submissionId;
    $vote = (int) $get->state;
    
    $votedCheck = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE submissionId = '{$submissionId}' AND type = 'mod' AND userId = '{$id}' LIMIT 1")->fetch();
    
    if(@$votedCheck->submissionVoteId)
    {
      return;
    }
    
    $msg = mysql_real_escape_string(prepare($get->msg));
    
    $submission = alxDatabaseManager::query("SELECT * FROM submissions WHERE submissionId = '{$submissionId}' LIMIT 1")->fetch();
    
    $submissionTags = array();
    
    $tags = alxDatabaseManager::query("SELECT tagId FROM submission_tags WHERE submissionId = '{$submission->submissionId}'");
    
    while($item = $tags->fetch())
    {
      $submissionTags[] = $item->tagId;
    }
    
    alxDatabaseManager::query("INSERT INTO submission_votes SET type = 'mod', submissionId = '{$submissionId}', userId = '{$id}', date = '{$now}', vote = '{$vote}', message = '{$msg}'");
    
    
    $votes = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE submissionId = '{$submissionId}' AND type = 'mod'");
    
    $other = 0;
    $no = 0;
    $yes = 0;
    $yes_switch = 0;
    $totalVotes = 0;
    
    while($item = $votes->fetch())
    {
      if($item->vote == '1')
        $yes++;
      elseif($item->vote == '2')
        $no++;
      elseif($item->vote == '4')
        $yes_switch++;
      else
        $other++;
      $totalVotes++;
    }
    
    if($totalVotes >= $this->votesNeeded)
    { // Mark submission done on mod side if votes >= votesNeeded
      alxDatabaseManager::query("UPDATE submissions SET modDone = '1' WHERE submissionId = '{$submission->submissionId}' LIMIT 1");
    }
    
    if(in_array('1', $submissionTags) && ($yes+$yes_switch) >= $this->votesNeeded_ggc)
    { // Mark submission done on mod side if submission has 'Existing GGC/PBBans' tag and there are enough 'yes' votes
    
      if($submission->type != 'ch')
      {
        addLog('mod', 'setSubmissionType_' . 'ch', $submissionId);
        alxDatabaseManager::query("UPDATE submissions SET type = 'ch' WHERE submissionId = '{$submission->submissionId}' LIMIT 1");
      }
      
      alxDatabaseManager::query("UPDATE submissions SET modDone = '1' WHERE submissionId = '{$submission->submissionId}' LIMIT 1");
    }
    
    if($no == $totalVotes && $no >= $this->votesNeeded)
    { // Mark submission invalid if all mods vote to not ban
      addLog('mod', 'setFinalSubmissionState_Invalid', $submission->submissionId);
      
      alxDatabaseManager::query("UPDATE submissions SET done = '1' WHERE submissionId = '{$submission->submissionId}' LIMIT 1");
    }
    
    
    addLog('mod', 'setSubmissionState_' . ($vote == '1' ? 'Valid' : 'Invalid'), $submission->submissionId);

    $this->respondString(json_encode($result));  
  }
  
  function setSubmissionTags($get)
  {  
    if(!isMod()) return;
    
    if(!@$get->submissionId or !isset($get->tagId)) return;
    
    $userId = getUserId();
    
    $result = array
    (
      'success' => true
    );
    
    $tagId = (int) $get->tagId;
    $state = $get->state;
    
    alxDatabaseManager::query("DELETE FROM submission_tags WHERE submissionId = '{$get->submissionId}' AND tagId = '{$tagId}'");
    
    if($state == '1')
    {
      alxDatabaseManager::query("INSERT INTO submission_tags SET submissionId = '{$get->submissionId}', tagId = '{$tagId}', userId = '{$userId}'");
    }
    
    addLog('mod', 'updateSubmissionTag_' . $tagId, $get->submissionId);

    $this->respondString(json_encode($result)); 
  }
  
  function addSubmissionNote($get)
  {  
    if(!isMod()) return;
    
    if(!@$get->submissionId or !@$get->note) return;
    
    $userId = getUserId();
    
    $result = array
    (
      'success' => true
    );
    
    $note = mysql_real_escape_string($get->note);
    $now = time();
    
    alxDatabaseManager::query("INSERT INTO submission_notes SET submissionId = '{$get->submissionId}', date = '{$now}', userId = '{$userId}', note = '{$note}'");
    
    // addLog('mod', 'addSubmissionNote', $get->submissionId);

    $this->respondString(json_encode($result)); 
  }
  
  function setAppealState($get)
  {
    if(!isMod()) return;
    
    if(!@$get->appealId or !@$get->state or !@$get->msg) return;
    
    $result = array
    (
      'success' => true
    );
    
    $id = getUserId();
    $now = time();
    $appealId = (int) $get->appealId;
    $vote = (int) $get->state;
    
    $msg = mysql_real_escape_string(prepare($get->msg));
    
    $appeal = alxDatabaseManager::query("SELECT * FROM appeals WHERE appealId = '{$appealId}' LIMIT 1")->fetch();
    
    alxDatabaseManager::query("INSERT INTO appeal_votes SET type = 'mod', appealId = '{$appealId}', userId = '{$id}', date = '{$now}', vote = '{$vote}', message = '{$msg}'");
    
    addLog('mod', 'setAppealState_' . ($vote == '1' ? 'Valid' : 'Invalid'), $appeal->appealId);

    $this->respondString(json_encode($result));  
  }
  
  function appeals($get)
  {
    if(!isMod()) return;
    
    $hideVoted = false;
    if(@$get->hideVoted == '1' or @$get->hideVoted == 'true')
    {
      $hideVoted = true;
    }
    
    $done = @$get->userId ? 1 : 0;
    
    $appeals = alxDatabaseManager::query("SELECT * FROM appeals WHERE done = '{$done}' ORDER BY created ASC");
    $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM appeals WHERE done = '{$done}'")->fetch();
    
    $this->add('userId', @$get->userId ?: false);
    $this->add('vote', @$get->vote ?: false);
    $this->add('appeals', $appeals);
    $this->add('count', $count->c);
    $this->add('votesNeeded', $this->votesNeeded);
    $this->add('hideVoted', $hideVoted);
    
    $this->render();
  }
  
  function myVotedAppeals($get)
  {
    if(!isMod()) return;
    
    $userId = getUserId();
    
    $w = "WHERE av.userId = '{$userId}' AND a.appealId = av.appealId";
    
    $appeals = alxDatabaseManager::query("SELECT * FROM appeals as a, appeal_votes as av {$w} ORDER BY a.created DESC LIMIT 250");
    $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM appeals as a, appeal_votes as av {$w}")->fetch();
    
    $this->add('userId', false);
    $this->add('vote', false);
    $this->add('appeals', $appeals);
    $this->add('count', $count->c);
    $this->add('votesNeeded', $this->votesNeeded);
    $this->add('hideVoted', false);
    $this->add('noContinue', true);
    
    $this->render('appeals');
  }
  
  function appealDetail($get)
  {
    if(!isMod()) return;
    
    if(!@$get->appealId) return;
    
    $appeal = alxDatabaseManager::query("SELECT * FROM appeals WHERE appealId = '{$get->appealId}' LIMIT 1")->fetch();
    
    $ban = alxDatabaseManager::query("SELECT * FROM bans WHERE banId = '{$appeal->banId}' LIMIT 1")->fetch();
    
    $submissionId = (int) $ban->submissionId;
    
    $submission = alxDatabaseManager::query("SELECT * FROM submissions WHERE submissionId = '{$submissionId}' LIMIT 1")->fetch();
    
    if(empty($submission->sourceNucleusId) && $submission->created <= 1354801000)
    {
      $sourceNames = array('G-Ban Import');
    }
    elseif(!$submission->sourceNucleusId)
    {
      $sourceNames = array('?');
    }
    else
    {
      $sourceNames = array();
    
      $source = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$submission->sourceNucleusId}'");
    
      while($item = $source->fetch())
      {
        $sourceNames[] = "{$item->name} ({$item->level})";  
      }
    }

    $target = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$submission->targetNucleusId}'");
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
    
    $prevSubCount = alxDatabaseManager::query("SELECT COUNT(DISTINCT s.submissionId) AS c FROM profiles as p 
    INNER JOIN submissions AS s ON p.nucleusId = s.targetNucleusId
    WHERE p.nucleusId = '{$submission->targetNucleusId}' AND s.created < {$submission->created}")->fetch()->c;
    
    $prevAppCount = alxDatabaseManager::query("SELECT COUNT(DISTINCT a.appealId) AS c FROM profiles as p
    INNER JOIN bans as b ON b.nucleusId = p.nucleusId
    INNER JOIN appeals as a ON a.banId = b.banId
    WHERE p.nucleusId = '{$submission->targetNucleusId}' AND a.created < {$appeal->created}")->fetch()->c;
    
    $verified = array();
    
    for($i=0;$i<$this->votesNeeded;$i++)
    {
      $verified[] = '-';
    }
    
    $voteCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submission_votes WHERE type = 'mod' AND submissionId = '{$submission->submissionId}'")->fetch();
    $votes = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE type = 'mod' AND submissionId = '{$submission->submissionId}' ORDER BY submissionVoteId ASC");
    
    $voters = array();
    $a = 0;
    while($item = $votes->fetch())
    {
      if($submission->modDone == '1' or $submission->done == '1' or $item->userId == getUserId())
      {
        $x = '';
        if($item->vote == '1') $x = 'Yes';
        if($item->vote == '2') $x = 'No';
        if($item->vote == '3') $x = 'Delay';
        
        $verified[$a] = '<span class="vote' . ($x) . '">' . getUser($item->userId)->username . '</span>';
      }
      else
      {
        $verified[$a] = 'Hidden';
      } 
      
      $voters[] = $item->userId;
      
      $a++;
    }
    
    $appealVoteCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM appeal_votes WHERE type = 'mod' AND appealId = '{$appeal->appealId}'")->fetch();
    $appealVotes = alxDatabaseManager::query("SELECT * FROM appeal_votes WHERE type = 'mod' AND appealId = '{$appeal->appealId}' ORDER BY appealVoteId ASC");
    
    $appealVerified = array();
    
    for($i=0;$i<$this->votesNeeded;$i++)
    {
      $appealVerified[$i] = '-';
    }
    
    $appealVoters = array();
    $a = 0;
    while($item = $appealVotes->fetch())
    {
      if($appealVoteCount->c >= $this->votesNeeded or $item->userId == getUserId())
      {
        $x = '';
        if($item->vote == '1') $x = 'Yes';
        if($item->vote == '2') $x = 'No';
        if($item->vote == '3') $x = 'Delay';
        
        $appealVerified[$a] = '<span class="vote' . ($x) . '">' . getUser($item->userId)->username . '</span>';
      }
      else
      {
        $appealVerified[$a] = 'Hidden';
      } 
      
      $appealVoters[] = $item->userId;
      
      $a++;
    }
    
    $appealAdminVoteCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM appeal_votes WHERE type = 'admin' AND appealId = '{$appeal->appealId}'")->fetch();
    $appealAdminVotes = alxDatabaseManager::query("SELECT * FROM appeal_votes WHERE type = 'admin' AND appealId = '{$appeal->appealId}' ORDER BY appealVoteId ASC");
    
    $appealAdminVerified = array();
    
    for($i=0;$i<$this->adminVotesNeeded;$i++)
    {
      $appealAdminVerified[$i] = '-';
    }
    
    $appealAdminVoters = array();
    $a = 0;
    while($item = $appealAdminVotes->fetch())
    {
      if($appealAdminVoteCount->c >= $this->adminVotesNeeded or $item->userId == getUserId() or $appeal->done == '1')
      {
        $x = '';
        if($item->vote == '1') $x = 'Yes';
        if($item->vote == '2') $x = 'No';
        if($item->vote == '3') $x = 'Delay';
        
        $appealAdminVerified[$a] = '<span class="vote' . ($x) . '">' . getUser($item->userId)->username . '</span>';
      }
      else
      {
        $appealAdminVerified[$a] = 'Hidden';
      } 
      
      $appealAdminVoters[] = $item->userId;
      
      $a++;
    }

    $valid = true;
    
    if(in_array(getUserId(), $appealVoters) or in_array(getUserId(), $voters) or ($submission->sourceMail != '' && @getUser(getUserId())->mail == $submission->sourceMail) or $appealVoteCount->c >= $this->votesNeeded)
    {
      $valid = false;
    }
    
    $clanCheck = alxDatabaseManager::query("SELECT clanId FROM users INNER JOIN user_profiles AS up ON users.userId = up.userId WHERE up.nucleusId='{$submission->targetNucleusId}' LIMIT 1")->fetch();
    
    if($clanCheck && $clanCheck->clanId > 1 && $clanCheck->clanId == getUser(getUserId())->clanId)
    {
      $valid = false;
    }
    
    $submissionTags = array();
    
    $tags = alxDatabaseManager::query("SELECT tagId FROM submission_tags WHERE submissionId = '{$submission->submissionId}'");
    
    while($item = $tags->fetch())
    {
      $submissionTags[] = $item->tagId;
    }
    
    $adminVotes = array();
    $banned = false;
    
    for($i=0;$i<$this->adminVotesNeeded;$i++)
    {
      $adminVotes[$i] = '-';
    }
    
    if($submission->done == '1')
    {
      $banCheck = alxDatabaseManager::query("SELECT banId FROM bans WHERE submissionId = '{$submission->submissionId}' AND active = '1' LIMIT 1")->fetch();
      
      if(@$banCheck->banId > 0)
      {
        $banned = true;
      }
      
      $voteCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submission_votes WHERE type = 'admin' AND submissionId = '{$submission->submissionId}'")->fetch();
      $votes = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE type = 'admin' AND submissionId = '{$submission->submissionId}' ORDER BY submissionVoteId ASC");
  
      $a = 0;
      
      while($item = $votes->fetch())
      {
        $x = '';
        if($item->vote == '1') $x = 'Yes';
        if($item->vote == '2') $x = 'No';
        
        $adminVotes[$a] = '<span class="vote' . ($x) . '">' . getUser($item->userId)->username . '</span>';


        $a++;
      }
    }
    
    $modVoteReasons = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE submissionId = '{$submission->submissionId}' AND type = 'mod' ORDER BY submissionVoteId ASC");
    $adminVoteReasons = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE submissionId = '{$submission->submissionId}' AND type = 'admin' ORDER BY submissionVoteId ASC");
    
    $appealModVoteReasons = alxDatabaseManager::query("SELECT * FROM appeal_votes WHERE appealId = '{$appeal->appealId}' AND type = 'mod' ORDER BY appealVoteId ASC");
    $appealAdminVoteReasons = alxDatabaseManager::query("SELECT * FROM appeal_votes WHERE appealId = '{$appeal->appealId}' AND type = 'admin' ORDER BY appealVoteId ASC");
    
    $submissionNotes = alxDatabaseManager::fetchMultiple("SELECT * FROM submission_notes WHERE submissionId = '{$submission->submissionId}' ORDER BY submissionNoteId");
    
    foreach($submissionNotes as $sn)
    {
      $sn->sourceNames = array();
      
      if($sn->sourceNucleusId)
      {
        // only show 'main' or first soldier name
        $source = alxDatabaseManager::query("SELECT name FROM profiles AS p INNER JOIN profile_soldiers AS ps ON ps.soldierId = p.soldierId WHERE nucleusId = '{$sn->sourceNucleusId}' AND isMain = '1' LIMIT 1")->fetch();
        if(!@$source->name)
        {
          $source = alxDatabaseManager::query("SELECT name FROM profiles WHERE nucleusId = '{$sn->sourceNucleusId}' LIMIT 1")->fetch();
        }
        
        $sn->sourceNames[] = $source->name;
      }
    }
    
    $this->add('submission', $submission);
    $this->add('appeal', $appeal);
    $this->add('ban', $ban);
    $this->add('source', $sourceNames);
    $this->add('verified', implode(' / ', $verified));
    $this->add('appealVerified', implode(' / ', $appealVerified));
    $this->add('appealAdminVotes', implode(' / ', $appealAdminVerified));
    $this->add('valid', $valid);
    
    $this->add('submissionTags', $submissionTags);
    $this->add('submissionNotes', $submissionNotes);
    
    $this->add('prevSubCount', $prevSubCount);
    $this->add('prevAppCount', $prevAppCount);
    
    $this->add('modVoteReasons', $modVoteReasons);
    $this->add('adminVoteReasons', $adminVoteReasons);
    $this->add('appealModVoteReasons', $appealModVoteReasons);
    $this->add('appealAdminVoteReasons', $appealAdminVoteReasons);
    $this->add('banned', $banned);
    $this->add('target', $targetNames);
    $this->add('targetNames', $targetNames2);
    $this->add('adminVotes', implode(' / ', $adminVotes));
    $this->add('modVotes', count($voters));
    $this->add('appealModVotes', count($appealVoters));
    $this->add('votesNeeded', $this->votesNeeded);
    $this->add('targets', $targets);
    $this->add('targetSoldierIds', $targetSoldierIds);
    
    $this->render();
  }

  function validateImportedBan($get)
  {
    if(!@$get->appealId) return;
    
    $result = array
    (
      'success' => true
    );
    
    $appeal = alxDatabaseManager::query("SELECT * FROM appeals WHERE appealId = '{$get->appealId}' LIMIT 1")->fetch();
    $ban = alxDatabaseManager::query("SELECT * FROM bans WHERE banId = '{$appeal->banId}' LIMIT 1")->fetch();
    $submission = alxDatabaseManager::query("SELECT * FROM submissions WHERE submissionId = '{$ban->submissionId}' LIMIT 1")->fetch();
    
    $created = time();
    $targetNucleusId = $ban->nucleusId;
    $type = $GLOBALS['idTypes'][$ban->blacklistId];
    $msg = "Automatic Re-Submission from lifted Imported G-Ban List Entry.\n\n";
    $msg.= "Original Import Reason: <div class=\"appeal\">" . $submission->msg . "</div><br /><br />\n\n";
    $msg.= "Original Appeal: <div class=\"appeal\">" . $appeal->appeal . "</div>\n\n";
    
    $msg = mysql_real_escape_string($msg);
    
    if($targetNucleusId <= 0)
    {
      return;
    }
    
    alxDatabaseManager::query("INSERT INTO submissions SET created = '{$created}', sourceNucleusId = '1', sourceMail = '', targetNucleusId = '{$targetNucleusId}', type = '{$type}', msg = '{$msg}', done = '0', postponed = '0'");
    
    sleep(1);
    
    alxDatabaseManager::query("DELETE FROM bans WHERE banId = '{$appeal->banId}' LIMIT 1");
    
    alxDatabaseManager::query("UPDATE appeals SET done = '1' WHERE appealId = '{$appeal->appealId}' LIMIT 1");
    
    $to = $appeal->mail;
    $subject = "T4G Blacklist -> Your Appeal (#{$appeal->appealId})";
    
    $msg = "Hello {$to}<br /><br />\n\nYour Appeal has been Processed, since it's an Imported Ban our Policy is to lift the Ban immediately and automatically re-submit the Case to ensure the Evidence is up to our standard, and re-verify the Case.";
    $msg.= "<br /><br />\n\nAs soon as the automatic re-submission is processed, your Ban either stays lifted, or it will get added again. You can check this <a href=\"http://blacklist.tools4games.com/en/search\">here</a>";
    $msg.= "<br /><br /><br />\n\nYour T4G Blacklist Team";
    
    sendMail($to, $subject, $msg);
    // sendMail('blacklist-log@tools4games.com', $subject, $msg);
    sendMail('blacklist-log@t4g-blacklist.net', $subject, $msg);
    
    addLog('mod', 'processImportedAppeal', $appeal->appealId);
    addLog('mod', 'deletedBanFromImportedAppeal', $appeal->appealId);
    
    $this->respondString(json_encode($result));
  }

  function stats($get)
  {
    if(!isMod() && !isAdmin()) return;
    
    if(@$get->users == 'admins')
    {
      $label = 'Admins';
      $type = 'admin';
      $moderators = alxDatabaseManager::query("SELECT * FROM users WHERE `mod` = '1' AND admin = '1' ORDER BY username ASC");
      $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM users WHERE `mod` = '1' AND admin = '1'")->fetch();
    }
    else
    {
      $label = 'Moderators';
      $type = 'mod';
      $moderators = alxDatabaseManager::query("SELECT * FROM users WHERE `mod` = '1' AND admin = '0' ORDER BY username ASC");
      $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM users WHERE `mod` = '1' AND admin = '0'")->fetch();
    }
    
    $this->add('label', $label);
    $this->add('type', $type);
    $this->add('count', $count->c);
    $this->add('moderators', $moderators);
    
    $this->render();
  }

  function statCharts($get)
  {
    if(!isMod() && !isAdmin()) return;
    
    if(@$get->users == 'admins')
    {
      $label = 'Admins';
      $type = 'admin';
      $moderators = alxDatabaseManager::query("SELECT * FROM users WHERE `mod` = '1' AND admin = '1' ORDER BY username ASC");
      $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM users WHERE `mod` = '1' AND admin = '1'")->fetch();
    }
    else
    {
      $label = 'Moderators';
      $type = 'mod';
      $moderators = alxDatabaseManager::query("SELECT * FROM users WHERE `mod` = '1' AND admin = '0' ORDER BY username ASC");
      $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM users WHERE `mod` = '1' AND admin = '0'")->fetch();
    }
    
    $this->add('label', $label);
    $this->add('type', $type);
    $this->add('count', $count->c);
    $this->add('moderators', $moderators);
    
    $this->render();
  }

  function blStatCharts($get)
  {
    if(!isMod() && !isAdmin()) return;
    
    $this->render();
  }

  // a search page for mods/admins to find existing submissions
  function search($get)
  {
    if(!isMod() && !isAdmin()) return;
    
    $this->render();
  }
  
  function searchResult($get)
  {
    if(!@$get->name) return;
    
    header('content-type: text/plain');
    
    $result = array
    (
      'success' => true,
      'data' => array()
    );
    
    $s = mysql_real_escape_string($get->name);
    
    if(is_numeric($s) && $s > 2000000000)
    {
      $w = " p.nucleusId = '{$s}'";
    }
    else
    {
      $pos = strpos($s, "*");
      $w = ($pos === false) ? " p.name = '{$s}'" : " p.name LIKE '" . str_replace("*", "%", $s) . "'";
    }
    
    
    $p = 1; // alxDatabaseManager::query("SELECT name, nucleusId FROM profiles WHERE{$w} LIMIT 1")->fetch();
    
    if($p)
    {
      $search = alxDatabaseManager::query("SELECT p.name, s.submissionId, s.created, s.type, s.done FROM profiles as p 
      INNER JOIN submissions AS s ON p.nucleusId = s.targetNucleusId
      WHERE {$w} GROUP BY s.submissionId ORDER BY s.created DESC LIMIT 75");
      
      $search2 = alxDatabaseManager::query("SELECT p.name, a.appealId, a.created, b.blacklistId, a.done FROM profiles as p
      INNER JOIN bans as b ON b.nucleusId = p.nucleusId
      INNER JOIN appeals as a ON a.banId = b.banId
      WHERE {$w} GROUP BY a.appealId ORDER BY a.created DESC LIMIT 75");
      
      while($item = $search2->fetch())
      {
        $s = new stdClass;
        $s->type = 'Appeal';
        $s->created = $item->created;
        $s->date = date('d.m.Y H:i', $item->created);
        $s->label = $GLOBALS['types'][$GLOBALS['idTypes'][$item->blacklistId]];
        $s->done = $item->done;
        $s->id = $item->appealId;
        
        $s->name = $item->name;
        $result['data'][] = $s;
      }
      
      while($item = $search->fetch())
      {
        $s = new stdClass;
        $s->type = 'Submission';
        $s->created = $item->created;
        $s->date = date('d.m.Y H:i', $item->created);
        $s->label = $GLOBALS['types'][$item->type];
        $s->done = $item->done;
        $s->id = $item->submissionId;
        
        $s->name = $item->name;
        $result['data'][] = $s;
      }
      
      usort($result['data'], function($a, $b){
        $a = $a->created;
        $b = $b->created;
        return ($a == $b) ? 0 : ($a<$b ? 1 : -1);
      });
    }
    
    $r = json_encode($result);
    
    if(@$get->callback)
    {
      $r = $get->callback . "({$r});";
    }
    
    $this->respondString($r);
  }
}
