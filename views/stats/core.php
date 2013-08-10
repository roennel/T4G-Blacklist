<div class="sub extended">
  
  <div>
    <label>Sort</label>
    <select id="key">
      <option value="">All</option>
      <?php
        foreach($data->keys as $key => $label)
        {
          echo "<option value=\"{$key}\">{$label}</option>";
        }
      ?>
    </select>
  </div>
  <?php $fs = '8pt' ?>
  <table class="data" style="margin-top: 10px;width: 100%">
    <thead>
      <tr>
        <th style="font-size: <?php echo $fs ?>">Soldier</th>
        <?php foreach($data->keys as $key => $label): ?>
        <th style="font-size: <?php echo $fs ?>"><?php echo $label ?></th>
        <?php endforeach ?>
      </tr>
    </thead>
    <tbody>
      <?php while($core = $data->core->fetch()): ?>
      <tr>
        <td style="font-size: <?php echo $fs ?>"><?php echo $core->name ?></td>
        <?php foreach($data->keys as $key => $label): 
          
          $cb = $data->callbacks['default'];
        
          if(array_key_exists($key, $data->callbacks))
          {
            $cb = $data->callbacks[$key];
          }
          
          ?>
        <td style="font-size: <?php echo $fs ?>"><?php echo $cb($core->{$key}) ?></td>
        <?php endforeach ?>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
  
</div>

<script>
window.addEvent('domready', function()
{
  <?php if($data->key): ?>
  $('key').set('value', '<?php echo $data->key ?>');
  <?php endif ?>
  
  $('key').addEvent('change', function()
  {
    t4g.url.query.set('key', this.get('value'));
    t4g.url.redirect();
  });
});
</script>
