<div id="isLogged" class="sub extended" style="color: red">
  You're already logged in.
</div>

<div class="sub" style="width: 475px; height: 200px" id="loginBox">
  <div style="height: 75px">
    <h2><?php t('login') ?></h2>
    <span class="loginInfo">
      Enter your Blacklist Username and Password.
    </span>

    <span id="loginFail" class="loginInfo fail">
      Invalid username or password.
    </span>

    <span id="loginFail2" class="loginInfo fail">
      You need to verify your email address before you can log in. <!-- If you haven't received a verification mail yet, <a id="verifyEmailLink" href="#">click here</a> to request another one. -->
    </span>
    
    <span id="loginSuccess" class="loginInfo success">
      Logged in. Redirecting..
    </span>
  </div>
  
  <table class="input" style="width: 100%; margin-top: 5px">
    <tbody>
      <tr>
        <td><?php t('username') ?></td>
        <td>
          <input type="text" id="authUsername" value="<?= htmlspecialchars(@$_GET['username']) ?>" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td><?php t('password') ?></td>
        <td>
          <input type="password" id="authPassword" />
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <input type="button" id="auth" value="<?php t('login') ?>" />
        </td>
      </tr>
    </tbody>
  </table>
</div>

<div class="sub" id="forgotPassBox" style="width: 400px; height: 200px">
  <div style="height: 75px">
    <h2>Forgot Passsword</h2>
    <span class="registerInfo">
      Enter either your Blacklist Username or Email Address to request a new password reset email.
    </span>
    <span id="registerSuccess" class="registerInfo success">
      An email has been sent to your registered email address. Click the link in the email to set a new password.
    </span>
    <span id="registerFail" class="registerInfo fail">
      An Error has occured. Verify that the email or username you've entered is valid.
    </span>
    <span id="registerFail2" class="registerInfo fail">
      An Error occured while sending mail.
    </span>
  </div>

  <table class="input" style="width: 100%; margin-top: 5px" id="registerTable">
    <tbody>
      <tr>
        <td><?php t('email') ?></td>
        <td>
          <input type="text" id="email" name="email" />
        </td>
        <td></td>
      </tr>
      <tr>
        <td><?php t('username') ?></td>
        <td>
          <input type="text" id="username" name="username" />
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <input type="button" id="register" value="<?php t('submit') ?>" />
        </td>
      </tr>
    </tbody>
  </table>
</div>

<script type="text/javascript">
  $('registerSuccess').hide();
  $('registerFail').hide();
  $('registerFail2').hide();
  $('loginSuccess').hide();
  $('loginFail').hide();
  $('loginFail2').hide();

window.addEvent('domready', function()
{
  <?php if(isLogged()):?>
  $$('.input input').set('disabled', true);
  return;
  <?php else: ?>
  $('isLogged').hide();
  <?php endif ?>
  
  <?php if(isset($_GET['forgotPassword'])): ?>
  $('email').focus();
  <?php else: ?>
  $('authUsername').focus();
  <?php endif ?>
  
  <?php if(@$_GET['loginError'] == 'verifyEmail'): ?>
  $$('.loginInfo').hide();
  $('loginFail2').show();
  <?php elseif(isset($_GET['loginError'])): ?>
  $$('.loginInfo').hide();
  $('loginFail').show();
  <?php endif ?>
  
  $('register').addEvent('click', function()
  {
    $$('#forgotPassBox table.input input').removeClass('error');
    // $('registerFail').hide();
    
    var username = $('username').get('value');
    var email = $('email').get('value');
    
    if(username == '' && email == '')
    {
      $('email').addClass('error');
      return;
    }
    
    new Request.JSON
    ({
      url: 'requestPassword',
      onSuccess: function(response)
      {
        if(response.state)
        { // Success
          $$('.registerInfo').hide();
          $('registerTable').dispose();
          $('registerSuccess').show();
          return;
        }
        else
        { // Error
          // $('registerTable').dispose();
          $$('.registerInfo').hide();

          if(response.valid)
            $('registerFail2').show();
          else
            $('registerFail').show();
        }
      }
    }).post({username: username, mail: email});
  });

  var auth = function()
  {
    $$('#loginBox table.input input').removeClass('error');
    // $('loginFail').hide();
    
    var username = $('authUsername').get('value');
    var pwd = $('authPassword').get('value');
    
    if(username == '')
    {
      $('authUsername').addClass('error');
      return;
    }
    
    if(pwd == '')
    {
      $('authPassword').addClass('error');
      return;
    }
    
    $('auth').disabled = true;
    
    new Request.JSON
    ({
      url: '/en/auth/login',
      onSuccess: function(response)
      {
        if(response.state)
        {
          $$('.loginInfo').hide();
          $('loginSuccess').show();
          
          t4g.url.controller = 'account';
          t4g.url.action = '';
          t4g.url.redirect();
        }
        else
        {
          $$('.loginInfo').hide();
          
          if(response.error == 'verifyEmail')
            $('loginFail2').show();
          else
            $('loginFail').show();
          
          $('auth').disabled = false;
        }
      }
    }).get('username=' + encodeURIComponent(username) + '&pwd=' + encodeURIComponent(pwd));
  };
  
  $('authPassword').addEvent('keydown', function(event){
    if(event.key == 'enter') auth();
  });
  $('auth').addEvent('click', auth);
  
  /*
  $('verifyEmailLink').addEvent('click', function(e)
  {
    
  });
  */
});
</script>
