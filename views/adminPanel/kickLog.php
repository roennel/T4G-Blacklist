<div class="sub extended">
  
  <h2>Kick Log</h2>
  
  <?php if(@$data->serverChoose): ?>
  <label>Server:</label>
  <select id="server" style="margin-bottom: 10px;margin-left: 5px">
    <option value="">All</option>
    <?php $s = alxDatabaseManager::query("SELECT * FROM servers ORDER BY name ASC"); while($server = $s->fetch()): ?>
    <option value="<?php echo $server->serverId ?>"><?php echo $server->name ?></option>
    <?php endwhile ?>
  </select>
  <?php endif ?>
  
  <br />
  
  <label>Type:</label>
  <select id="type" style="margin-bottom: 10px;margin-left: 14px">
    <option value="">All</option>
    <option value="0">Banlist</option>
    <option value="1">Glitch Protect</option>
  </select>
  
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th style="width: 400px">Server Id</th>
        
        <?php if(!@$data->noBanId): ?>
        <th style="width: 60px">Ban Id</th>
        <?php endif ?>
        
        <th>Player</th>
        <th>Banlist</th>
        <th style="width: 140px">Date</th>
        <th style="width: 100px">Options</th>
      </tr>
    </thead>
    <tbody>
      <?php while($item = $data->kicks->fetch()): 
        
        if($item->type == '1')
        {
          $ban = alxDatabaseManager::query("SELECT name FROM profiles WHERE nucleusId = '{$item->nucleusId}'")->fetch();

          $blacklist = new stdClass;
          $blacklist->label = 'Glitch Protection AutoKick';
        }
        else
        {
          $ban = alxDatabaseManager::query("SELECT p.name AS name, b.submissionId AS submissionId, b.blacklistId AS blacklistId FROM bans AS b, profiles AS p WHERE b.nucleusId = p.nucleusId AND b.banId = '{$item->banId}' LIMIT 1")->fetch();
          
          if(!@$ban) continue;
          
          $blacklist = alxDatabaseManager::query("SELECT label FROM blacklists WHERE blacklistId = '{$ban->blacklistId}' LIMIT 1")->fetch();
        }
        
        $server = alxDatabaseManager::query("SELECT name FROM servers WHERE serverId = '{$item->serverId}' LIMIT 1")->fetch();
        ?>
      <tr>
        <td><?php echo $server->name ?></td>
        
        <?php if(!@$data->noBanId): ?>
        <td><?php echo $item->banId == '0' ? '-' : '<a href="/en/modPanel/submissionDetail?submissionId='.$ban->submissionId.'" target="_blank">'.$item->banId.'</a>' ?></td>
        <?php endif ?>
        
        <td><?php echo $ban->name ?></td>
        <td><?php echo $blacklist->label ?></td>
        <td><?php echo date('d.m.Y H:i:s', $item->date) ?></td>
        <td>
          <?php if($item->type != '1'): ?>
          <a href="#" onclick="t4g.url.controller = 'adminPanel'; t4g.url.action = 'userKickHistory'; t4g.url.query.set('banId', '<?php echo $item->banId ?>'); t4g.url.redirect();">Kick History</a>
          <?php endif ?>
        </td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
</div>

<script type="text/javascript">
window.addEvent('domready', function()
{
  if($('server'))
  {
  $('server').addEvent('change', function()
  {
    t4g.url.query.set('serverId', this.get('value'));
    
    if(this.get('value') == '')
    {
      t4g.url.query.erase('serverId');
    }
    
    t4g.url.redirect();
  });
  
  $('server').set('value', '<?php echo @$_GET['serverId'] ?>');
  }
  
  <?php if(@$_GET['serverId']): ?>
  t4g.url.query.set('serverId', '<?php echo @$_GET['serverId'] ?>');
  <?php endif ?>
  
  $('type').addEvent('change', function()
  {
    t4g.url.query.set('type', this.get('value'));
    
    if(this.get('value') == '')
    {
      t4g.url.query.erase('type');
    }
    
    t4g.url.redirect();
  });
  
  $('type').set('value', '<?php echo @$_GET['type'] ?>');  
});
</script>