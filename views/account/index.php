<div class="sub extended">

  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th colspan="2">Account Details</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="width:30%">User Name</td>
        <td>
          <?= htmlspecialchars($data->user->username); ?>
        </td>
      </tr>
      
      <tr>
        <td>Type</td>
        <td><?= ucfirst($data->user->type); ?></td>
      </tr>
      
      <tr>
        <td>Email</td>
        <td><?= htmlspecialchars($data->user->mail); ?></td>
      </tr>
      
      <tr>
        <td>Clan / Community</td>
        <td>
          <select id="clan">
            <option value="0">Unspecified</option>
            <?php
            while($item = $data->clans->fetch()):
              $disabled = $item->editable != '1' ? 'disabled="disabled"' : '';
              $selected = $item->clanId == $data->user->clanId ? 'selected="selected"' : '';
            ?>
              <option <?= $disabled ?> <?= $selected ?> value="<?=$item->clanId?>"><?=$item->label?></option>
            <? endwhile; ?>
          </select>
          <span class="clanStatus success"> Updated!</span>
          <span class="clanStatus fail"> Error!</span>
        </td>
      </tr>

      <tr>
        <td>Country</td>
        <td>
          <select id="country">
            <option value="">Unspecified</option>
            <?php
            foreach($GLOBALS['countries'] as $countryCode => $countryName):
              $selected = $countryCode == $data->user->country ? 'selected="selected"' : '';
            ?>
              <option <?= $selected ?> value="<?=$countryCode?>"><?=$countryName ?></option>
            <? endforeach; ?>
          </select>
          <span class="countryStatus success"> Updated!</span>
          <span class="countryStatus fail"> Error!</span>
        </td>
      </tr>
      
      <tr>
        <td>Join Date</td>
        <td><?= date('d.m.Y H:i', $data->user->joined); ?></td>
      </tr>
      
      <tr>
        <td>Servers</td>
        <td><a href="#" onclick="t4g.url.controller='admin'; t4g.url.action='servers'; t4g.url.redirect()"><?= $data->serverCount; ?></a></td>
      </tr>
      
      <tr>
        <td>Submissions</td>
        <td>
        <?php
          if($data->subCount===false)
          { ?>
            <span style="color: #aaa">Add your BF Profiles to see the count.</span>
          <?php
          }else{
            echo $data->subCount;
          }
        ?></td>
      </tr>
    </tbody>
  </table>
  
  <table class="data" style="width: 100%; margin-top: 20px">
    <thead>
      <tr>
        <th colspan="2">Change Password</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="width:30%">Current Password</td>
        <td><input type="password" size="20" id="oldPass" /></td>
      </tr>
      
      <tr>
        <td>New Password</td>
        <td><input type="password" size="20" id="newPass" /></td>
      </tr>
      
      <tr>
        <td colspan="2">
          <span class="pwdStatus success">Your password has been updated successfully.</span>
          <span class="pwdStatus fail">Please check the password again.</span>
          <input type="button" style="float: right" id="updatePass" value="Update" />
        </td>
      </tr>
    </tbody>
  </table>
  
  <table class="data" style="width: 100%; margin-top: 20px">
    <thead>
      <tr>
        <th colspan="2">BF Profiles</th>
      </tr>
    </thead>
    <tbody>
    <?php
    foreach($data->user->names as $nid => $names): ?>
      <tr style="width:30%">
        <td><?= $names ?></td>
        <td><a href="http://battlefield.play4free.com/en/profile/<?= $nid ?>">http://battlefield.play4free.com/en/profile/<?= $nid ?></a></td>
      </tr>
    <?
      endforeach;
    ?>
      <tr>
        <td colspan="2">
          <span class="addProfileStatus success">Your profile has been added successfully.</span>
          <span class="addProfileStatus fail">Error: Profile does not exist.</span>
          <input type="button" style="float: right" id="addProfile" value="Add" />
        </td>
      </tr>
    </tbody>
  </table>
  
</div>


<script type="text/javascript">
  $$('.pwdStatus').hide();
  $$('.clanStatus').hide();
  $$('.countryStatus').hide();
  $$('.addProfileStatus').hide();
  
  $('addProfile').addEvent('click', function(e)
  {
    $$('.addProfileStatus').hide();
    
    var text = prompt('Enter Nucleus Id or Profile URL here:\ne.g. http://battlefield.play4free.com/en/profile/123456789', '') || '';
    var match = text.match(/^(?:(\d+)|.*\/profile\/(\d+).*)$/);
    
    if(match)
    {
      var nucleusId = match[1] || match[2];
      
      new Request.JSON
      ({
        url: 'account/addNucleusId',
        onSuccess: function(response)
        {
          if(response.success)
          {
            t4g.url.redirect();
          }
          else
          {
            $$('.addProfileStatus.fail').show();
          }
        }
      }).get('userId=<?= $data->userId ?>&nucleusId=' + nucleusId);
    }
  });
  
  $('updatePass').addEvent('click', function(e)
  {
    $$('.pwdStatus').hide();
    $('updatePass').disabled = true;
    
    var oldPass = $('oldPass'), newPass = $('newPass');
    
    if(!validatePass(oldPass, newPass))
    {
      $('updatePass').disabled = false;
      return;
    }
    
    new Request.JSON
    ({
      url: 'account/changePwd',
      onSuccess: function(response)
      {
        $('updatePass').disabled = false;
        
        if(response.success)
        {
          $$([oldPass, newPass]).set('value', '');
          $$('.pwdStatus.success').show();
        }
        else
        {
          oldPass.addClass('error');
          oldPass.focus();
          oldPass.select();
          $$('.pwdStatus.fail').show();
        }
      }
    }).post({userId: '<?= $data->userId ?>', oldPass: oldPass.value, newPass: newPass.value});
  });
  
  $('clan').addEvent('change', function(e)
  {
    $$('.clanStatus').hide();
    $('clan').disabled = true;
    
    new Request.JSON
    ({
      url: 'account/setClanId',
      onSuccess: function(response)
      {
        $('clan').disabled = false;
        
        if(response.success)
          $$('.clanStatus.success').show();
        else
        {
          $('clan').value = '0';
          $$('.clanStatus.fail').show();
        }
        
        (function(){ $$('.clanStatus').hide(); }).delay(3000);
      }
    }).get({userId: '<?= $data->userId ?>', clanId: $('clan').value});
  });
  
  $('country').addEvent('change', function(e)
  {
    $$('.countryStatus').hide();
    $('country').disabled = true;
    
    new Request.JSON
    ({
      url: 'account/setCountry',
      onSuccess: function(response)
      {
        $('country').disabled = false;
        
        if(response.success)
          $$('.countryStatus.success').show();
        else
        {
          $('country').value = '';
          $$('.countryStatus.fail').show();
        }
        
        (function(){ $$('.countryStatus').hide(); }).delay(3000);
      }
    }).get({userId: '<?= $data->userId ?>', country: $('country').value});
  });
  
  function validatePass(oldPass, newPass)
  {
    var valid = true;
    
    $$([oldPass, newPass]).removeClass('error');
    
    if(!oldPass.value)
    {
      oldPass.addClass('error');
      valid = false;
    }
    
    if(!newPass.value)
    {
      newPass.addClass('error');
      valid = false;
    }
    
    if(oldPass.value == newPass.value)
    {
      $$([oldPass, newPass]).addClass('error');
      valid = false;
    }
    
    return valid;
  }
</script>