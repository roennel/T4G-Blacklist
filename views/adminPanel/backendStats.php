<div class="sub extended" style="display: none">
  
  <h2>Last Chain</h2>
  
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th style="width: 500px">Server</th>
        <th>Start</th>
        <th>End</th>
        <th style="width: 50px">Duration</th>
      </tr>
    </thead>
    <tbody>
      <?php while($item = $data->chain->fetch()): 
        
        $server = alxDatabaseManager::query("SELECT name FROM servers WHERE serverId = '{$item->serverId}' LIMIT 1")->fetch();
        
        ?>
      <tr>
        <td><?php echo $server->name ?></td>
        <td><?php echo date('d.m.Y H:i:s', (int) $item->start) ?></td>
        <td><?php echo date('d.m.Y H:i:s', (int) $item->end) ?></td>
        <td><?php echo number_format($item->end - $item->start, 3) ?>s</td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
</div>

<div class="sub extended">

  <h2>Execution Log</h2>
  
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th style="width: 160px">Execution Date</th>
        <th style="width: 100px">Duration</th>
        <th>Servers</th>
        <th style="width: 100px">RAM Used</th>
        <th>Args</th>
      </tr>
    </thead>
    <tbody>
      <?php while($item = $data->executions->fetch()): ?>
      <tr>
        <td><?php echo date('d.m.Y H:i:s', $item->date) ?></td>
        <td><?php echo number_format((float) $item->duration, 3) ?>s</td>
        <td><?php echo $item->servers ?></td>
        <td><?php echo $item->ram ?> MB</td>
        <td><?php echo $item->args ?></td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
  
</div>
