<div class="sub extended">
  
  <?php
  
    $f = function($v, $dig=0)
    {
      return number_format($v, $dig, '.', '\'');
    };
  
  ?>
  
  <?php if($data->soldierId == 0): ?>
    
    <label>NucleusId</label>
    <input type="text" id="nucleusId" value="2682063259" />
    <br /><br />
    <label>SoldierId&nbsp;&nbsp;</label>
    <input type="text" id="soldierId" value="690737138" />
    <br /><br />
    <input type="button" id="send" value="Go" />
    
    <script>
      $('send').addEvent('click', function()
      {
        t4g.url.query.set('nucleusId', $('nucleusId').get('value'));
        t4g.url.query.set('soldierId', $('soldierId').get('value'));
        t4g.url.redirect();
      });
    </script>
    
  <?php else: ?>
    
    <h2><?php echo $data->profile->name ?> @ <?php echo date('d.m.Y H:i:s', $data->ts) ?></h2>
    
    <table style="width: 500px">
      <tbody>
        <tr>
          <td>Global <?php echo $data->keyLabel ?></td>
          <td><?php echo $f($data->core->c) ?></td>
        </tr>
        <tr>
          <td>Weapon <?php echo $data->keyLabel ?> Sum</td>
          <td><?php echo $f($data->weapon->c) ?></td>
        </tr>
        <tr>
          <td>Vehicle <?php echo $data->keyLabel ?> Sum</td>
          <td><?php echo $f($data->vehicle->c) ?></td>
        </tr>
        <tr>
          <td>Vehicle + Weapon <?php echo $data->keyLabel ?> Sum</td>
          <td><?php echo $f($data->vehicle->c + $data->weapon->c) ?></td>
        </tr>
        <tr>
          <td>Map <?php echo $data->keyLabel ?> Sum</td>
          <td><?php echo $f($data->map->c) ?></td>
        </tr>
        <tr>
          <td>GameMode <?php echo $data->keyLabel ?> Sum</td>
          <td><?php echo $f($data->gameMode->c) ?></td>
        </tr>
        <tr>
          <td>Error Ratio</td>
          <td><?php echo $f(1 - (($data->vehicle->c + $data->weapon->c) / $data->core->c), 3) ?>%</td>
        </tr>
      </tbody>
    </table>
    
    <table style="width: 500px;margin-top: 20px;">
      <tbody>
        <?php $t = 0; while($w = $data->weapons->fetch()): if($w->{$data->key} == 0) continue; ?>
        <tr>
          <td><?php echo $GLOBALS['weaponIds'][$w->weaponId] ?></td>
          <td><?php echo $f($w->{$data->key}) ?></td>
        </tr>
        <?php $t+= $w->{$data->key}; endwhile ?>
      </tbody>
      <tfoot>
        <tr>
          <td>Total</td>
          <td><?php echo $f($t) ?></td>
        </tr>
      </tfoot>
    </table>
    
    <table style="width: 500px;margin-top: 20px;">
      <tbody>
        <?php $t = 0; while($w = $data->vehicles->fetch()): if($w->{$data->key} == 0) continue; ?>
        <tr>
          <td><?php echo $GLOBALS['vehicleIds'][$w->vehicleId] ?></td>
          <td><?php echo $f($w->{$data->key}) ?></td>
        </tr>
        <?php $t+= $w->{$data->key}; endwhile ?>
      </tbody>
      <tfoot>
        <tr>
          <td>Total</td>
          <td><?php echo $f($t) ?></td>
        </tr>
      </tfoot>
    </table>
    
  <?php endif ?>
  
</div>
