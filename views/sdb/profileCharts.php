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
 
<div class="sub extended">
  
  <?php foreach($data->keys as $key): 
    
    $keys = $key;
      
    if(is_array($key))
    {
      $keys = implode('&key[]=', $key);
    }
    
    ?>
  
  <img src="/img/img_profileChart.php?soldierId=<?php echo $data->soldierId ?>&key[]=<?php echo $keys ?>" />
    
  <?php endforeach ?>
</div>
