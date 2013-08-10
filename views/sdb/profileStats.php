<div class="sub extended">
  <h3><?php echo $data->profile->name ?></h3>
  <div style="float: left">
    <?php
      $links = array();
      $kits = array
      (
        0 => 'Recon',
        1 => 'Assault',
        2 => 'Medic',
        3 => 'Engineer'
      );
    
      while($link = $data->profiles->fetch())
      {
        $links[] = '<a href="?soldierId=' . $link->soldierId . '">' . $link->name  . ' [' . $kits[$link->kit] . ']</a>';
      }
    
      echo implode(' | ', $links);
    ?>
  </div>
  <div style="float: right">
    View: 
    <a href="#" onclick="t4g.url.action = 'profileCharts'; t4g.url.redirect()">Profile Charts</a> | 
    <a href="#" onclick="t4g.url.action = 'profileStats'; t4g.url.redirect()">Detailed Stats</a>
  </div>
</div>
 
<?php
  $stats = array();
  
  while($stat = $data->stats->fetch())
  {
    $stats[$stat->date] = $stat;
  }
?>


<div class="sub extended">
  <table class="data" style="width: 100%">
    <thead>
      <th style="width: 15%">Date</th>
      <?php foreach($stats as $date => $stat): ?>
      <th><?php echo date('d.m.Y', $date) ?></th>
      <?php endforeach ?>
    </thead>
    <tbody>
    <?php foreach($data->keys as $key => $label): ?>
    <tr>
      <td style=""><?php echo $label ?></td>
      <?php foreach($stats as $date => $stat):
          $cb = $data->callbacks['default'];
      
          if(array_key_exists($key, $data->callbacks))
          {
            $cb = $data->callbacks[$key];
          }
      ?>
      <td><?php echo $cb($stat->{$key}) ?></td>
      <?php endforeach ?>
    </tr>
    <?php endforeach ?>
    </tbody>
  </table>
</div>