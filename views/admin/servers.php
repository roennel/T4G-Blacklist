<div class="sub extended">

  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th><?php t('name') ?></th>
        <th><?php t('ip') ?></th>
        <th><?php t('rconPort') ?></th>
        <th>Kicks Executed</th>
        <th colspan="2">&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($data->servers as $server): 
        
        $c = alxDatabaseManager::query("SELECT COUNT(*) AS c FROM kickLog WHERE serverId = '{$server->serverId}'")->fetch();
        
        $t = (time()-$server->lastOnline) / 86400;
        $t = number_format($t, 1, '.', '\'');
        
        ?>
      <tr>
        <td><?php echo $server->name ?></td>
        <td><?php echo $server->ip ?></td>
        <td><?php echo $server->port ?></td>
        <td><?php echo number_format($c->c, 0, '.', '\'') ?></td>
        <td style="text-align: right">
          <?php if(@$_SESSION['debug']): ?>
            <input class="plugins" type="button" value="Plugins" serverId="<?php echo $server->serverId ?>" />
          <?php endif ?>
            <input class="kickLog" type="button" value="Kick Log" serverId="<?php echo $server->serverId ?>" />
            <input class="editServer" type="button" value="<?php t('edit') ?>" serverId="<?php echo $server->serverId ?>" />
            <input class="deleteServer" type="button" value="<?php t('delete') ?>" serverId="<?php echo $server->serverId ?>" />
        </td>
        <?php if($server->disabled): ?>
        <td style="color: red">Error: Timeout / Disabled (<?=$t ?>d)</td>
        <?php elseif($server->online == '0'): ?>
        <td style="color: red">Error: Timeout (<?=$t ?>d)</td>
        <?php elseif($server->noLogin == '1'): ?>
        <td style="color: red">Error: Can't Login</td>
        <?php else: ?>
        <td></td>
        <?php endif ?>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<script type="text/javascript">
window.addEvent('domready', function()
{
  $$('input.plugins').each(function(item)
  {
    item.addEvent('click', function()
    {
      t4g.url.action = 'plugins';
      t4g.url.query.set('serverId', this.get('serverId'));
      
      t4g.url.redirect();
    });
  });
  
  $$('input.kickLog').each(function(item)
  {
    item.addEvent('click', function()
    {
      t4g.url.action = 'kickLog';
      t4g.url.query.set('serverId', this.get('serverId'));
      
      t4g.url.redirect();
    });
  });
  
  $$('input.editServer').each(function(item)
  {
    item.addEvent('click', function()
    {
      t4g.url.action = 'editServer';
      t4g.url.query.set('serverId', this.get('serverId'));
      
      t4g.url.redirect();
    });
  });
  
  $$('input.deleteServer').each(function(item)
  {
    item.addEvent('click', function()
    {
      var conf = confirm('<?php t('areYouSure') ?>');
      
      if(!conf) return;
      
      var serverId = this.get('serverId');
      
      new Request.JSON
      ({
        url: 'deleteServer',
        onSuccess: function(response)
        {
          t4g.url.redirect();
        }
      }).post('serverId=' + serverId);
    });
  });
});
</script>
