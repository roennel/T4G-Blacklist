<div class="sub extended">
  
  <h2><span id="sc"></span> / 251 Active Servers</h2>
  <h3 style="margin-top: -5px; margin-bottom: 15px;font-size: 11pt; color: #ccc"><span id="scp"></span>% of all Battlefield Play4Free Rental Servers protected</h2>
  <h3 style="float: left; margin-top: -5px; margin-bottom: 15px;font-size: 11pt; color: #ccc"><span id="sco" style="color: #00aa00"></span> Servers Online</h2>
  <h3 style="float: left;margin-top: -5px; margin-left: 5px; margin-bottom: 15px;font-size: 11pt; color: #ccc"> / <span id="sco2" style="color: #aa0000"></span> Servers Offline</h2>
  <div style="clear: both"></div>
  
  <label>Server Location:</label>
  <select id="country" style="margin-left: 10px;margin-bottom: 10px">
    <option value="">All</option>
  </select>
  
  <table class="data serverList sortable" cellspacing="0">
    <thead>
      <tr>
        <th style="cursor: pointer">Country</th>
        <th style="cursor: pointer">Server</th>
        <?php if(@$_GET['lastSeen']): ?>
        <th>Last Seen</th>
        <?php endif ?>
        <th style="cursor: pointer">Blacklists</th>
        <th style="cursor: pointer">Hacktivity</th>
        <th>Bookmark</th>
        <th>Online</th>
      </tr>
    </thead>
    <tbody>
      <?php $i=0; $o = 0; $countries = array(); $cc = array(); foreach($data->servers as $server): 
        
        if(!array_key_exists($server->country, $cc))
        {
          $cc[$server->country] = 0;
        }
        
        if(!in_array($server->country, $countries))
        {
          $countries[] = $server->country;
        }
        
        $cc[$server->country]++;
        
        if(@$_GET['country'] && $_GET['country'] != $server->country) continue;
        
        $clr = '#00aa00';
        $st = 'Online';
        
        if($server->online == '0')
        {
          $clr = '#aa0000';
          $st = 'Offline';
        }
        
        if($server->noLogin == '1')
        {
          $clr = '#aa5500';
          $st = 'Server Online, Not Protected. T4G Blacklist can\'t Login';
        }
        
        ?>
      <tr serverId="<?php echo $server->serverId ?>">
        <td><img src="http://blacklist.tools4games.com/img/flags/<?php echo strToLower($server->country) ?>.png" /></td>
        <td><?php echo $server->name ?></td>
        <?php if(@$_GET['lastSeen']): ?>
        <td><?=($server->lastOnline == 0 ? 'OFFLINE' : number_format((time()-$server->lastOnline) / 86400, 1) . 'd') ?></td>
        <?php endif ?>
        <td style="font-size: 9pt"><?php echo implode(' / ', $server->blacklists) ?></td>
        <td><?php echo $server->hacktivity ?>%</td>
        <td><a target="_blank" href="http://battlefield.play4free.com/bookmark/server:<?php echo $server->bookmarkLink ?>">Bookmark</a></td>
        <td style="text-align: center">
          <div style="margin-left: 15px;width: 10px; height: 10px; background-color: <?php echo $clr ?>" title="<?=$st ?>"></div>
        </td>
      </tr>
      <?php if($server->online == '1') { $o++; } $i++; endforeach ?>
    </tbody>
  </table>
  
  <br /><br />
  <h2>Disabled Servers (Registered but timed out for > 2 Weeks) [<?=count($data->servers2) ?>]:</h2>
  <table class="data serverList sortable" cellspacing="0">
    <thead>
      <tr>
        <th style="cursor: pointer">Country</th>
        <th style="cursor: pointer">Server</th>
        <?php if(@$_GET['lastSeen']): ?>
        <th>Last Seen</th>
        <?php endif ?>
        <th style="cursor: pointer">Blacklists</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($data->servers2 as $server): 
          
          if(!@$server) continue;
          
        ?>
      <tr serverId="<?php echo $server->serverId ?>">
        <td><img src="http://blacklist.tools4games.com/img/flags/<?php echo strToLower($server->country) ?>.png" /></td>
        <td><?php echo $server->name ?></td>
        <?php if(@$_GET['lastSeen']): ?>
        <td><?=($server->lastOnline == 0 ? 'OFFLINE' : number_format((time()-$server->lastOnline) / 86400, 1) . 'd') ?></td>
        <?php endif ?>
        <td><?php echo implode(' / ', $server->blacklists) ?></td>
       </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  
</div>

<script type="text/javascript">
window.addEvent('domready', function()
{
  $('sc').set('text', '<?php echo $i ?>');
  $('scp').set('text', '<?php echo number_format(($i / 255) * 100, 0) ?>');
  $('sco').set('text', '<?php echo number_format($o, 0) ?>');
  $('sco2').set('text', '<?php echo number_format($i - $o, 0) ?>');
  
  <?php foreach($countries as $country): ?>
  var opt = new Element('option');
  opt.set('value', '<?php echo $country ?>');
  opt.set('text', '<?php echo $GLOBALS['countries'][$country] ?> (<?=$cc[$country]?>)');
  $('country').grab(opt);
  <?php endforeach ?>

  $('country').addEvent('change', function()
  {
    t4g.url.query.set('country', this.get('value'));
    
    if(this.get('value') == '')
    {
      t4g.url.query.erase('country');
    }
    
    t4g.url.redirect();
  });
  
  $('country').set('value', '<?php echo @$_GET['country'] ?>');
});
</script>

<style type="text/css">
  table.serverList
  {
    width: 100%;
    
  }
  
  table.serverList thead tr th
  {
    text-align: left;
    font-weight: bold;
    padding: 10px;
    
    border-bottom: 1px solid #333;
    

  }
  
  table.serverList tbody tr td
  {
    padding: 10px;
    
  }
  
  table.serverList tbody tr:nth-child(odd) td
  {
  }
  
  table.serverList tbody tr td:nth-child(1),
  table.serverList thead tr th:nth-child(1)
  {
    text-align: center;
    width: 30px;
  }
  
  table.serverList tbody tr td:nth-child(4),
  table.serverList thead tr th:nth-child(4)
  {
    text-align: center;
    width: 70px;
  }
  
  table.serverList tbody tr td:nth-child(5)
  {
    width: 70px;
  }
  
</style>
