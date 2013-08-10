<?php

class AccountController extends alxController
{
  function index($get)
  {
    if(!isLogged()) return;
    
    $userId = getUserId();
    
    if(@$get->userId and isTech())
    {
      $userId = $get->userId;
    }
    
    $user = alxDatabaseManager::query("SELECT * FROM users WHERE userId = '{$userId}' LIMIT 1")->fetch();
    
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
    
    $user->type = $user->tech ? 'tech' : ($user->admin ? 'admin' : ($user->mod ? 'mod' : 'user'));
    
    
    if(count($user->nucleusIds))
    {
      $subCount = alxDatabaseManager::query("SELECT COUNT(submissionId) AS c FROM submissions WHERE sourceNucleusId IN (" . implode(',', $user->nucleusIds) . ")")->fetch()->c;
      $this->add('subCount', $subCount);
    }
    else
    {
      $this->add('subCount', false);
    }
    
    $serverCount = alxDatabaseManager::query("SELECT COUNT(serverId) AS c FROM servers WHERE userId = '{$userId}'")->fetch()->c;
    $this->add('serverCount', $serverCount);
    
    $clans = alxDatabaseManager::query("SELECT * FROM clans ORDER BY label ASC");
    $this->add('clans', $clans);
    
    $this->add('userId', $userId);
    $this->add('user', $user);
    
    $this->render();
  }
  
  function login($get)
  {
  /*
    if(isLogged()) 
    {
      $this->redirect('home');
      return;
    }
  */
    $this->render();
  }
  
  function post_requestPassword($post)
  {
    if(!@$post->username and !@$post->mail) return;
    
    $result = array
    (
      'state' => false,
      'valid' => false
    );

    $user = new UserModel;
    
    $params = $post->mail ? array('mail' => $post->mail) : array('username' => $post->username);
    
    if(!$user->load($params) or $user->mailVerified == '0')
    {
      $this->respondString(json_encode($result)); 
      return;
    }
    
    $result['valid'] = true;
    
    // $newPass = substr(sha1(uniqid(mt_rand(), true)), 0, 10);;
    $salt = 'ma*#%&(~!q0nt';
    $key = sha1($salt . $user->pwd . $user->mail);
    
    $to = $user->mail;
    $subject = "T4G Blacklist -> Reset Password";
    $msg = "
Hi {$user->username},
<br /><br />
You're receiving this email because someone requested a password reset for your Blacklist Account.
If it was not done by you, please ignore this email.
<br /><br />
To set a new password, click the following link:
<br />
http://blacklist.tools4games.com/en/account/resetPassword?u={$user->userId}&key={$key}
<br /><br />
Your Password Reset Key is: {$key}
<br /><br />
Your T4G Blacklist Team.
<br />
<a href=\"http://blacklist.tools4games.com\">T4G Blacklist</a>
<br />
<a href=\"http://forum.tools4games.com\">T4G Forum</a>
";
    
    ob_start();
    $success = sendMail($to, $subject, $msg);
    ob_end_clean();
    
    $result['state'] = $success ? true : false;
    
    addLog('user', 'requestPasswordReset', $user->userId);
    
    $this->respondString(json_encode($result)); 
  }
  
  function resetPassword($get)
  {  
    $this->render();
  }
  
  function post_resetPassword($post)
  {
    if(!@$post->u or !@$post->key or !@$post->newPass) return;
    
    $userId = (int) $post->u;
    
    $user = new UserModel;
    if(!$user->loadById($userId)) return;
    
    $result = array
    (
      'success' => true
    );
    
    $newPass = hash('sha512', $post->newPass);
      
    $salt = 'ma*#%&(~!q0nt';
    $key = sha1($salt . $user->pwd . $user->mail);
    
    if($key == $post->key)
    {
      $user->pwd = $newPass;
      $state = $user->update();
      
      $result['success'] = $state;
      
      addLog('user', 'resetPassword', $user->userId);
    }
    else
    {
      $result['success'] = false;
    }
   
    $this->respondString(json_encode($result)); 
  }
  
  function addNucleusId($get)
  {
    if(!isLogged() or !@$get->nucleusId) return;
    
    $userId = getUserId();
    
    if(@$get->userId and isTech())
    {
      $userId = $get->userId;
    }
    
    $result = array
    (
      'success' => false
    );
    
    $userId = (int) $userId;
    $nucleusId = mysql_real_escape_string($get->nucleusId);
    $ins = false;
    
    // Update soldiers and check if nucleusId exists
    $url = "http://battlefield.play4free.com/en/profile/soldiers/{$nucleusId}";
    
    $con = @file_get_contents($url);
    
    if($con)
    {
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
      
      if(count($json->data))
      {
        $ins = alxDatabaseManager::query("INSERT INTO user_profiles SET userId= '{$userId}', nucleusId = '{$nucleusId}'");
    
        if($ins)
        {
          $result['success'] = true;
          addLog('user', 'addNucleusId', $nucleusId);
        }
      }
    }

    $this->respondString(json_encode($result)); 
  }
  
  function setClanId($get)
  {
    if(!isLogged() or @$get->clanId=='') return;
    
    $userId = getUserId();
    
    if(@$get->userId and isTech())
    {
      $userId = $get->userId;
    }
    
    $result = array
    (
      'success' => true
    );
    
    $userId = (int) $userId;
    $clanId = $get->clanId;
    
    $userModel = new UserModel;
    $userModel->loadById($userId);
    
    $userModel->clanId = $clanId;
    $state = $userModel->update();
    
    $result['success'] = $state;
    
    addLog('user', 'setClanId', $clanId);
   
    $this->respondString(json_encode($result)); 
  }
  
  function setCountry($get)
  {
    if(!isLogged()) return;
    
    $userId = getUserId();
    
    if(@$get->userId and isTech())
    {
      $userId = $get->userId;
    }
    
    $result = array
    (
      'success' => true
    );
    
    $userId = (int) $userId;
    $country = $get->country;
    
    $userModel = new UserModel;
    $userModel->loadById($userId);
    
    $userModel->country = $country;
    $state = $userModel->update();
    
    $result['success'] = $state;
    
    addLog('user', 'setCountry', $country);
   
    $this->respondString(json_encode($result)); 
  }
  
  function post_changePwd($post)
  {
    if(!isLogged() or !@$post->oldPass or !@$post->newPass) return;
    
    $userId = getUserId();
    
    if(@$post->userId and isTech())
    {
      $userId = $post->userId;
    }
    
    $result = array
    (
      'success' => true
    );
    
    $userId = (int) $userId;
    $oldPass = hash('sha512', $post->oldPass);
    $newPass = hash('sha512', $post->newPass);
    
    $userModel = new UserModel;
    $userModel->loadById($userId);
    
    if($userModel->pwd == $oldPass)
    {
      $userModel->pwd = $newPass;
      $state = $userModel->update();
      
      $result['success'] = $state;
    }
    else
    {
      $result['success'] = false;
    }
   
    addLog('user', 'updatedPassword', '');
    
    $this->respondString(json_encode($result)); 
  }
}
