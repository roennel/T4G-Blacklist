<?php

class AuthController extends alxController
{
  function login($get)
  {
    if(!@$get->username or !@$get->pwd) return;
    
    $result = array
    (
      'state' => false,
      'error' => false
    );
    
    $get->pwd = hash('sha512', $get->pwd);
    
    $userModel = new UserModel;
    $userModels = $userModel->getItems(array
    (
      'username' => trim($get->username),
      'pwd' => trim($get->pwd)
    ));
    
    $user = @$userModels[0];
    $state = false;
    $error = false;
    
    if($user)
    {
      if($user->mailVerified == '1')
      {
        $state = true;
        
        $_SESSION['t4gBlacklistUserId'] = $user->userId;
        $_SESSION['t4gBlacklistMod'] = $user->mod;
        $_SESSION['t4gBlacklistAdmin'] = $user->admin;
        $_SESSION['t4gBlacklistTech'] = $user->tech;
        $_SESSION['t4gBlacklistUserMail'] = $user->mail;
      }
      else
      {
        $error = 'verifyEmail';
      }
    }
    else
    {
      $error = true;
    }
    
    $result['state'] = $state;
    $result['error'] = $error;
    
    $this->respondJSON($result);
  }
  
  function logout($get)
  {
    unset($_SESSION['t4gBlacklistUserId']);
    unset($_SESSION['t4gBlacklistMod']);
    unset($_SESSION['t4gBlacklistAdmin']);
    unset($_SESSION['t4gBlacklistTech']);
    unset($_SESSION['t4gBlacklistUserMail']);
    session_destroy();
    
    $this->redirect('home');
  }
  
  function loginAs($get)
  {
    $user = new UserModel;
    $prevUserId = getUserId();
    $newUserId = null;
    
    if(@$get->userId)
    {
      if(isTech() && $user->loadById($get->userId))
      {
        $_SESSION['t4gBlacklistUserId_'] = getUserId();
        
        $_SESSION['t4gBlacklistUserId'] = $user->userId;
        $_SESSION['t4gBlacklistMod'] = $user->mod;
        $_SESSION['t4gBlacklistAdmin'] = $user->admin;
        $_SESSION['t4gBlacklistTech'] = $user->tech;
        $_SESSION['t4gBlacklistUserMail'] = $user->mail;
        
        $newUserId = $user->userId;
      }
    }
    else if(@$_SESSION['t4gBlacklistUserId_'])
    {
      if($user->loadById($_SESSION['t4gBlacklistUserId_']))
      {
        unset($_SESSION['t4gBlacklistUserId_']);
        
        $_SESSION['t4gBlacklistUserId'] = $user->userId;
        $_SESSION['t4gBlacklistMod'] = $user->mod;
        $_SESSION['t4gBlacklistAdmin'] = $user->admin;
        $_SESSION['t4gBlacklistTech'] = $user->tech;
        $_SESSION['t4gBlacklistUserMail'] = $user->mail;
        
        $newUserId = $user->userId;
      }
    }
    
    $this->respondString(json_encode(array
    (
      'success' => true,
      'oldUserId' => $prevUserId,
      'newUserId' => $newUserId,
      'changed' => $newUserId ? true : false
    )));
  }
  
  function f()
  {
    return false;
    
    $users = alxDatabaseManager::fetchMultiple
    ("SELECT * FROM users");
    
    foreach($users as $user)
    {
      $pw = hash('sha512', $user->pwd);
      
      alxDatabaseManager::query
      ("
        UPDATE users SET pwd = '{$pw}' WHERE userId = '{$user->userId}' LIMIT 1
      ");
    }
  }
}
