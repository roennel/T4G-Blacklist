<div class="sub extended">
  
  <p>
    <?php t('registerServerIntro') ?>
  </p>
  
</div>

<div class="sub" style="width:380px"> 
  
  <h2><?php t('register') ?></h2>
  
  <span id="registerSuccess">
    Your Account has been created.
    <br /><br />
    Please check your inbox and click the verification link to activate your account.
  </span>
  
  <span id="registerFailName" class="fail">
    The username or e-mail address is unavailable.
  </span>
  
  <span id="registerFail" class="fail">
    An Error has occured.
  </span>
  
  <table class="input" style="width: 380px; margin-top: 10px" id="registerTable">
    <tbody>
      <tr>
        <td><?php t('username') ?></td>
        <td>
          <input type="text" id="username" />
        </td>
      </tr>
      <tr>
        <td><?php t('email') ?></td>
        <td>
          <input type="text" id="mail" />
        </td>
      </tr>
      <tr>
        <td><?php t('password') ?></td>
        <td>
          <input type="password" id="password" />
        </td>
      </tr>
      <tr>
        <td><?php t('password2') ?></td>
        <td>
          <input type="password" id="password2" />
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <input type="button" id="register" value="<?php t('register') ?>" />
        </td>
      </tr>
    </tbody>
  </table>
</div>

<div class="sub" style="width: 500px">
  
  <h2><?php t('info') ?></h2>
  
  <h3>Available Methods:</h3>
  
  <h4>RCON</h4>
  <p>
    Direct access via RCON Data.
  </p>
  
  <h4>PunkBuster UCon</h4>
  <p>
    In Development.
  </p>
  
  <h4>P4FCC XML Banlist Export</h4>
  <p>
    Not implemented, discuss <a href="http://forum.tools4games.com/viewtopic.php?f=32&t=81" target="_blank">here</a>.
  </p>
  
</div>

<script type="text/javascript">
window.addEvent('domready', function()
{
  $('registerSuccess').hide();
  $('registerFail').hide();
  $('registerFailName').hide();
  
  $('register').addEvent('click', function()
  {
    $('registerFailName').hide();
    $$('table.input input').removeClass('error');
    $('register').disabled = true;
    
    var username = $('username').get('value');
    var pwd = $('password').get('value');
    var pwd2 = $('password2').get('value');
    var mail = $('mail').get('value');
    
    if(username == '')
    {
      $('username').addClass('error');
      $('register').disabled = false;
      return;
    }
    
    if(mail == '' || !/^\S+@[a-z0-9\-]+\.\w+$/i.test(mail))
    {
      $('mail').addClass('error');
      $('register').disabled = false;
      return;
    }
    
    if(pwd != pwd2 || pwd == '' || pwd2 == '')
    {
      $('password').addClass('error');
      $('password2').addClass('error');
      $('register').disabled = false;
      return;
    }
    
    new Request.JSON
    ({
      url: 'doRegister',
      onSuccess: function(response)
      {
        $('register').disabled = false;
        
        if(response.state)
        { // Success
          $('registerTable').dispose();
          $('registerSuccess').show();
        }
        else if(response.nameUnavailable)
        { // Username or E-mail is unavailable
          $('registerFailName').show();
        }
        else
        { // Error
          $('registerTable').dispose();
          $('registerFail').show();
        }
      }
    }).post('username=' + encodeURIComponent(username) + '&pwd=' + encodeURIComponent(pwd) + '&mail=' + encodeURIComponent(mail));
  });
});
</script>
