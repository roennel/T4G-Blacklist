<div id="resetSuccess" class="sub extended pwdStatus">
  <h2>Status</h2>
  Your password has been reset successfully. You may now log in with your new password.
</div>

<div id="resetFail" class="sub extended pwdStatus">
  An Error has occured. Verify that you've entered a valid reset key.
</div>

<div class="sub extended" id="resetBox">
  <h2>Reset Passsword</h2>
  <table class="input" style="width: 100%; margin-top: 20px">
    <tbody>
      <tr>
        <td style="width:30%">Password Reset Key</td>
        <td><input type="text" size="45" id="resetKey" value="<?= htmlspecialchars(@$_GET['key']) ?>" /></td>
      </tr>
      <tr>
        <td style="width:30%">New Password</td>
        <td><input type="password" size="20" id="newPass" /></td>
      </tr>
      
      <tr>
        <td>Confirm New Password</td>
        <td><input type="password" size="20" id="newPass2" /></td>
      </tr>
      
      <tr>
        <td colspan="2">
          <input type="button" style="float: right" id="updatePass" value="Update" />
        </td>
      </tr>
    </tbody>
  </table>
</div>

<script type="text/javascript">
  $$('.pwdStatus').hide();
  
  $('updatePass').addEvent('click', function(e)
  {
    $$('.pwdStatus').hide();
    $('updatePass').disabled = true;
    
    var key = $('resetKey').value;
    var newPass = $('newPass'), newPass2 = $('newPass2');
    
    if(!validatePass(newPass, newPass2))
    {
      $('updatePass').disabled = false;
      return;
    }
    
    new Request.JSON
    ({
      url: 'resetPassword',
      onSuccess: function(response)
      {
        $('updatePass').disabled = false;
        
        if(response.success)
        {
          $('resetBox').dispose();
          $('resetSuccess').show();
        }
        else
          $('resetFail').show();
        
        $$([newPass, newPass2]).set('value', '');  
      }
    }).post({u: '<?= @$_GET['u'] ?>', key: key, newPass: newPass.value});
  });
  
  function validatePass(newPass, newPass2)
  {
    var valid = true;
    
    $$([newPass, newPass2]).removeClass('error');
    
    if(!newPass.value)
    {
      newPass.addClass('error');
      valid = false;
    }
    
    if(!newPass2.value)
    {
      newPass2.addClass('error');
      valid = false;
    }
    
    if(newPass.value != newPass2.value)
    {
      $$([newPass, newPass2]).addClass('error');
      valid = false;
    }
    
    return valid;
  }
</script>
