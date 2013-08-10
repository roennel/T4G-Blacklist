<?php

class AppealController extends alxController
{
  public $minWaitingPeriod = 604800; // 7 days

  function index()
  {
    $this->render();
  }
  
  function checkPlayer($get)
  {
    if(!@$get->nucleusId) return;
    
    $result = array
    (
      'success' => true,
      'existingBan' => false,
      'existingAppeal' => false
    );
    
    $nucleusId = (int) $get->nucleusId;
    
    $check = alxDatabaseManager::query("SELECT * FROM bans WHERE nucleusId = '{$nucleusId}' AND active = '1' LIMIT 1")->fetch();
    
    if(@$check->banId > 0)
    {
      $result['banId'] = $check->banId;
      $result['existingBan'] = true;
    
      $check2 = alxDatabaseManager::query("SELECT * FROM appeals WHERE banId = '{$check->banId}' ORDER BY created DESC LIMIT 1")->fetch();
      $appealCount = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM appeals WHERE banId = '{$check->banId}'")->fetch()->c;
      
      if(@$check2->appealId > 0)
      {
        if($check2->done == '0')
        {
          $result['appealId'] = $check2->appealId;
          $result['existingAppeal'] = true;
        }
        else{
          $wp = $this->minWaitingPeriod * $appealCount;
          if($check2->created > (time()-$wp))
          {
            $result['appealId'] = $check2->appealId;
            $result['recentlyAppealed'] = true;
            $result['lastAppealDate'] = date('d.m.Y', $check2->created);
          }
        }
      }
    }
    
    $this->respondString(json_encode($result));
  }
  
  function post_add($post, $get)
  {
    if(!@$post->banId or !@$post->mail or !@$post->text) return;
    
    $result = array
    (
      'success' => true
    );
    
    $banId = (int) $post->banId;
    $mail = mysql_real_escape_string($post->mail);
    $text = mysql_real_escape_string($post->text);
    $created = time();
    $sourceIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
    
    alxDatabaseManager::query("INSERT INTO appeals SET created = '{$created}', banId = '{$banId}', mail = '{$mail}', appeal = '{$text}', ip = '{$sourceIp}'");
    
    $this->respondString(json_encode($result));
  }
}
