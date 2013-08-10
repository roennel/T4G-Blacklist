<div class="sub extended">
  
  <h2>Bad Submitters</h2>
  
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th>Name</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($data->items as $item): ?>
      <tr>
        <td><?=$item->name ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>