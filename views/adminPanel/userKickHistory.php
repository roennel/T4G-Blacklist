<div class="sub extended">

  <h2>User Kick History for '<?php echo $data->user ?>'</h2>
  
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th style="width: 120px">Date</th>
        <th>Server</th>
      </tr>
    </thead>
    <tbody>
      <?php while($item = $data->kicks->fetch()): 
          
          $server = alxDatabaseManager::query("SELECT serverId, name FROM servers WHERE serverId = '{$item->serverId}' LIMIT 1")->fetch();
          
          $name = @$server->name;
          
          if(@$server->serverId <= 0)
          {
            $name = 'Deleted Server';
          }
        ?>
      <tr>
        <td><?php echo date('d.m.Y H:i:s', $item->date) ?></td>
        <td><?php echo $name ?></td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
  
</div>
