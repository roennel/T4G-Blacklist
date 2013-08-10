<?php

// Forum's PHPBB API
/*
global $phpbb_root_path, $phpEx;
global $db, $user, $auth, $cache, $config;

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '/var/www/forum.tools4games.com/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include_once($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
*/

require_once '/var/www/t4g_blacklist/lib/phpbb_api.php';

$user->session_begin(false);
$user->session_create(2237, false, false, false);

class AdminPanelController extends alxController
{
  public $votesNeeded = 3;
  public $modVotesNeeded = 5;
  
  function index()
  {
    $this->render();
  }
  
  function badSubmitters($get)
  {
    $items = alxDatabaseManager::fetchMultiple
    ("
      SELECT p.name FROM profiles AS p, bad_submitters AS bs WHERE bs.nucleusId = p.nucleusId
    ");
    
    $this->add('items', $items);
    
    $this->render();
  }
  
  function addUserVote($get)
  {
    if(!isMod() or (!isAdmin() && getUserId() != 5 && getUserId() != 8)) return;
    
    $this->render();
  }
  
  function addUserVoteDo($get)
  {
    if(!isMod() or (!isAdmin() && getUserId() != 5 && getUserId() != 8)) return;
    
    $result = array
    (
      'success' => false
    );
    
    $userId = mysql_real_escape_string($get->userId);
    $type = mysql_real_escape_string($get->type);
    $score = mysql_real_escape_string($get->score);
    $voteId = mysql_real_escape_string($get->voteId);
    $link = mysql_real_escape_string($get->link);
    $reason = mysql_real_escape_string($get->reason);
    $notify = @$get->notify ? true : false;
    
    $authorUserId = getUserId();
    $date = time();
    
    $check = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM user_votes WHERE link = '{$link}'" . ($voteId ? " OR voteId = '{$voteId}'" : ""))->fetch();
    
    if($check->c > 0)
    {
      $ins = false;
    }
    else
    {
      $ins = alxDatabaseManager::query
      ("
        INSERT INTO user_votes 
        SET userId = '{$userId}', authorUserId = '{$authorUserId}', 
        date = '{$date}', type = '{$type}', `voteId` = '{$voteId}', `link` = '{$link}',
        reason = '{$reason}', score = '{$score}'
      ");
      
      if($notify)
      {
        $userVote = alxDatabaseManager::query
        ("
          SELECT userVoteId FROM user_votes 
          WHERE userId = '{$userId}' AND authorUserId = '{$authorUserId}'
          AND date = '{$date}' AND `voteId` = '{$voteId}' AND `link` = '{$link}'
        ")->fetch();
        
        alxDatabaseManager::query
        ("
          INSERT INTO user_notifications
          SET userId = '{$userId}', userVoteId = '{$userVote->userVoteId}', 
          `read` = '0'
        ");
      }
    }
    
    if($ins)
    {
      $result['success'] = true;
    }
    
    $this->respondString(json_encode($result));
  }
  
  function userVoting($get)
  {
    if(!isMod() or (!isAdmin() && getUserId() != 5 && getUserId() != 8)) return;
    
    if(@$get->type == 'ranking')
    {
      $ranking = alxDatabaseManager::fetchMultiple
      ("
        SELECT userId, SUM(score) AS score FROM user_votes GROUP BY userId ORDER BY score DESC 
      ");
      
      $this->add('ranking', $ranking);
    }
    else
    {
      $items = alxDatabaseManager::query("SELECT * FROM user_votes ORDER BY date DESC");
    
      $this->add('items', $items);
    }
    
    $this->render();
  }
  
  function notifications($get)
  {
    if(!isMod()) return;
    
    $userId = getUserId();
    
    $groups = array();
    
    $groups['Unread'] = alxDatabaseManager::query("
      SELECT *
      FROM user_notifications AS un
      INNER JOIN user_votes AS uv ON uv.userVoteId = un.userVoteId
      WHERE un.userId = '{$userId}' AND `read` = '0'
      ORDER BY date DESC
    ");
    
    $groups['Read'] = alxDatabaseManager::query("
      SELECT *
      FROM user_notifications AS un
      INNER JOIN user_votes AS uv ON uv.userVoteId = un.userVoteId
      WHERE un.userId = '{$userId}' AND `read` = '1'
      ORDER BY date DESC
    ");
    
    $myNotifications = alxDatabaseManager::query("
      SELECT *
      FROM user_notifications AS un
      INNER JOIN user_votes AS uv ON uv.userVoteId = un.userVoteId
      WHERE uv.authorUserId = '{$userId}'
      ORDER BY date DESC
    ");
    
    $this->add('groups', $groups);
    $this->add('myNotifications', $myNotifications);
    
    $this->render();
  }
  
  function markNotificationRead($get)
  {
    if(!isMod()) return;
    
    if(!@$get->userId or !@$get->notificationId) return;
    
    $result = array
    (
      'success' => false
    );
    
    $userId = (int) $get->userId;
    $notificationId = (int) $get->notificationId;
    $status = @$get->setUnread == '1' ? '0' : '1';
    
    if(getUserId() != $userId) return;
    
    $upd = alxDatabaseManager::query("
      UPDATE user_notifications
      SET `read` = '{$status}'
      WHERE userId = '{$userId}' AND userNotificationId = '{$notificationId}'
    ");
    
    if($upd)
    {
      $result['success'] = true;
    }
    
    $this->respondString(json_encode($result));
  }
  
  function userKickHistory($get)
  {
    if(@$get->banId)
    {
    $banId = (int) $get->banId;
    
    $ban = alxDatabaseManager::query("SELECT * FROM bans WHERE banId = '{$banId}' LIMIT 1")->fetch();
    
    $kicks = alxDatabaseManager::query("SELECT * FROM kickLog WHERE banId = '{$banId}' ORDER BY date DESC");
    
    $_user = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$ban->nucleusId}'");
    }
    
    if(@$get->nucleusId)
    {
      $nucleusId = (int) $get->nucleusId;
      
      $kicks = alxDatabaseManager::query("SELECT * FROM kickLog WHERE nucleusId = '{$nucleusId}' ORDER BY date DESC");
    
      $_user = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$nucleusId}'");
    }
    
    $users = array();
    
    while($user = $_user->fetch())
    {
      $users[] = $user->name;
    }
    
    $this->add('user', implode(' / ', $users));
    $this->add('kicks', $kicks);
    
    $this->render();
  }
  
  function mostKickedUsers($get)
  {
    $bans = alxDatabaseManager::query("SELECT COUNT(kickLogId) AS c, banId FROM `kickLog` GROUP BY banId ORDER BY c DESC LIMIT 100");
    
    $this->add('bans', $bans);
    
    $this->render();
  }
  
  function topSubmitters($get)
  {
    $sub = alxDatabaseManager::query("SELECT COUNT(submissionId) AS c, submissionId, sourceNucleusId FROM submissions WHERE sourceNucleusId > 1 GROUP BY sourceNucleusId ORDER BY c DESC LIMIT 50");

    $this->add('sub', $sub);
    
    $this->render();
  }
  
  function worstSubmitters($get)
  {
    $sub = alxDatabaseManager::query("SELECT COUNT(submissionId) AS c, submissionId, sourceNucleusId FROM submissions WHERE sourceNucleusId > 1 GROUP BY sourceNucleusId HAVING COUNT(submissionId) > 30 ORDER BY c ASC LIMIT 50");

    $this->add('sub', $sub);
    
    $this->render('topSubmitters');
  }
  
  function doneSubmissions($get)
  {
    if(!isAdmin() && !isMod()) return;
    
    if(isset($get->recent))
    { // NOTE: This will only show items that have an associated setFinalXXState log entry
      $submissions = alxDatabaseManager::query("SELECT s.*, log.date FROM submissions AS s INNER JOIN log ON log.value = s.submissionId AND log.action IN ('setFinalSubmissionState_Valid', 'setFinalSubmissionState_Invalid', 'markedSubmissionInvalid') WHERE s.done = '1' GROUP BY s.submissionId ORDER BY date DESC LIMIT 200");
      $this->add('showDoneDate', true);
    }
    else
    {
      $submissions = alxDatabaseManager::query("SELECT * FROM submissions WHERE done = '1' ORDER BY submissionId DESC LIMIT 200");
    }
    $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submissions WHERE done = '1'")->fetch();
    $countImport = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM bans WHERE created <= '1354801000'")->fetch();
    
    
    $countValid = alxDatabaseManager::query("SELECT COUNT(b.banId) AS c FROM submissions AS s, bans AS b WHERE b.created > '1354801000' AND b.submissionId = s.submissionId")->fetch();
    
    $t = $count->c - $countImport->c;
    
    $this->add('submissions', $submissions);
    $this->add('count', $count->c);
    $this->add('countImport', $countImport->c);
    $this->add('countValid', $countValid->c);
    $this->add('countInvalid', $t - $countValid->c);
    
    $this->render();
  }
  
  function doneAppeals($get)
  {
    if(!isAdmin() && !isMod()) return;

    if(isset($get->recent))
    { // NOTE: This will only show items that have an associated setFinalXXState log entry
      $appeals = alxDatabaseManager::query("SELECT a.*, log.date FROM appeals AS a INNER JOIN log ON log.value = a.appealId AND log.action IN ('setFinalAppealState_Valid', 'setFinalAppealState_Invalid') WHERE a.done = '1' GROUP BY a.appealId ORDER BY date DESC LIMIT 200");
      $this->add('showDoneDate', true);
    }
    else
    {
      $appeals = alxDatabaseManager::query("SELECT * FROM appeals WHERE done = '1' ORDER BY appealId DESC");
    }
    $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM appeals WHERE done = '1'")->fetch();
    
    $t = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM appeals WHERE done = '1'")->fetch()->c;
    
    $countValid = alxDatabaseManager::query("SELECT COUNT(DISTINCT b.banId) AS c FROM appeals AS a, bans AS b WHERE a.done = '1' AND a.banId = b.banId AND b.active = '0'")->fetch();
    
    
    $this->add('appeals', $appeals);
    $this->add('count', $count->c);
    $this->add('countValid', $countValid->c);
    $this->add('countInvalid', $t - $countValid->c);
    
    $this->render();
  }
  
  function submissions($get)
  {
    if(!isAdmin()) return;
    
    $userId = getUserId();
    $user = getUser($userId);
    
    $pp = time();
    
    $c2 = '';
    $q2 = " AND (s.postponed != '0' AND s.postponed > '{$pp}')";
    
    if(!@$get->showDelayed)
    {
      $c2 = " AND (s.postponed = '0' OR s.postponed <= '{$pp}')";
    }
    
    $limit = 9999999;
    $sort = ' ORDER BY created';
    // $sort = ' ORDER BY votesCount DESC, created';
    
    $query = '';
    $join = '';
    $join_profileStats = true;
    
    if(@$get->hideVoted == '1')
    {
      $query .= "
        AND s.submissionId NOT IN (SELECT submissionId FROM submission_votes WHERE userId = '{$userId}') 
        AND s.sourceMail != '{$user->mail}'
      ";
      $limit = 250;
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
    }
    
    if(@$get->tagged)
    {
      if($get->tagged == 'false')
        $query .= " AND s.submissionId NOT IN (SELECT submissionId FROM submission_tags)";
      else
        $query .= " AND s.submissionId IN (SELECT submissionId FROM submission_tags)";
    }
    
    if(@$get->minVotes)
    {
      $minVotes = mysql_real_escape_string($get->minVotes);
      
      $query .= " AND (SELECT COUNT(submissionVoteId) FROM submission_votes WHERE submissionId = s.submissionId AND type = 'admin') >= '{$minVotes}'";
    }
    
    if(@$get->minHSRatio)
    {
      $hsRatio = mysql_real_escape_string($get->minHSRatio);
      
      $join_profileStats = true;
      $query .= " AND p.kit != '0' AND ps.kills > '200' AND ps.headshotratio >= '{$hsRatio}'";
      $limit = 50;
    }
    
    if(@$get->minBestScore)
    {
      $bestScore = mysql_real_escape_string($get->minBestScore);
      
      $join_profileStats = true;
      $query .= " AND ps.bestScore >= '{$bestScore}'";
      $limit = 50;
    }
    
    if(@$get->sort)
    {
      $sort = $get->sort;
      $sortOrder = "ASC";
      
      if(substr($sort, 0, 1) == "-")
      {
        $sort = substr($sort, 1);
        $sortOrder = "DESC";
      }
      
      $sort = " ORDER BY " . mysql_real_escape_string($sort) . " " . $sortOrder;
    }
    
    if($join_profileStats)
    {
      $join .= " LEFT JOIN profile_stats AS ps ON p.soldierId = ps.soldierId";
    }
    
    if(!@$get->showModSubs)
    {
      $query = " AND s.modDone = '1'" . $query;
    }
    
    $s = "
    SELECT s.submissionId, s.created, s.sourceNucleusId, s.sourceMail, s.targetNucleusId, s.type, s.msg,
    GROUP_CONCAT(DISTINCT p.name SEPARATOR ' / ') AS names,
    COUNT(DISTINCT(IF(sv.type='mod', sv.submissionVoteId, NULL))) AS modVotesCount, COUNT(DISTINCT(IF(sv.type='admin', sv.submissionVoteId, NULL))) AS votesCount,
    COUNT(DISTINCT(IF(sv.type='mod' AND sv.vote='1', sv.submissionVoteId, NULL))) AS modVotesYes, COUNT(DISTINCT(IF(sv.type='mod' AND sv.vote='2', sv.submissionVoteId, NULL))) AS modVotesNo
    FROM submissions AS s 
    LEFT JOIN profiles AS p ON s.targetNucleusId = p.nucleusId
    {$join}
    LEFT JOIN submission_votes AS sv ON s.submissionId = sv.submissionId
    WHERE s.done = '0'{$query}{$c2} 
    AND s.submissionId NOT IN (SELECT submissionId FROM submission_votes WHERE userId = '{$userId}') 
    AND s.sourceMail != '{$user->mail}'
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
	
    $so = "
    SELECT MAX(sv.submissionId)
    FROM submissions AS s 
    LEFT JOIN submission_votes AS sv ON s.submissionId = sv.submissionId
    WHERE s.done = '0' AND s.modDone = '1'{$c2} 
    AND s.submissionId NOT IN (SELECT submissionId FROM submission_votes WHERE userId = '{$userId}') 
    AND s.sourceMail != '{$user->mail}'
    GROUP BY s.submissionId
    ";
    
    $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submissions AS s WHERE s.done = '0'{$c2}")->fetch();
    $modCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submissions AS s WHERE s.done = '0'{$c2}")->fetch();
    $delCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submissions AS s WHERE s.done = '0'{$q2}")->fetch();
    $ownCount = alxDatabaseManager::rows(alxDatabaseManager::query($so)->getRawQuery());
    
    $adminCount = alxDatabaseManager::query("
    SELECT COUNT(s.submissionId) AS c FROM submissions AS s
    WHERE s.done = '0' AND s.modDone = '1'{$c2}")->fetch();
    
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
        AND s.modDone = '1'
        LIMIT 1
      ")->fetch()->c;
    }
    
    $this->add('tagCounts', $tagCounts);
	
    $this->add('submissions', $submissions);
    $this->add('count', $count->c);
    $this->add('modCount', $modCount->c);
    $this->add('delayCount', $delCount->c);
    $this->add('ownCount', $ownCount);
    $this->add('votesNeeded', $this->votesNeeded);
    $this->add('adminCount', $adminCount->c);
    
    $this->render();
  }
  
  function submissionsByTags($get)
  {
    if(!isAdmin()) return;
    
    $pp = time();
    
    $c2 = '';
    $q2 = " AND (s.postponed != '0' AND s.postponed > '{$pp}')";
    
    if(!@$get->showDelayed)
    {
      $c2 = " AND (s.postponed = '0' OR s.postponed <= '{$pp}')";
    }
    
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
      
      $s = " ORDER BY " . mysql_real_escape_string($sort) . " " . $sortOrder;
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
    
    if(@$get->hideVoted == '1' or @$get->hideVoted == 'true')
    {
      $userId = getUserId();
      $user = getUser($userId);
      $t .= " AND s.submissionId NOT IN (SELECT submissionId FROM submission_votes WHERE userId = '{$userId}') 
      AND s.sourceMail != '{$user->mail}'";
    }
    
    $s = "SELECT s.submissionId, s.created, s.sourceNucleusId, s.sourceMail, s.targetNucleusId, s.type, s.msg,
    (SELECT GROUP_CONCAT(name SEPARATOR ' / ') FROM profiles p WHERE p.nucleusId = s.targetNucleusId) AS names,
    SUM(sv.type='mod') AS modVotesCount, SUM(sv.type='admin') AS votesCount,
    SUM(sv.type='mod' AND sv.vote='1') AS modVotesYes, SUM(sv.type='mod' AND sv.vote='2') AS modVotesNo
    FROM submissions AS s
    LEFT JOIN submission_votes AS sv ON s.submissionId = sv.submissionId
    WHERE s.done = '0' AND s.modDone = '1'{$t}{$c2} 
    GROUP BY s.submissionId
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
    
    $so = "
      SELECT MAX(sv.submissionId)
      FROM submissions AS s 
      LEFT JOIN submission_votes AS sv ON s.submissionId = sv.submissionId
      WHERE s.done = '0' AND s.modDone = '1'{$c2} 
      AND s.submissionId NOT IN (SELECT submissionId FROM submission_votes WHERE userId = '{$userId}') 
      AND s.sourceMail != '{$user->mail}'
      GROUP BY s.submissionId
    ";
    
    $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submissions AS s WHERE s.done = '0'{$c2}")->fetch();
    $modCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submissions AS s WHERE s.done = '0'{$c2}")->fetch();
    $delCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submissions AS s WHERE s.done = '0'{$q2}")->fetch();
    $ownCount = alxDatabaseManager::rows(alxDatabaseManager::query($so)->getRawQuery());
    
    $adminCount = alxDatabaseManager::query("
    SELECT COUNT(s.submissionId) AS c FROM submissions AS s
    WHERE s.done = '0' AND s.modDone = '1'{$c2}")->fetch();
    
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
        AND s.modDone = '1'
        LIMIT 1
      ")->fetch()->c;
    }
    
    $this->add('tagCounts', $tagCounts);
	
    $this->add('submissions', $submissions);
    $this->add('count', $count->c);
    $this->add('modCount', $modCount->c);
    $this->add('delayCount', $delCount->c);
    $this->add('ownCount', $ownCount);
    $this->add('votesNeeded', $this->votesNeeded);
    $this->add('hideVoted', false);
    $this->add('adminCount', $adminCount->c);
    
    $this->add('activeTagId', @$get->tagId);
    
    $this->render('submissions');
  }
  
  function submissionDetail($get)
  {
    if(!isAdmin() and !isMod()) return;
    
    if(!isAdmin())
    {
      $params = getQueryStr();
      header("location: ../modPanel/" . alxRequestHandler::getAction() . "?" . $params);
      return;
    }
    
    if(!@$get->submissionId) return;
    
    $submissionId = (int) $get->submissionId;
    
    $delayed = false;
    
    $submission = alxDatabaseManager::query("SELECT * FROM submissions WHERE submissionId = '{$submissionId}' LIMIT 1")->fetch();
    
    if($submission->postponed > 0 && $submission->postponed >= time())
    {
      $delayed = true;  
    }
    
    
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
    
    $target = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$submission->targetNucleusId}'");
    $targetNames = array();
    $targets = array();
    $targetSoldierIds = array();
    $_targetNames = array();
    while($item = $target->fetch())
    {
      $targets[] = $item;
      $targetNames[] = "{$item->name} ({$item->level})";  
      $_targetNames[] = $item->name;
      $targetSoldierIds[] = $item->soldierId;
    }
    
    $prevSubCount = alxDatabaseManager::query("SELECT COUNT(DISTINCT s.submissionId) AS c FROM profiles as p 
    INNER JOIN submissions AS s ON p.nucleusId = s.targetNucleusId
    WHERE p.nucleusId = '{$submission->targetNucleusId}' AND s.created < {$submission->created}")->fetch()->c;
    
    $prevAppCount = alxDatabaseManager::query("SELECT COUNT(DISTINCT a.appealId) AS c FROM profiles as p
    INNER JOIN bans as b ON b.nucleusId = p.nucleusId
    INNER JOIN appeals as a ON a.banId = b.banId
    WHERE p.nucleusId = '{$submission->targetNucleusId} AND a.created < {$submission->created}'")->fetch()->c;

    $verified = array();
    
    for($i=0;$i<$this->votesNeeded;$i++)
    {
      $verified[] = '-';
    }
    
    $voteCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submission_votes WHERE type = 'admin' AND submissionId = '{$submission->submissionId}'")->fetch();
    $votes = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE type = 'admin' AND submissionId = '{$submission->submissionId}' ORDER BY submissionVoteId");
    
    $voters = array();
    $a = 0;
    while($item = $votes->fetch())
    {
      if($voteCount->c >= $this->votesNeeded or $submission->done or $item->userId == getUserId())
      {
        $x = '';
        if($item->vote == '1') $x = 'Yes';
        if($item->vote == '2') $x = 'No';
        
        $verified[$a] = '<span class="vote' . ($x) . '">' . getUser($item->userId)->username . '</span>';
      }
      else
      {
        $verified[$a] = 'Hidden';
      } 
      
      $voters[] = $item->userId;
      
      $a++;
    }
    
    $adminVoteCount = $voteCount->c;

    $valid = true;
    
    if(in_array(getUserId(), $voters) or $submission->sourceMail != '' && @getUser(getUserId())->mail == $submission->sourceMail)
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
    
    $voteCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM submission_votes WHERE type = 'mod' AND submissionId = '{$submission->submissionId}'")->fetch();
    $votes = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE type = 'mod' AND submissionId = '{$submission->submissionId}' ORDER BY submissionVoteId ASC");

    $modVotes = array();

    $a = 0;
    while($item = $votes->fetch())
    {
      $x = '';
      if($item->vote == '1') $x = 'Yes';
      if($item->vote == '2') $x = 'No';
      if($item->vote == '3') $x = 'Delay';
	
      $modVotes[$a] = '<span class="vote' . ($x) . '">' . getUser($item->userId)->username . '</span>';
      
      $voters[] = $item->userId;
      
      $a++;
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
    
    $this->add('modVoteCount', $voteCount->c);
    $this->add('modVotesNeeded', $this->modVotesNeeded);
    
    $this->add('adminVoteCount', $adminVoteCount);
    
    $this->add('submission', $submission);
    $this->add('delayed', $delayed);
    $this->add('source', $sourceNames);
    $this->add('target', $targetNames);
    $this->add('targetNames', $_targetNames);
    $this->add('verified', implode(' / ', $verified));
    $this->add('valid', $valid);
    $this->add('modVotes', implode(' / ', $modVotes));
    $this->add('targets', $targets);
    $this->add('targetSoldierIds', $targetSoldierIds);
    $this->add('submissionTags', $submissionTags);
    
    $this->add('submissionNotes', $submissionNotes);
    
    $this->render();
  }
  
  function scoreStats($get)
  {
    if(!isAdmin()) return;
    
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
  
  function search($get)
  {
    header('location: ../modPanel/search?' . getQueryStr());
    return;
  }

  function delaySubmission($get)
  {
    if(!isAdmin()) return;
    
    if(!@$get->submissionId) return;
    
    $result = array
    (
      'success' => true
    );
    
    $submissionId = (int) $get->submissionId;
    $type = $get->type;
    
    // 2 Weeks
    $pp = time() + 1209600;
    
    if($type == 'revoke')
    {
      alxDatabaseManager::query("UPDATE submissions SET postponed = '0' WHERE submissionId = '{$submissionId}' LIMIT 1");
    }
    else
    {
      alxDatabaseManager::query("UPDATE submissions SET postponed = '{$pp}' WHERE submissionId = '{$submissionId}' LIMIT 1");
    }
    
    addLog('admin', 'setSubmissionState_' . ($type == 'delay' ? 'Delay' : 'Revoked'), $submissionId);

    $this->respondString(json_encode($result)); 
  }
  
  function setSubmissionState($get)
  {
    if(!isAdmin()) return;
    
    if(!@$get->submissionId or !@$get->state) return;
    
    $result = array
    (
      'success' => true
    );
    
    $id = getUserId();
    $now = time();
    $submissionId = (int) $get->submissionId;
    $vote = (int) $get->state;
    
    $votedCheck = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE submissionId = '{$submissionId}' AND type = 'admin' AND userId = '{$id}' LIMIT 1")->fetch();
    
    if(@$votedCheck->submissionVoteId)
    {
      return;
    }
    
    $msg = mysql_real_escape_string(prepare($get->msg));
    
    $submission = alxDatabaseManager::query("SELECT * FROM submissions WHERE submissionId = '{$submissionId}' LIMIT 1")->fetch();

    alxDatabaseManager::query("INSERT INTO submission_votes SET type = 'admin', submissionId = '{$submissionId}', userId = '{$id}', date = '{$now}', vote = '{$vote}', message = '{$msg}'");
    
    
    $votes = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE submissionId = '{$submissionId}' AND type = 'admin'");
    
    $yes = 0;
    $no = 0;
    
    while($item = $votes->fetch())
    {
      if($item->vote == '1')
      {
        $yes++;   
      }
      
      if($item->vote == '2')
      {
        $no++;
      }
    }
    
    if(($yes + $no) >= $this->votesNeeded or $yes > ($this->votesNeeded/2) or $no > ($this->votesNeeded/2))
    { // FINAL VOTE
      
      if($yes > $no)
      {
        $now = time();
        $blacklistId = $GLOBALS['typeIds'][$submission->type];
        
        // We now allow players to re-submit a player for Cheating if he's already banned for Statspadding/Glitching etc list
        // Which means it's possible that a ban already exists while this one gets created. Check for and disable those previous bans
        $banCheck = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM bans WHERE blacklistId > 1 AND active = '1' AND nucleusId = '{$submission->targetNucleusId}' LIMIT 1")->fetch();
        if(@$banCheck->c > 0)
        {
          alxDatabaseManager::query("UPDATE bans SET active = '0' WHERE blacklistId > 1 AND active = '1' AND nucleusId = '{$submission->targetNucleusId}'");  
        }
        
        alxDatabaseManager::query("INSERT INTO bans SET nucleusId = '{$submission->targetNucleusId}', blacklistId = '{$blacklistId}', submissionId = '{$submission->submissionId}', created = '{$now}'");  
        
        addLog('admin', 'setFinalSubmissionState_Valid', $submission->submissionId);
      }
      else
      {
        addLog('admin', 'setFinalSubmissionState_Invalid', $submission->submissionId);
      }
      
      alxDatabaseManager::query("UPDATE submissions SET done = '1' WHERE submissionId = '{$submission->submissionId}' LIMIT 1");
    }
    
    addLog('admin', 'setSubmissionState_' . ($vote == '1' ? 'Valid' : 'Invalid'), $submission->submissionId);

    $this->respondString(json_encode($result));  
  }
  
  function markSubmissionInvalid($get)
  {
    if(!isAdmin()) return;
    
    if(!@$get->submissionId) return;
    
    $result = array
    (
      'success' => true
    );
    
    $submissionId = (int) $get->submissionId;
    
    $submission = alxDatabaseManager::query("SELECT * FROM submissions WHERE submissionId = '{$submissionId}' LIMIT 1")->fetch();
    
    alxDatabaseManager::query("UPDATE submissions SET done = '1' WHERE submissionId = '{$submission->submissionId}' LIMIT 1");
    
    addLog('admin', 'markedSubmissionInvalid', $submission->submissionId);
    
    $this->respondString(json_encode($result));  
  }
  
  function setSubmissionTags($get)
  {  
    if(!isAdmin()) return;
    
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
    
    addLog('admin', 'updateSubmissionTag_' . $tagId, $get->submissionId);

    $this->respondString(json_encode($result)); 
  }
  
  function addSubmissionNote($get)
  {  
    if(!isAdmin()) return;
    
    if(!@$get->submissionId or !@$get->note) return;
    
    $userId = getUserId();
    
    $result = array
    (
      'success' => true
    );
    
    $note = mysql_real_escape_string($get->note);
    $now = time();
    
    alxDatabaseManager::query("INSERT INTO submission_notes SET submissionId = '{$get->submissionId}', date = '{$now}', userId = '{$userId}', note = '{$note}'");
    
    // addLog('admin', 'addSubmissionNote', $get->submissionId);

    $this->respondString(json_encode($result)); 
  }
  
  function appeals($get)
  {
    if(!isAdmin()) return;
    //if(!isTech()) $this->respondString('in development, roennel');
    
    $appeals = alxDatabaseManager::query("SELECT * FROM appeals WHERE done = '0' ORDER BY created ASC");
    $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM appeals WHERE done = '0' ")->fetch();
    $modCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM appeals WHERE done = '0'")->fetch();
    
    $this->add('appeals', $appeals);
    $this->add('count', $count->c);
    $this->add('modCount', $modCount->c);
    $this->add('modVotesNeeded', $this->modVotesNeeded);
    $this->add('votesNeeded', $this->votesNeeded);
    
    $this->render();
  }
  
  function appealDetail($get)
  {
    if(!isAdmin() and !isMod()) return;
    
    if(!isAdmin())
    {
      $params = getQueryStr();
      header("location: ../modPanel/" . alxRequestHandler::getAction() . "?" . $params);
      return;
    }
    
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
    
    for($i=0;$i<$this->modVotesNeeded;$i++)
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
    
    for($i=0;$i<$this->modVotesNeeded;$i++)
    {
      $appealVerified[$i] = '-';
    }
    
    $appealVoters = array();
    $a = 0;
    while($item = $appealVotes->fetch())
    {
      if($appealVoteCount->c >= $this->modVotesNeeded or $item->userId == getUserId())
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
    
    for($i=0;$i<$this->votesNeeded;$i++)
    {
      $appealAdminVerified[$i] = '-';
    }
    
    $appealAdminVoters = array();
    $a = 0;
    while($item = $appealAdminVotes->fetch())
    {
      if($appealAdminVoteCount->c >= $this->votesNeeded or $item->userId == getUserId())
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
    
    $appealAdminVoteCount = $appealAdminVoteCount->c;

    $valid = true;
    
    if(in_array(getUserId(), $appealAdminVoters) or in_array(getUserId(), $appealVoters) or ($submission->sourceMail != '' && @getUser(getUserId())->mail == $submission->sourceMail))
    {
      $valid = false;
    }
    
    $clanCheck = alxDatabaseManager::query("SELECT clanId FROM users INNER JOIN user_profiles AS up ON users.userId = up.userId WHERE up.nucleusId='{$submission->targetNucleusId}' LIMIT 1")->fetch();
    
    if($clanCheck && $clanCheck->clanId > 1 && $clanCheck->clanId == getUser(getUserId())->clanId)
    {
      $valid = false;
    }
    
    $adminVotes = array();
    $banned = false;
    
    for($i=0;$i<$this->votesNeeded;$i++)
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
    
    $modVoteReasons = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE submissionId = '{$submission->submissionId}' AND type = 'mod' ORDER BY submissionVoteId ASC");
    $adminVoteReasons = alxDatabaseManager::query("SELECT * FROM submission_votes WHERE submissionId = '{$submission->submissionId}' AND type = 'admin' ORDER BY submissionVoteId ASC");
    
    $appealModVoteReasons = alxDatabaseManager::query("SELECT * FROM appeal_votes WHERE appealId = '{$appeal->appealId}' AND type = 'mod' ORDER BY appealVoteId ASC");
    $appealAdminVoteReasons = alxDatabaseManager::query("SELECT * FROM appeal_votes WHERE appealId = '{$appeal->appealId}' AND type = 'admin' ORDER BY appealVoteId ASC");
    
    $this->add('submission', $submission);
    $this->add('appeal', $appeal);
    $this->add('ban', $ban);
    $this->add('source', $sourceNames);
    $this->add('verified', implode(' / ', $verified));
    $this->add('appealVerified', implode(' / ', $appealVerified));
    $this->add('appealAdminVotes', implode(' / ', $appealAdminVerified));
    $this->add('valid', $valid);
    
    $this->add('prevSubCount', $prevSubCount);
    $this->add('prevAppCount', $prevAppCount);
    
    $this->add('modVoteReasons', $modVoteReasons);
    $this->add('adminVoteReasons', $adminVoteReasons);
    $this->add('appealModVoteReasons', $appealModVoteReasons);
    $this->add('appealAdminVoteReasons', $appealAdminVoteReasons);
    
    $this->add('appealAdminVoteCount', $appealAdminVoteCount);
    
    $this->add('banned', $banned);
    $this->add('target', $targetNames);
    $this->add('targetNames', $targetNames2);
    $this->add('adminVotes', implode(' / ', $adminVotes));
    $this->add('modVotes', count($voters));
    $this->add('votesNeeded', $this->modVotesNeeded);
    $this->add('adminVotesNeeded', $this->votesNeeded);
    $this->add('targets', $targets);
    $this->add('targetSoldierIds', $targetSoldierIds);
    $this->add('submissionNotes', $submissionNotes);
    
    $this->render();
  }
  
  function setAppealState($get)
  {
    if(!isAdmin()) return;
    
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
    
    alxDatabaseManager::query("INSERT INTO appeal_votes SET type = 'admin', appealId = '{$appealId}', userId = '{$id}', date = '{$now}', vote = '{$vote}', message = '{$msg}'");

    $votes = alxDatabaseManager::query("SELECT * FROM appeal_votes WHERE appealId = '{$appealId}' AND type = 'admin'");

    $yes = 0;
    $no = 0;
    
    while($item = $votes->fetch())
    {
      if($item->vote == '2')
      {
        $yes++;   
      }
      
      if($item->vote == '1')
      {
        $no++;
      }
    }
    
    if(($yes + $no) >= $this->votesNeeded or $yes > ($this->votesNeeded/2) or $no > ($this->votesNeeded/2))
    { // FINAL VOTE
      
      $ban = alxDatabaseManager::query("SELECT * FROM bans WHERE banId = '{$appeal->banId}' LIMIT 1")->fetch();
      
      if($yes > $no)
      {
        $now = time();

        alxDatabaseManager::query("UPDATE bans SET active = '0' WHERE banId = '{$appeal->banId}' LIMIT 1");  
        
        addLog('admin', 'setFinalAppealState_Valid', $appeal->appealId);
        
        $msg = "We would like to inform you that your appeal of your T4G Blacklist ban has been processed.
<br /><br />
All appeals are subject to the same review process as the original ban, consisting of votes by 5 moderators and 3 admins.  None of the moderators or admins who voted on your original ban were permitted to vote on your appeal.  Furthermore, if the person who originally submitted your name to the Blacklist is a mod or admin on the project, they were not able to vote on your ban or your appeal.
<br /><br />
Following this review process, the result of your appeal is:
<br /><br />
<b>YOUR BAN HAS BEEN LIFTED.</b>
<br /><br />
We thank you for your patience in this process, and hope you will enjoy playing on our affiliated servers once again.
<br /><br />
Your T4G Blacklist Team.
<br />
<a href=\"http://blacklist.tools4games.com\">T4G Blacklist</a>
<br />
<a href=\"http://forum.tools4games.com\">T4G Forum</a>
        ";
      }
      else
      {
        addLog('admin', 'setFinalAppealState_Invalid', $appeal->appealId);
        
        $msg = "
We would like to inform you that your appeal of your T4G Blacklist ban has been processed.
<br /><br />
All appeals are subject to the same review process as the original ban, consisting of votes by 5 moderators and 3 admins.  None of the moderators or admins who voted on your original ban were permitted to vote on your appeal.  Furthermore, if the person who originally submitted your name to the Blacklist is a mod or admin on the project, they were not able to vote on your ban or your appeal.
<br /><br />
Following this review process, the result of your appeal is:
<br /><br />
<b>YOUR BAN HAS BEEN UPHELD.</b>
<br /><br />
Our moderators and admins found sufficient reason to uphold your ban, based on one or more of the following:
<br /><br />
- Existing ban on GGC/PBBans<br />
- Stats that fall outside plausible ranges (cheating)<br />
- Scores that fall outside plausible ranges (statpadding)<br />
- Validated dirty/cropped PBSS<br />
- Other compelling evidence (definitive screenshots/videos/etc)<br />
<br /><br />
If you still feel that this ban is unwarranted, you may file one further appeal, which will be subject to the same review process as the first.  If you do decide to file a 2nd appeal, do NOT simply repeat the statements in your first appeal, as that will guarantee the 2nd will fail as well.
<br /><br />
Your T4G Blacklist Team.
<br />
<a href=\"http://blacklist.tools4games.com\">T4G Blacklist</a>
<br />
<a href=\"http://forum.tools4games.com\">T4G Forum</a>
        ";
      }
      
      $to = $appeal->mail;
      $subject = "T4G Blacklist -> Appeal Result (#{$appeal->appealId})";
      
      sendMail($to, $subject, $msg);
      // sendMail('blacklist-log@tools4games.com', $subject, $msg);
      sendMail('blacklist-log@t4g-blacklist.net', $subject, $msg);
      
      alxDatabaseManager::query("UPDATE appeals SET done = '1' WHERE appealId = '{$appeal->appealId}' LIMIT 1");
    }
    
    addLog('admin', 'setAppealState_' . ($vote == '2' ? 'Valid' : 'Invalid'), $appeal->appealId);

    $this->respondString(json_encode($result));  
  }
  
  function closeAppeal($get)
  {
    if(!isTech()) return;
	
	if(!@$get->appealId or !@$get->msg) return;
	
    $result = array
    (
      'success' => true
    );
	
	$id = getUserId();
    $now = time();
    $appealId = (int) $get->appealId;
	
	$msg = mysql_real_escape_string(prepare($get->msg));
	
	alxDatabaseManager::query("INSERT INTO appeal_votes SET type = 'admin', appealId = '{$appealId}', userId = '{$id}', date = '{$now}', vote = '1', message = '{$msg}'");
	alxDatabaseManager::query("UPDATE appeals SET done = '1' WHERE appealId = '{$appealId}' LIMIT 1");
	
	addLog('admin', 'setFinalAppealState_Invalid', $appealId);
	
    $this->respondString(json_encode($result));
  }
  
  function testSendMail($get)
  {
    echo sendMail($get->to, $get->subject, $get->msg);
    $this->respondString(json_encode(array('a'=>true)));
  }
  
  function adminLog($get)
  {
    if(!isAdmin() and !isTech()) return;
    
    if(@$get->value)
    {
      $log = alxDatabaseManager::query("SELECT * FROM log WHERE type IN ('admin', 'mod') AND value = '{$get->value}' ORDER BY date ASC LIMIT 100");
    }
    else
    {
      $q = '';
      
      if(@$get->actionFilter)
      {
        $q = "AND action LIKE '" . mysql_real_escape_string($get->actionFilter) . "%'";
      }
      
      $start = (int) @$get->start;
      $o = '';
      
      if($start){
        $o = ' OFFSET ' . $start;
      }
      
      $log = alxDatabaseManager::query("SELECT * FROM log WHERE type = 'admin' {$q} ORDER BY date DESC LIMIT 100{$o}");
    }
    
    $this->add('type', 'admin');
    $this->add('log', $log);
    
    $this->render('log');
  }
  
  function modLog($get)
  {
    if(!isAdmin() and !isTech()) return;
    
    $log = alxDatabaseManager::query("SELECT * FROM log WHERE type = 'mod' ORDER BY date DESC LIMIT 100");
    
    $this->add('type', 'mod');
    $this->add('log', $log);
    
    $this->render('log');
  }
  
  function userIps($get)
  {
    if(!isTech()) return;
    
    if(!@$get->userId) return;
    
    $userId = (int) $get->userId;
    
    $log = alxDatabaseManager::query("SELECT DISTINCT(ip) FROM log WHERE userId = '{$userId}' ORDER BY date DESC");
    
    $ips = array();
    
    while($item = $log->fetch())
    {
      $ips[] = "<a href=\"http://www.pbbans.com/mpi/mpi-ip-search-" . str_replace('.', '-', $item->ip) . ".html\">" . $item->ip. "</a>";
    }
    
    $ips = array_unique($ips);
    
    $this->respondString(implode("<br />\r\n", $ips));
  }
  
  function backendStats($get)
  {
    if((!isAdmin() && !isMod()) && @$get->x != 'sfdj833uf82jf320fj') return;
    
    $chain = alxDatabaseManager::query("SELECT serverId, start, end, end-start AS cp FROM chainLog ORDER BY cp DESC");
    $executions = alxDatabaseManager::query("SELECT * FROM executeLog ORDER BY executeLogId DESC LIMIT 100");
    
    $this->add('chain', $chain);
    $this->add('executions', $executions);
    
    $this->render();
  }
  
  function kickLog($get)
  {
    if(!isAdmin() && !isMod()) return;
    
    $w = '';
    
    if(@$get->serverId)
    {
      $w = "WHERE serverId = '{$get->serverId}' ";
    }
    
    if(@$get->type or @$get->type == '0')
    {
      if(empty($w))
      {
        $w = "WHERE type = '{$get->type}' ";  
      }
      else
      {
        $w.= "AND type = '{$get->type}' ";    
      }
    }
    
    $kicks = alxDatabaseManager::query("SELECT * FROM kickLog {$w}ORDER BY kickLogId DESC LIMIT 100");
    
    $this->add('kicks', $kicks);
    $this->add('serverChoose', true);
    
    $this->render();
  }
  
  function setSubmissionType($get)
  {
    if(!isAdmin()) return;
    
    if(!@$get->submissionId) return;
    
    $result = array
    (
      'success' => true
    );
    
    $submissionId = (int) $get->submissionId;
    $type = $get->type;

    alxDatabaseManager::query("UPDATE submissions SET type = '{$type}' WHERE submissionId = '{$submissionId}' LIMIT 1");
    
    addLog('admin', 'setSubmissionType_' . $type, $submissionId);

    $this->respondString(json_encode($result)); 
  }
  
  function setAppealBanId($get)
  {
    if(!isAdmin()) return;
    
    if(!@$get->appealId or !@$get->type) return;
    
    $result = array
    (
      'success' => true
    );
    
    $appealId = (int) $get->appealId;
    $type = $get->type;
    
    $appeal = alxDatabaseManager::query("SELECT * FROM appeals WHERE appealId = '{$appealId}' LIMIT 1")->fetch();
    
    $ban = alxDatabaseManager::query("SELECT * FROM bans WHERE banId = '{$appeal->banId}' LIMIT 1")->fetch();

    $blacklistId = $GLOBALS['typeIds'][$type];
   
    if(!$blacklistId or !$ban->banId)
      return;
     
    alxDatabaseManager::query("UPDATE bans SET blacklistId = '{$blacklistId}' WHERE banId = '{$appeal->banId}' LIMIT 1");
    alxDatabaseManager::query("UPDATE submissions SET type= '{$type}' WHERE submissionId = '{$ban->submissionId}' LIMIT 1");
   
    addLog('admin', 'setBanId_' . $blacklistId, $appeal->banId);
    addLog('admin', 'setSubmissionType_' . $type, $ban->submissionId);

    $this->respondString(json_encode($result)); 
  }
  
  function listUsers($get)
  {
    if(!isAdmin() and !isTech()) return;
    
    $w = "`mod` = '1' OR admin = '1' OR tech = '1'";
    if(@$get->q and @$get->xhr)
    {
      $q = mysql_real_escape_string($get->q);
      $w = "username = '{$q}' OR userId = '{$q}'";
    }
    
    global $db;
    
    $users = array();
    $_users = alxDatabaseManager::query("SELECT * FROM users WHERE {$w} ORDER BY userId");
    $count = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM users WHERE {$w}")->fetch();
    
    $startTime = strtotime('-' . ('1month')) ?: time();
    $startTime = floor($startTime/86400)*86400;
    $endTime = floor(time()/86400)*86400;
      
    $days = ($endTime - $startTime) / 86400;
    
    while($user = $_users->fetch())
    {
      $user->nucleusIds = array();
      $user->names = array();
      
      $_nucleusIds = alxDatabaseManager::query("
        SELECT up.nucleusId, GROUP_CONCAT(name SEPARATOR ' / ') AS names
        FROM user_profiles AS up, profiles AS p
        WHERE p.nucleusId = up.nucleusId AND up.userId = '{$user->userId}'
        GROUP BY p.nucleusId
      ");
      
      while($profile = $_nucleusIds->fetch())
      {
        $user->nucleusIds[] = $profile->nucleusId;
        $user->names[$profile->nucleusId] = $profile->names;
      }

      if($user->tech)
      {
        $user->type = 'tech';
        $users['Tech'][] = $user;
      }elseif($user->admin)
      {
        $user->type = 'admin';
        $users['Admin'][] = $user;
      }elseif($user->mod)
      {
        $user->type = 'mod';
        $users['Mod'][] = $user;
      }else
      {
        $user->type = 'user';
        $users['User'][] = $user;
      }
          
      $actions = alxDatabaseManager::query("
        SELECT
          COUNT(*) AS count
        FROM `log`
        WHERE
          userId IN ({$user->userId}) AND
          type IN ('mod', 'admin') AND
          (FLOOR(date/86400) * 86400) > {$startTime}
      ")->fetch()->count;
      
      $user->ad = $actions / $days;
      
      if($user->forumUserId)
      {
        $user->forum = null;
        
        $sql = 'SELECT * FROM phpbb1_users WHERE user_id = ' . (int) $user->forumUserId;
        $sqlResult = $db->sql_query($sql);
        $user->forum = $db->sql_fetchrow($sqlResult);
        $db->sql_freeresult($sqlResult);
      }
    }
    
    $clans = alxDatabaseManager::fetchMultiple("SELECT * FROM clans ORDER BY label ASC");
    
    $this->add('clans', $clans);
    $this->add('users', $users);
    $this->add('usersCount', $count->c);
    
    if(@$get->xhr)
    {
      $this->add('showHeader', false);
      $this->respond();
    }
    else
    {
      $this->add('showHeader', true);
      $this->render();
    }
  }
  
  function editUser($get)
  {
    if(!isAdmin() and !isTech()) return;
    
    $this->render();
  }
  
  function setUserAs($get)
  {
    if(!isAdmin() and !isTech()) return;
    
    if(!@$get->userId or !@$get->type) return;
    
    $groupIds = array
    (
      'mod' => 13,
      'admin' => 15
    );
    
    $result = array
    (
      'success' => true
    );
    
    $userId = (int) $get->userId;
    $userType = $get->type;
    $forumUserId = null;
    
    $blUser = new UserModel;
    if($blUser->loadById($userId))
    {
      $forumUserId = $blUser->forumUserId;
    }
    
    global $user;
    // Update $user->ip so that the correct IP address gets logged in phpbb's (admin) log
    $user->ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?: $_SERVER['REMOTE_ADDR'];
    
    if($userType == 'mod')
    {
      alxDatabaseManager::query("UPDATE users SET `mod` = '1' WHERE userId = '{$userId}'");
      
      if($forumUserId)
      {
        group_user_add($groupIds['mod'], $forumUserId, false, false, true);
        
        // Send Welcome PM
        $my_subject = utf8_normalize_nfc('Important Information for Blacklist Moderators');
        $my_text    = utf8_normalize_nfc('Congratulations and welcome to T4G Blacklist.

This is an automated message we send out to all new moderators for Blacklist.  It contains much of the information you will need to know to be an effective part of the staff here.  Please take the  time to review this information and ask questions often.  All of the staff here is willing to help and guide you as you get a feel for how we do things.
It is encouraged for you to install Skype as we use this as a Instant Messaging communication tool.  The link for that can be found below.  Once installed, contact Gazza at [url]http://forum.tools4games.com/ucp.php?i=pm&mode=compose&u=69[/url] to be added to the private T4G Blacklist group chat and T4G Moderators group chat.

As a member of the staff here you will be expected to maintain a minimum amount of activity working on submissions and appeals.  Activity is tracked and visible to all the staff just as you can view the activity of the other staff members.  This is a team effort and we want to share the workload since we are all here on a voluntary basis.

It is also very important that you visit this website often to participate in discussions or to simply keep yourself informed as we change any policies or procedures for dealing with different scenarios.  You are part of the team and your input will be valued so be sure to speak up if you have an opinion on anything.

These few links will take you to posts that are MANDATORY to read and understand.
[list][*][color=#FF8040]Policies and Procedures:[/color]  http://forum.tools4games.com/viewtopic.php?f=52&t=1519
[*][color=#FF8040]Non Disclosure Policy:[/color] http://forum.tools4games.com/viewtopic.php?f=54&t=457
[*][color=#FF8040]Moderator guide #1:[/color]  http://forum.tools4games.com/viewtopic.php?f=54&t=120
[*][color=#FF8040]Moderator guide #2:[/color]  http://forum.tools4games.com/viewtopic.php?f=54&t=1106
[*][color=#FF8040]SKYPE Download Link:[/color]  http://www.skype.com/en/download-skype/skype-for-computer/ [/list]

Some of the material found in those posts will be repetitive.  Ask questions when in doubt.  Remember that our job here is to ban "Cheaters, Stats-Padders and Glitchers" but that should NEVER be greater than our desire to not ban an innocent player.  Demand conclusive proof in your decision making and place detailed messages in your "Vote Message" to let others know WHY you voted the way you did and what evidence you used to come to your conclusion.

Good luck and thanks in advance for the time and effort you will be donating here.');

        // variables to hold the parameters for submit_pm
        $poll = $uid = $bitfield = $options = ''; 
        generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
        generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);
        
        $data = array( 
            'address_list'      => array ('u' => array($forumUserId => 'to')),
            'from_user_id'      => $user->data['user_id'],
            'from_username'     => $user->data['username'],
            'icon_id'           => 0,
            'from_user_ip'      => $user->ip, // $user->data['user_ip'],
             
            'enable_bbcode'     => true,
            'enable_smilies'    => true,
            'enable_urls'       => true,
            'enable_sig'        => true,
                    
            'message'           => $my_text,
            'bbcode_bitfield'   => $bitfield,
            'bbcode_uid'        => $uid,
        );
        
        $result['pm_id'] = submit_pm('post', $my_subject, $data, true);
      }
    }
    elseif($userType == 'user')
    {
      alxDatabaseManager::query("UPDATE users SET `mod` = '0', `admin` = '0' WHERE userId = '{$userId}'");
      
      if($forumUserId)
      {
        group_user_del($groupIds['mod'], $forumUserId);
        // group_user_del($groupIds['admin'], $forumUserId);
      }
    }
    else
    {
      return;
    }
    
    addLog('admin', 'setUserAs_' . $userType, $userId);
    
    $this->respondString(json_encode($result));
  }
  
  function userLog($get)
  {
    if(!isTech()) return;
    
    $log = alxDatabaseManager::query("SELECT * FROM log WHERE type = 'user' ORDER BY date DESC LIMIT 100");
    
    $this->add('type', 'user');
    $this->add('log', $log);
    
    $this->render('log');
  }
  
  
  function addNucleusId($get)
  {
    if(!isAdmin() and !isTech()) return;
    
    if(!@$get->userId or !@$get->nucleusId) return;
    
    $result = array
    (
      'success' => true
    );
    
    $userId = (int) $get->userId;
    $nucleusId = $get->nucleusId;
    
    alxDatabaseManager::query("INSERT INTO user_profiles SET userId= '{$userId}', nucleusId = '{$nucleusId}'");
   
    $this->respondString(json_encode($result)); 
  }
  
  function setClanForUser($get)
  {
    if(!isAdmin() and !isTech()) return;
    
    if(!@$get->userId or @$get->clanId=='') return;
    
    $result = array
    (
      'success' => true
    );
    
    $userId = (int) $get->userId;
    $clanId = (int) $get->clanId;
    
    alxDatabaseManager::query("UPDATE users SET clanId = '{$clanId}' WHERE userId = '{$userId}'");
   
    $this->respondString(json_encode($result)); 
  }
  
  function setForumIdForUser($get)
  {
    if(!isAdmin() and !isTech()) return;
    
    if(!@$get->userId or !@$get->forumUserId) return;
    
    $result = array
    (
      'success' => true,
      'forumUser' => array()
    );
    
    $userId = (int) $get->userId;
    $forumUserId = mysql_real_escape_string($get->forumUserId);
    
    $fuser_id = $forumUserId;
    $fuser_name = array();
    $res = user_get_id_name($fuser_id, $fuser_name);
    
    if($res === false && sizeof($fuser_name))
    {
      $result['forumUser']['name'] = $fuser_name[$forumUserId];
      $result['forumUser']['id'] = $fuser_id[0];
      alxDatabaseManager::query("UPDATE users SET forumUserId = '{$forumUserId}' WHERE userId = '{$userId}'");
    }
    else
    {
      $result['success'] = false;
    }
   
    $this->respondString(json_encode($result)); 
  }
  
  /*
  function batchProcessSubs($get)
  {
    if(!isMod() or !(getUserId() == '5' or getUserId() == '513')) return;
    
    $result = array
    (
      'success' => true,
      'yes' => 0,
      'no' => 0
    );
    
    $subs = alxDatabaseManager::query("
    SELECT s.type, s.created, s.done, s.submissionId, sv.vote, s.targetNucleusId, COUNT(DISTINCT sv.vote) AS distinct_votes, COUNT(sv.submissionVoteId) AS votes
    FROM `submission_votes` AS sv, submissions AS s
    WHERE s.submissionId = sv.submissionId AND s.done = '0' AND sv.type = 'admin'
    GROUP BY sv.submissionId
    HAVING distinct_votes = '1' AND votes = '2'
    ");
    
    while($item = $subs->fetch())
    {
      $submissionId = $item->submissionId;
      $vote = $item->vote;
      
      $now = time();
      
      if($vote == '1')
      {
        $action = 'setFinalSubmissionState_Valid';
        
        $blacklistId = $GLOBALS['typeIds'][$item->type];
        
        alxDatabaseManager::query("INSERT INTO bans SET nucleusId = '{$item->targetNucleusId}', blacklistId = '{$blacklistId}', submissionId = '{$submissionId}', created = '{$now}'");
        
        $result['yes']++;
      }
      else
      {
        $action = 'setFinalSubmissionState_Invalid';
        
        $result['no']++;
      }
      
      alxDatabaseManager::query("UPDATE submissions SET done = '1' WHERE submissionId = '{$submissionId}' LIMIT 1");

      alxDatabaseManager::query("INSERT INTO `log` SET userId = '0', type = 'admin', date = '{$now}', action = '{$action}', value = '{$submissionId}'");
    }
    
    $this->respondString(json_encode($result)); 
  }
  */
}
