<?php

class RegisterController extends alxController
{
  function index()
  {
    $this->render();
  }
  
  function server($get)
  {
    $this->render();
  }
  
  function post_doRegister($post, $get)
  {
    if(!@$post->username or !@$post->mail or !@$post->pwd) return;
    
    $sourceIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
    
    $result = array
    (
      'state' => false
    );
    
    $exists = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM users WHERE username = '{$post->username}' OR mail = '{$post->mail}'")->fetch();
    
    if($exists->c > 0)
    {
      $result['nameUnavailable'] = true;
      $this->respondJSON($result);
      return;
    }
    
    $userModel = new UserModel;
    $userModel->username = $post->username;
    $userModel->mail = $post->mail;
    $userModel->pwd = hash('sha512', $post->pwd);
    $userModel->joined = time();
    $userModel->ip = $sourceIp;
    $userModel->mailVerified = '0';
    
    $state = $userModel->create();
    
    if($state)
    {
      $salt = 'f#01k)$E@k,ad0d_d';
      $key = sha1($salt . $userModel->pwd . $userModel->mail);
    
      $verifyLink = "http://blacklist.tools4games.com/en/register/verifyEmail?m=" . urlencode($userModel->mail) . "&key={$key}";
      
      $to = $userModel->mail;
      $subject = "T4G Blacklist -> Verify Email Address";
      $msg = "
Hi {$userModel->username},
<br /><br />
Thanks for registering.
<br /><br />
Before you can start using your Blacklist Account, we need you to verify your email address.
<br /><br />
<a href=\"{$verifyLink}\">Confirm Email Address</a>.
<br /><br />
If you did not sign up to Blacklist using this email address, you can safely ignore this message.
<br /><br />
Thanks,<br />
Your T4G Blacklist Team.
<br />
<a href=\"http://blacklist.tools4games.com\">T4G Blacklist</a>
<br />
<a href=\"http://forum.tools4games.com\">T4G Forum</a>
  ";
      
      ob_start();
      sendMail($to, $subject, $msg);
      ob_end_clean();
    }
    
    $result['state'] = $state;
    
    $this->respondJSON($result);
  }
  
  function verifyEmail($get)
  {
    if(!@$get->m or !@$get->key) return;
    
    $mail = $get->m;
    
    $user = new UserModel;
    if(!$user->load(array('mail' => $mail))) return;
    
    $alreadyVerified = false;
    $verified = false;
    
    if($user->mailVerified == '0')
    {
      $salt = 'f#01k)$E@k,ad0d_d';
      $key = sha1($salt . $user->pwd . $user->mail);
      
      if($key == $get->key)
      {
        $user->mailVerified = '1';
        $state = $user->update();
        
        $verified = true;
        
        addLog('user', 'verifiedEmail', $user->userId);
      }
    }
    else
    {
      $alreadyVerified = true;
    }
    
    $this->add('alreadyVerified', $alreadyVerified);
    $this->add('verified', $verified);
    $this->render();
  }

}
