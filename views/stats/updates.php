<div class="sub extended">
  
  <table class="data" style="margin-top: 10px;width: 100%">
    <thead>
      <tr>
        <th>Date</th>
        <th>Updates</th>
      </tr>
    </thead>
    <tbody>
      <?php while($event = $data->updates->fetch()): ?>
      <tr>
        <td><?php echo date('d.m.Y', $event->date)  ?></td>
        <td><?php echo number_format($event->c, 0, '.', '\'') ?></td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
  
</div>
