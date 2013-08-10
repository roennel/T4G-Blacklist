<?php

$f = function($v, $dec=0)
{
  return number_format($v, $dec, '.', '\'');
};

?>
<div class="sub extended">
  
  <h2>Freeloader vs. Payer (including free permanent daily draw weapons)</h2>
  <table cellspacing="0" class="data" style="width: 100%">
    <thead>
      <tr>
        <th>Type</th>
        <th>Absolute</th>
        <th>Perc</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Freeloaders</td>
        <td><?php echo $f($data->all->f, 0) ?></td>
        <td><?php echo $f(($data->all->f / $data->all->total) * 100, 1) ?>%</td>
      </tr>
       <tr>
        <td>Payers</td>
        <td><?php echo $f($data->all->p, 0) ?></td>
        <td><?php echo $f(($data->all->p / $data->all->total) * 100, 1) ?>%</td>
      </tr>
       <tr>
        <td>Total</td>
        <td><?php echo $f($data->all->total, 0) ?></td>
        <td>100%</td>
      </tr>
    </tbody>
  </table>

  
  <?php /*
  <table cellspacing="0" class="data" style="width: 100%">
    <thead>
      <tr>
        <th>Player</th>
        <th>Weapons</th>
        <th>Freeloader?</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($data->data as $soldierId => $item): ?>
      <tr>
        <td><?php echo $item[0]->name ?></td>
        <td><?php echo implode('/', $item[2]) ?></td>
        <td><?php echo $item[1] ? 'Yes' : 'No' ?></td>
        <td>
          <a href="http://battlefield.play4free.com/en/profile/<?php echo $item[0]->nucleusId ?>/<?php echo $item[0]->soldierId ?>" target="_blank">LINK</a>
        </td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  */ ?>
</div>