<div class="sub extended">
  
  <p>
    <?php t('submitIntro') ?> 
  </p>
  
  <span style="color: #bb0000; font-size: 12pt; font-weight: bold">
    Use something like <a href="http://imgur.com/" target="_blank">imgur.com</a> to upload PunkBuster Screenshots, we need the original PNG File to verify it.
  </span>
  
</div>

<div class="sub clear">
  
  <h2><?php t('submitReport') ?></h2>
  
  <table class="input" style="width: 600px" id="submitReport">
    <tbody>
      <tr>
        <td><?php t('yourProfileUrl') ?></td>
        <td>
          <input type="text" id="yourProfileUrl" value="http://battlefield.play4free.com/en/profile/" onfocus="this.select()" />
        </td>
      </tr>
      <tr>
        <td><?php t('yourMail') ?></td>
        <td>
          <input type="text" id="sourceMail" value="" />
        </td>
      </tr>
      <tr>
        <td><?php t('targetProfileUrl') ?></td>
        <td>
          <input type="text" id="targetProfileUrl" value="http://battlefield.play4free.com/en/profile/" onfocus="this.select()" />
        </td>
      </tr>
      <tr>
        <td><?php t('type') ?></td>
        <td>
          <select id="type">
            <option value=""><?php t('pleaseSelect') ?></option>
            <option value="ch" selected="selected">Cheating</option>
            <option value="gl">Glitching</option>
            <option value="sp">Stats Padding</option>
          </select>
        </td>
      </tr>
      <tr>
        <td style="vertical-align: top">
          <?php t('msg') ?>
        </td>
        <td>
          <textarea style="" id="msg"></textarea>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <input type="button" id="submit" value="<?php t('submit') ?>" />
        </td>
      </tr>
    </tbody>
  </table>
</div>

<div class="sub" style="width: 280px">
  
  <h2>Status</h2>
  
  <div id="submitState">Please fill out the Form.</div>
  
</div>

<script type="text/javascript">

window.addEvent('domready', function()
{
  var addToState = function(msg)
  {
    $('submitState').set('html', $('submitState').get('html') + msg);
  };
  
  $('submit').addEvent('click', function()
  {
    $$('table.input input, table.input select, table.input textarea').removeClass('error');
    
    $('submitState').set('html', '');
    
    var sourceSplit = $('yourProfileUrl').get('value').split('/'); 
    var sourceNucleusId = sourceSplit.length == 7 ? sourceSplit[sourceSplit.length-2] : sourceSplit[sourceSplit.length-1];
    
    var targetSplit = $('targetProfileUrl').get('value').split('/'); 
    var targetNucleusId = targetSplit.length == 7 ? targetSplit[targetSplit.length-2] : targetSplit[targetSplit.length-1];

    var sourceMail = $('sourceMail').get('value');
    var type = $('type').get('value');
    var msg = $('msg').get('value');
    
    if(sourceNucleusId == '')
    {
      $('yourProfileUrl').addClass('error');
      return;
    }
    
    if(targetNucleusId == '')
    {
      $('targetProfileUrl').addClass('error');
      return;
    }
    
    if(sourceMail == '')
    {
      $('sourceMail').addClass('error');
      return;
    }
    
    if(type == '')
    {
      $('type').addClass('error');
      return;
    }
    
    if(msg == '')
    {
      $('msg').addClass('error');
      return;
    }
    
    addToState('Checking Source Player...<br />');
    
    // Check Source Player
    new Request.JSON
    ({
      url: 'submit/checkPlayer',
      onSuccess: function(response)
      {
        if(response.data.length > 0)
        {
          addToState('<span class="success">Success</span>');
        }
        else
        {
          addToState('<span class="fail">Failure');
          addToState('<br /><br />Submission Failed');
          $('yourProfileUrl').addClass('error');
          return;
        }
        
        addToState('<br /><br />');
        addToState('Checking Target Player...<br />');
        
        new Request.JSON
        ({
          url: 'submit/checkPlayer',
          onSuccess: function(response)
          {
            if(response.data.length > 0)
            {
              addToState('<span class="success">Success</span>');
              addToState('<br /><br />Submission Successful');
              
              var targetName = response.data[0].name;
              
              $$('#submitReport tr td input, #submitReport tr td textarea, #submitReport tr td select').each(function(item)
              {
                item.set('disabled', 'disabled');
              });
              
            }
            else
            {
              addToState('<span class="fail">Failure</span>');
              addToState('<br /><br />Submission Failed');
              $('targetProfileUrl').addClass('error');
              return;
            }
            
            new Request.JSON
            ({
              url: 'submit/submitData',
              onSuccess: function(response)
              {
                
              }
            }).post('sourceNucleusId=' + sourceNucleusId + '&sourceMail=' + sourceMail + '&targetName=' + targetName + '&targetNucleusId=' + targetNucleusId + '&type=' + type + '&msg=' + msg);
          }
        }).get('nucleusId=' + targetNucleusId);
      }
    }).get('nucleusId=' + sourceNucleusId);
    
  });
});
</script>
