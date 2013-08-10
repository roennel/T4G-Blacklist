<div class="sub extended" style="text-align: center;margin-bottom: 20px">
  <span style="color: #00bb00; font-size: 16pt">T4G Blacklist is up and running.</span>
</div>

<?php

$f = function($v)
{
  return number_format($v, 0, '.', '\'');
};

?>

<h2 style="margin-left: 10px">Matches found in Database (Kicks):</h2>

<?php $w = '28.5%;margin-bottom:30px' ?>

<div class="sub" style="width: <?php echo $w ?>">
  
  <h2>Today</h2>
  
  <table class="data" style="width: 100%">
    <tbody>
      <?php $i = 0; while($item = $data->today->fetch()): if(empty($item->label)) continue; ?>
      <tr>
        <td><?php echo $item->label ?></td>
        <td style="width: 20%"><?php echo $f($item->kicks) ?></td>
      </tr>
      <?php $i+= $item->kicks; endwhile ?>
      <tr>
        <td>Total</td>
        <td style="width: 20%"><?php echo $f($i) ?></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="sub" style="width: <?php echo $w ?>">
  
  <h2>Month</h2>
  
  <table class="data" style="width: 100%">
    <tbody>
      <?php $i = 0; while($item = $data->month->fetch()): if(empty($item->label)) continue;  ?>
      <tr>
        <td><?php echo $item->label ?></td>
        <td style="width: 20%"><?php echo $f($item->kicks) ?></td>
      </tr>
      <?php $i+= $item->kicks; endwhile ?>
      <tr>
        <td>Total</td>
        <td style="width: 20%"><?php echo $f($i) ?></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="sub" style="width: <?php echo $w ?>">
  
  <h2>Total</h2>
  
  <table class="data" style="width: 100%">
    <tbody>
      <?php $i = 0; while($item = $data->total->fetch()): if(empty($item->label)) continue;  ?>
      <tr>
        <td><?php echo $item->label ?></td>
        <td style="width: 20%"><?php echo $f($item->kicks) ?></td>
      </tr>
      <?php $i+= $item->kicks; endwhile ?>
      <tr>
        <td>Total</td>
        <td style="width: 20%"><?php echo $f($i) ?></td>
      </tr>
    </tbody>
  </table>
</div>


<h2 style="margin-left: 10px;">Temporary Glitch Protection Kicks:</h2>

<?php $w = '28.5%;margin-bottom:30px' ?>

<div class="sub" style="width: <?php echo $w ?>">
  
  <h2>Today</h2>
  
  <table class="data" style="width: 100%">
    <tbody>
      <tr>
        <td>Level -1</td>
        <td style="width: 20%"><?php echo $f($data->todayG) ?></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="sub" style="width: <?php echo $w ?>">
  
  <h2>Month</h2>
  
  <table class="data" style="width: 100%">
    <tbody>
      <tr>
        <td>Level -1</td>
        <td style="width: 20%"><?php echo $f($data->monthG) ?></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="sub" style="width: <?php echo $w ?>">
  
  <h2>Total</h2>
  
  <table class="data" style="width: 100%">
    <tbody>
      <tr>
        <td>Level -1</td>
        <td style="width: 20%"><?php echo $f($data->totalG) ?></td>
      </tr>
    </tbody>
  </table>
</div>


<h2 style="margin-left: 10px">Bans:</h2>

<?php $w = '28.5%' ?>

<div class="sub" style="width: <?php echo $w ?>">
  
  <h2>Today</h2>
  
  <table class="data" style="width: 100%">
    <tbody>
      <?php $i = 0; while($item = $data->todayB->fetch()): if(empty($item->label)) continue; ?>
      <tr>
        <td><?php echo $item->label ?></td>
        <td style="width: 20%"><?php echo $f($item->bans) ?></td>
      </tr>
      <?php $i+= $item->bans; endwhile ?>
      <tr>
        <td>Total</td>
        <td style="width: 20%"><?php echo $f($i) ?></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="sub" style="width: <?php echo $w ?>">
  
  <h2>Month</h2>
  
  <table class="data" style="width: 100%">
    <tbody>
      <?php $i = 0; while($item = $data->monthB->fetch()): if(empty($item->label)) continue;  ?>
      <tr>
        <td><?php echo $item->label ?></td>
        <td style="width: 20%"><?php echo $f($item->bans) ?></td>
      </tr>
      <?php $i+= $item->bans; endwhile ?>
      <tr>
        <td>Total</td>
        <td style="width: 20%"><?php echo $f($i) ?></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="sub" style="width: <?php echo $w ?>">
  
  <h2>Total</h2>
  
  <table class="data" style="width: 100%">
    <tbody>
      <?php $i = 0; while($item = $data->totalB->fetch()): if(empty($item->label)) continue;  ?>
      <tr>
        <td><?php echo $item->label ?></td>
        <td style="width: 20%"><?php echo $f($item->bans) ?></td>
      </tr>
      <?php $i+= $item->bans; endwhile ?>
      <tr>
        <td>Total</td>
        <td style="width: 20%"><?php echo $f($i) ?></td>
      </tr>
    </tbody>
  </table>
</div>

