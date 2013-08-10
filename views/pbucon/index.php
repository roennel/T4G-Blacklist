<div class="sub extended">
  
  <h2>PB UCon</h2>
  
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th>Date</th>
        <th>Server</th>
        <th>Message</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($data->items as $item): ?>
      <tr>
        <td style="width: 120px"><?=date('d.m.Y H:i:s', $item->date) ?></td>
        <td><?=$item->name?></td>
        <td><?=$item->msg ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>
