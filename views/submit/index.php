<div class="sub extended" id="appealInfo">
  
   <?php t('submitIntro') ?> 
  
   <br /><br />
   <span style="color: #bb0000; font-size: 12pt; font-weight: bold">
    Use something like <a href="http://imgur.com/" target="_blank">imgur.com</a> to upload PunkBuster Screenshots, we need the original PNG File to verify it.
  </span>
</div>

<div class="sub extended" id="existingSubmission">
  <h2 style="color: #FFA500">Existing Submission</h2>
  
  There is already a Submission pending for that Player for "<span id="existingSubType"></span>".
  <span id="addEvidenceText">If you have anymore Evidence for the Case, <a href="#" id="addEvidenceNext">click here</a> to submit it.
  <br />
  Please don't submit if you rely solely on Stats, Thank You.</span>
  <span id="addEvidenceText2">If you have anymore Evidence for the Case, let us know using <a href="http://forum.tools4games.com/viewforum.php?f=32">our T4G Forums</a>.
  </span>
</div>

<div class="sub extended" id="existingBan">
  <h2 style="color: red">Existing Blacklist Entry</h2>
  
  There is already an existing Blacklist Entry for that Player for "<span id="existingBanType"></span>".
  <span id="existingBanSubmit"><br /><br />If you have enough evidence to submit the Player for the Cheating list, <a href="#" id="existingBanNext">click here</a>.
  </span>
</div>

<div class="sub extended" id="appealError">
  <h2 style="color: red">Error</h2>
  <span id="appealErrorText"></span>
</div>

<div class="sub extended" id="appealState">
  <h2>Status</h2>
  <span id="appealStateText">
    Your Submission has been added. <a href="" id="appealStateLink"></a><br /><br />
    Thank You.
  </span>
</div>

<div class="sub extended" id="appealState2">
  <h2>Status</h2>
  <span id="appealStateText2">
    The Evidence has been added to the existing Submission. <a href="" id="appealStateLink2"></a><br /><br />
    Thank You.
  </span>
</div>

<div class="sub extended" id="appealForm">
  
  <table class="data" style="width: 100%" id="appealStep1">
    <thead>
      <tr>
        <th><b style="font-weight: bold !important">Suspected</b> Players Profile Page (like http://battlefield.play4free.com/en/profile/2381427693)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><input type="text" id="targetProfileUrl" style="width: 100%" value="http://battlefield.play4free.com/en/profile/<?php echo htmlspecialchars(@$_GET['nucleusId']); ?>" onfocus="this.select();" /></td>
      </tr>
      <tr>
        <td style="text-align: right"><input type="button" value="<?php t('next') ?>" id="appealStepNext1" /></td>
      </tr>
    </tbody>
  </table>
  
  <table class="data" style="width: 100%" id="appealStep2">
    <thead>
      <tr>
        <th colspan="99" id="submitHeader">Your Submission</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Suspected Player</td>
        <td id="targetPlayerNames"></td>
      </tr>
      <tr>
        <td><b style="font-weight: bold !important">Your</b> Profile Page</td>
        <td><input type="text" id="submitSourceProfile" value="http://battlefield.play4free.com/en/profile/<?php echo @$_SESSION['sourceNucleusId'] ?: @$_GET['sourceNucleusId'] ?>" onfocus="this.select();" style="width: 100%" /></td>
      </tr>
      <tr class="newSubField">
        <td>Your E-Mail</td>
        <td><input type="text" id="submitSourceMail" style="width: 100%" value="<?php echo @$_SESSION['sourceMail'] ?: @getUser(getUserId())->mail ?>" /></td>
      </tr>
      <tr class="newSubField">
        <td><?php t('type') ?></td>
        <td>
          <select id="submitType">
            <option value="" selected="selected"><?php t('pleaseSelect') ?></option>
            <option value="ch">Cheating</option>
            <option value="gl">Glitching</option>
            <option value="sp">Stats Padding</option>
            <option value="st">Strict ToS Enforcement</option>
          </select>
        </td>
      </tr>
      <tr>
        <td style="vertical-align: top"><?php t('msg') ?></td>
        <td>
          <textarea style="width: 100%; height: 300px" id="submitText" value=""><?php echo htmlspecialchars(@$_GET['message']); ?></textarea>
        </td>
      </tr>
      <tr>
        <td colspan="99" style="text-align: right"><input type="button" value="<?php t('submit') ?>" id="appealStepNext2" /></td>
      </tr>
    </tbody>
  </table>

</div>

<div class="sub extended" id="preview">
Loading..
</div>

<?php
  if(!isset($_SESSION['csrf_token']) or $_SESSION['csrf_token_time'] <= (time()-3600))
  {
    $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
    $_SESSION['csrf_token_time'] = time();
  }
?>


<script type="text/javascript">
$('appealError').hide();
$('appealStep2').hide();
$('appealState').hide();
$('appealState2').hide();
$('existingSubmission').hide();
$('existingBan').hide();
$('preview').hide();

var banId;
var targetNucleusId;
var addEvidence = false;

window.addEvent('domready', function()
{
  var nextStep = function()
  {
    $('appealError').hide();
    $('appealState2').hide();
    $('existingSubmission').hide();
    $('existingBan').hide();
    
    $('addEvidenceText').hide();
    $('addEvidenceText2').hide();
    $('existingBanSubmit').hide();
    
    var spl = $('targetProfileUrl').get('value').split('/');
    var nucleusId = spl.length == 7 ? spl[spl.length-2] : spl[spl.length-1];
    
    if(nucleusId == '' || nucleusId == '/')
    {
      $('appealError').show();
      $('appealErrorText').set('text', 'Profile does not exist.');
      return;
    }
    
    targetNucleusId = nucleusId;
    
    new Request.JSON
    ({
      url: 'submit/checkPlayerBan',
      onSuccess: function(response)
      {
        if(!response.valid)
        {
          $('appealError').show();
          $('appealErrorText').set('text', 'Profile does not exist.');
          
          return;  
        }
        
        $('targetPlayerNames').set('text', response.names.join(' / '));
        
        var valid = true;
        
        if(response.existingSubmission)
        {
          $('existingSubType').set('text', response.submissionLabel);
          $('existingSubmission').show();
          
          var text = (response.addEvidence) ? $('addEvidenceText') : $('addEvidenceText2');
          text.show();
          
          valid = false;
        }
        
        if(response.recentlySubmitted)
        {
          $('appealError').show();
          $('appealErrorText').set('text', 'This Profile was reviewed less than a week ago by Blacklist Staff. Please wait for a week before submitting again.');
          
          valid = false;
        }
        
        if(response.existingBan)
        {
          $('existingBanType').set('text', response.banLabel);
          $('existingBan').show();
          
          if(response.allowResubmit && !response.existingSubmission && !response.recentlySubmitted)
          {
            $('existingBanSubmit').show();
          }
          
          valid = false;
        }
        
        if(valid)
        {
          $('appealStep1').hide();
          $('appealStep2').show();
          
          loadPreview(nucleusId);
        }
      }
    }).get('nucleusId=' + nucleusId);
  };
  
  var loadPreview = function(nucleusId)
  {
    <?php if(isMod()): ?>
    $('preview').load('submit/preview?nucleusId=' + nucleusId);
    $('preview').show();
    <?php endif; ?>
  };
  
  $('appealStepNext2').addEvent('click', function()
  {
    $('appealError').hide();
    $('appealStep2').getElements('input, select, textarea').removeClass('error');
    
    var submitType = $('submitType').get('value');
    var sourceProfile = $('submitSourceProfile').get('value');
    var sourceMail = $('submitSourceMail').get('value');
    var submitText = $('submitText').get('value');
    
    // submitText = submitText.replace('+', '[__]').replace('#', '[___]').replace('&', '[_]');
    submitText = encodeURIComponent(submitText);
    
    var spl = $('submitSourceProfile').get('value').split('/');
    var nucleusId = spl.length == 7 ? spl[spl.length-2] : spl[spl.length-1];

    if(nucleusId == '' || nucleusId == '/')
    {
      $('appealError').show();
      $('appealErrorText').set('text', 'Profile does not exist.');
      return;
    }
    
    var sourceNucleusId = nucleusId;
    
    if(sourceNucleusId == targetNucleusId)
    {
      $('appealError').show();
      $('appealErrorText').set('text', 'Please enter a different Profile ID for "Your Profile Page" field.');
      return;
    }
    
    if(submitText == '')
    {
      $('submitText').addClass('error');
      return;
    }
    
    if(!addEvidence)
    {
      if(submitType == '')
      {
        $('submitType').addClass('error');
        return;
      }
      
      if(sourceMail == '' || !sourceMail.contains('@'))
      {
        $('submitSourceMail').addClass('error');
        return;
      }
      
      $('appealStepNext2').hide();
      
      new Request.JSON
      ({
        url: 'submit/submitData',
        onSuccess: function(response)
        {
          if(response.error){
            var msg = '';
            
            switch(response.error)
            {
              case 'csrf_mismatch':
                msg = 'CSRF Attack Detected.';
                break;
              case 'flood_protection':
                msg = 'Flood Protection Active.';
                break;
              default:
                msg = 'Error. Please refresh this page and try again.';
            }
            
            $('appealErrorText').set('text', msg);
            $('appealError').show();
            return;  
          }
          
          if(response.link)
          {
            $('appealStateLink').set('href', '.' + response.link);
            $('appealStateLink').set('text', '#' + response.ticket + '');
          }
          
          $('appealStep2').hide();
          $('appealForm').hide();
          $('appealInfo').hide();
          $('preview').hide();
          $('appealState').show();
        }
      }).post('targetNucleusId=' + targetNucleusId + '&sourceNucleusId=' + sourceNucleusId + '&sourceMail=' + sourceMail + '&type=' + submitType + '&csrf_token=<?php echo $_SESSION['csrf_token'] ?>&msg=' + submitText);
    }
    else
    {
      $('appealStepNext2').hide();
      
      new Request.JSON
      ({
        url: 'submit/addEvidence',
        onSuccess: function(response)
        {
          if(response.error){
            var msg = '';
            
            switch(response.error)
            {
              case 'csrf_mismatch':
                msg = 'CSRF Attack Detected.';
                break;
              case 'flood_protection':
                msg = 'Flood Protection Active.';
                break;
              default:
                msg = 'Error. Please refresh this page and try again.';
            }
            
            $('appealErrorText').set('text', msg);
            $('appealError').show();
            return;  
          }
          
          if(response.link)
          {
            $('appealStateLink2').set('href', '.' + response.link);
            $('appealStateLink2').set('text', '#' + response.ticket + '');
          }
          
          $('existingSubmission').hide();
          $('existingBan').hide();
          $('appealStep2').hide();
          $('appealForm').hide();
          $('appealInfo').hide();
          $('preview').hide();
          $('appealState2').show();
        }
      }).post('targetNucleusId=' + targetNucleusId + '&sourceNucleusId=' + sourceNucleusId + '&csrf_token=<?php echo $_SESSION['csrf_token'] ?>&msg=' + submitText);
    }
  });
  
  $('existingBanNext').addEvent('click', function()
  {
    $('existingBan').hide();
    
    $('submitType').set('value', 'ch');
    $('submitType').disabled = true;
    
    $('appealStep1').hide();
    $('appealStep2').show();
    
    loadPreview(targetNucleusId);
    
    return false;
  });
  
  $('addEvidenceNext').addEvent('click', function()
  {
    addEvidence = true;
    
    $('submitHeader').set('text', 'Additional Evidence');
    $$('.newSubField').hide();
    
    $('appealStep1').hide();
    $('appealStep2').show();
    
    loadPreview(targetNucleusId);
    
    return false;
  });
  
  $('appealStepNext1').addEvent('click', nextStep);
  
  <?php if(@$_GET['nucleusId']): ?>
  nextStep();
  <?php endif ?>
  
  <?php if(@$_GET['type']): ?>
  $('submitType').set('value', <?php echo json_encode($_GET['type']); ?>);
  <?php endif ?>
});
</script>
