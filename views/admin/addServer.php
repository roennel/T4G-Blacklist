
<div class="sub extended">

  <table class="data" style="width: 100%" id="addServerStep1">
    <thead>
      <tr>
        <th><?php t('enterBookmarkLink') ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><input type="text" style="width: 100%" id="bookmarkLink" onfocus="this.select()" value="http://battlefield.play4free.com/en/bookmark/server:" /></td>
      </tr>
      <tr>
        <td style="text-align: right">
          <input type="button" id="addServer1" value="<?php t('next') ?>" />
        </td>
      </tr>
    </tbody>
  </table>
  
  <table class="data" style="width: 100%" id="addServerStep2">
    <thead>
      <tr>
        <th colspan="2"><?php t('checkServerData') ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php t('serverLocation') ?></td>
        <td>
          <select id="serverCountry">
            <?php foreach($GLOBALS['countries'] as $code => $label): ?>
            <option value="<?php echo $code ?>"><?php echo $label ?></option>
            <?php endforeach ?>
          </select>
        </td>
      </tr>
      
      <tr>
        <td><?php t('serverName') ?></td>
        <td><input type="text" id="serverName" style="width: 100%" /></td>
      </tr>
      
      <tr>
        <td><?php t('ip') ?></td>
        <td><input type="text" id="serverIp" style="width: 100%" /></td>
      </tr>
      
      <tr>
        <td><?php t('rconPort') ?></td>
        <td><input type="text" id="serverPort" style="width: 100%" /></td>
      </tr>
      
      <tr>
        <td><?php t('rconPassword') ?></td>
        <td><input type="text" id="serverPassword" style="width: 100%" /></td>
      </tr>
    </tbody>
  </table>
  
  <table class="data" style="width: 100%;margin-top: 10px" id="addServerStep2b">
    <thead>
      <tr>
        <th colspan="2"><?php t('banlists') ?></th>
      </tr>
    </thead>
    <tbody>
      <?php while($item = $data->banlists->fetch()): 
        
        $c = '';
        
        if($item->optIn == '0')
        {
          $c = ' disabled="disabled" checked="checked"';
        }
        else if(@$data->serverBanlists and in_array($item->blacklistId, $data->serverBanlists))
        {
          $c = ' checked="checked"';
        }
        
        ?>
      <tr>
        <td><?php echo $item->label ?></td>
        <td><input type="checkbox" blacklistId="<?php echo $item->blacklistId ?>" class="banlist"<?php echo $c ?> /></td>
      </tr>
      <?php endwhile ?>
      
      <tr>
        <td colspan="2" style="text-align: right">
          <input type="button" id="addServer2" value="<?php t('next') ?>" />
        </td>
      </tr>
    </tbody>
  </table>
  
</div>

<div class="sub extended" id="addServerStep3">
  <h2>Checking Server Data...</h2>
  
  <span id="addServerState"></span>
</div>

<div class="sub extended" id="addServerStepError">
  <h2>Error</h2>
  
  <span id="addServerError"></span>
</div>

<script type="text/javascript">
$('addServerStep2').hide();
$('addServerStep2b').hide();
$('addServerStep3').hide();
$('addServerStepError').hide();

var bookmarkLink;

// edit page
<?php if(@$data->server): ?>
  bookmarkLink = '<?php echo $data->server->bookmarkLink; ?>';
  $('bookmarkLink').set('value', 'http://battlefield.play4free.com/en/bookmark/server:' + bookmarkLink);
  $('bookmarkLink').set('disabled', true);
  
  $('serverCountry').set('value', <?php echo json_encode($data->server->country); ?>);
  $('serverName').set('value', <?php echo json_encode($data->server->name); ?>);
  $('serverIp').set('value', <?php echo json_encode($data->server->ip); ?>);
  $('serverPort').set('value', <?php echo json_encode($data->server->port); ?>);
  $('serverPassword').set('value', <?php echo json_encode($data->server->pwd); ?>);
  
  $('addServer2').set('value', '<?php t('update'); ?>');
  
  $('addServer1').hide();
  $('addServerStep2').show();
  $('addServerStep2b').show();
<?php endif; ?>
 
window.addEvent('domready', function()
{
  $('addServer1').addEvent('click', function()
  {
    $('addServerStepError').hide();
    
    var svm = $('bookmarkLink').get('value').match(/server:([^\/#]+)/);
    bookmarkLink = svm && svm[1];
    
    new Request.JSON
    ({
      url: 'checkServerId',
      onSuccess: function(result)
      {
        if(result.error)
        {
          $('addServerStepError').show();
          
          if(result.exists)
          {
            $('addServerError').set('text', 'Server already exists: "' + result.serverInfo.name + '"' + (result.serverInfo.offline ? ' (Offline)' : ''));
          }
          else
          {
            $('addServerError').set('text', 'Cannot find specified Server, please try again.');
          }
          
          return;
        }
        
        $('addServerStep1').hide();
        $('addServerStep2').show();
        $('addServerStep2b').show();
        $('serverCountry').set('value', result.country);
        $('serverName').set('value', result.name);
        $('serverIp').set('value', result.ip);
      }
    }).get('serverId=' + bookmarkLink);
  });
  
  $('addServer2').addEvent('click', function()
  {
    var country = $('serverCountry').get('value');
    var name = $('serverName').get('value').replace('#', '[___]').replace('+', '[__]');
    var ip = $('serverIp').get('value');
    var port = $('serverPort').get('value');
    var password = $('serverPassword').get('value').replace('#', '[___]').replace('+', '[__]');
    
    var blacklists = [];
    
    $$('input.banlist').each(function(item)
    {
      if(item.checked)
      {
        blacklists.push(item.get('blacklistId'));
      }
    });
    
    new Request.JSON
    ({
      url: 'checkServerData',
      onSuccess: function(result)
      {
        $('addServerStep3').show();
        
        if(result.valid)
        {
          $('addServerState').set('text', 'Server has successfully been added!');
          
          (function()
          {
            t4g.url.action = 'servers';
            t4g.url.redirect();
          }).delay(3000);
        }
        else
        {
          $('addServerState').set('text', 'An Error has occured, please check your Data');
        }
      }
    }).get('serverId=' + '<?php echo @$data->server ? $data->server->serverId : ''; ?>' + '&bookmarkLink=' + bookmarkLink + '&country=' + country + '&ip=' + ip + '&port=' + port + '&pwd=' + password + '&name=' + name + '&blacklists=' + blacklists.implode(';'));
    
  });
});
</script>

<!--
<div class="sub extended">

  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th><?php t('name') ?></th>
        <th><?php t('ip') ?></th>
        <th><?php t('rconPort') ?></th>
        <th><?php t('rconPassword') ?></th>
        <th><?php t('bookmarkLink') ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><input type="text" style="width: 100%" id="serverName" /></td>
        <td><input type="text" style="width: 100%" id="serverIp" /></td>
        <td><input type="text" style="width: 100%" id="serverRconPort" /></td>
        <td><input type="text" style="width: 100%" id="serverRconPwd" /></td>
        <td><input type="text" style="width: 100%" id="serverBookmarkLink" /></td>
      </tr>
      <tr>
        <td colspan="5" style="text-align: right">
          <input type="button" id="addServer" value="<?php t('addServer') ?>" />
        </td>
      </tr>
    </tbody>
  </table>
</div>

<script type="text/javascript">
window.addEvent('domready', function()
{
  $('addServer').addEvent('click', function()
  {
    $$('table.data input').removeClass('error');
    
    var serverName = $('serverName').get('value');
    var serverIp= $('serverIp').get('value');
    var serverRconPort = $('serverRconPort').get('value');
    var serverRconPwd = $('serverRconPwd').get('value');
    var serverBookmarkLink = $('serverBookmarkLink').get('value');
    
    if(serverName == '')
    {
      $('serverName').addClass('error');
      return;
    }
    
    if(serverIp == '')
    {
      $('serverIp').addClass('error');
      return;
    }
    
    if(serverRconPort == '')
    {
      $('serverRconPort').addClass('error');
      return;
    }
    
    if(serverRconPwd == '')
    {
      $('serverRconPwd').addClass('error');
      return;
    }
    
    if(serverBookmarkLink == '')
    {
      $('serverBookmarkLink').addClass('error');
      return;
    }
  
    new Request.JSON
    ({
      url: 'addServer',
      onSuccess: function(response)
      {
        if(response.state)
        {
          t4g.url.redirect();
        }
        else
        {
          t4g.url.redirect();
        }
      }
    }).post('name=' + serverName + '&ip=' + serverIp + '&port=' + serverRconPort + '&pwd=' + serverRconPwd + '&bookmarkLink=' + serverBookmarkLink);
  });
});
</script>
-->