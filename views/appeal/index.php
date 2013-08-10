<div class="sub extended" id="appealInfo">
  
  Think you for wrongly banned? Appeal here and explain your case.
  
  <br />
  <br />
  Please read <a href="http://forum.tools4games.com/viewtopic.php?f=32&t=153&p=477#p477" target="_blank">this</a> Thread before appealing, if you submit an Appeal based on something thats listed in there will only get it closed instantly. 

  <br />
  <br />
  <span style="color: #bb0000; font-size: 12pt; font-weight: bold">
   This is not the place to report a player. If you'd like to report a suspicious profile, please use the <a href="submit">Submit Page</a>.
  </span>
</div>

<div class="sub extended" id="appealError">
  <h2>Error</h2>
  <span id="appealErrorText" class="appealErrorText errorChars">Appeal messages require at least 50 characters. Please be descriptive and tell us why you're innocent. Do not submit an appeal with just "I do not hack" message as that'll likely end up in a negative result.</span>
</div>

<div class="sub extended" id="appealState">
  <h2>Status</h2>
  <span id="appealStateText">
    You're appeal has been added.<br /><br />
    As soon as it's processed you will hear from us via E-Mail.
  </span>
</div>


<div class="sub extended" id="appealForm">
  
  <table class="data" style="width: 100%" id="appealStep1">
    <thead>
      <tr>
        <th>Your Profile URL (like http://battlefield.play4free.com/en/profile/2381427693)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><input type="text" id="profileUrl" style="width: 100%" value="http://battlefield.play4free.com/en/profile/" onfocus="this.select();" /></td>
      </tr>
      <tr>
        <td style="text-align: right"><input type="button" value="<?php t('next') ?>" id="appealStepNext1" /></td>
      </tr>
    </tbody>
  </table>
  
  <div id="appealStep2">
    <?php include('appealRules.html'); ?>
    <br />
    <br />
    <div style="text-align: center" class="sub extended buttons">
      <input type="button" value="I understand and want to Appeal" id="appealStepNext2" />
    </div>
  </div>
  
  <table class="data" style="width: 100%" id="appealStep3">
    <thead>
      <tr>
        <th colspan="99">Your Appeal</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Your E-Mail</td>
        <td><input type="text" id="appealMail" style="width: 100%" /></td>
      </tr>
      <tr>
        <td style="vertical-align: top">Appeal</td>
        <td>
          <textarea style="width: 100%; height: 300px" id="appealText"></textarea>
        </td>
      </tr>
      <tr>
        <td colspan="99" style="text-align: right"><input type="button" value="<?php t('submit') ?>" id="appealStepNext3" /></td>
      </tr>
    </tbody>
  </table>

</div>

<script type="text/javascript">
$('appealError').hide();
$('appealStep2').hide();
$('appealStep3').hide();
$('appealState').hide();
var banId;

window.addEvent('domready', function()
{
  $('appealStepNext1').addEvent('click', function()
  {
    $('appealError').hide();
    $('appealState').hide();
    
    var spl = $('profileUrl').get('value').split('/');
    var nucleusId = spl.length == 7 ? spl[spl.length-2] : spl[spl.length-1];
    
    if(nucleusId == '' || nucleusId == '/')
    {
      $('appealError').show();
      $('appealErrorText').set('text', 'Profile does not exist.');
      return;
    }
    
    new Request.JSON
    ({
      url: 'appeal/checkPlayer',
      onSuccess: function(response)
      {
        if(!response.existingBan)
        {
          $('appealErrorText').set('text', 'No Ban found in Blacklist for this Profile.');
          $('appealError').show();
          
          return;
        }
        
        if(response.existingAppeal)
        {
          $('appealErrorText').set('text', 'There is already an existing Appeal for that Player.');
          $('appealError').show();
          
          return;
        }
        
        if(response.recentlyAppealed)
        {
          $('appealErrorText').set('text', 'Hold your horses, you already appealed on ' + response.lastAppealDate + ', you have to wait a bit to appeal again.');
          $('appealError').show();
          
          return;
        }
        
        banId = response.banId;
        
        $('appealStep1').hide();
        $('appealInfo').hide();
        $('appealStep2').show();
      }
    }).get('nucleusId=' + nucleusId);
  });
  
  $('appealStepNext2').addEvent('click', function()
  {
    $('appealStep2').hide();
    $('appealStep3').show();
  });
  
  $('appealStepNext3').addEvent('click', function()
  {
    $('appealError').hide();
    $$('.appealErrorText').hide();
    $('appealStep3').getElements('input, select, textarea').removeClass('error')
    
    var appealMail = $('appealMail').get('value');
    var appeal = $('appealText').get('value');
    
    if(appealMail == '' || !appealMail.contains('@'))
    {
      $('appealMail').addClass('error');
      return;
    }
    
    if(appeal == '' || appeal.length < 50)
    {
      $('appealText').addClass('error');
      $$('.appealErrorText.errorChars').show();
      $('appealError').show();
      return;
    }
    
    $('appealStepNext2').hide();

    new Request.JSON
    ({
      url: 'appeal/add',
      onSuccess: function()
      {
        $('appealStep3').hide();
        $('appealForm').hide();
        $('appealInfo').hide();
        $('appealState').show();
      }
    }).post('banId=' + banId + '&mail=' + encodeURIComponent(appealMail) + '&text=' + encodeURIComponent(appeal));
  });
});
</script>
