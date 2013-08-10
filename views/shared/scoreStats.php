<?php if(@$_GET['target'] != '0'): ?>
<?php
  
  $soldiers = 0;
  
  $scoreLabels = array
  (
    'hsRatio' => 'HS Ratio',
    'accuracy' => 'Accuracy'
  );
  
  $conLabels = array
  (
    'hsRatio' => 'HS Consistency',
    'accuracy' => 'Acc. Consistency'
  );
  
  $scoreCol = array
  (
    0 => '#009900',
    10 => '#dddd00',
    20 => '#cc00cc',
    100 => '#cc0000'
  );
  
  $averageCol = array
  (
    2 => '#cc0000',
    4 => '#cc00cc',
    8 => '#dddd00',
    10 => '#009900'
  );
  
  /*
* "Karkand":"1","Oman":"2","Sharqi":"3","Basra":"4","Dragon Valley":"5","Dalian":"6","Mashtuur":"7","Myanmar":"8"}
* */

  $bestScoreLabels = array
  (
    'global' => 'Global',
    '4_1' => 'Karkand Assault',
    '4_2' => 'Oman Assault',
    '4_3' => 'Sharqi Assault',
    '4_4' => 'Basra Assault',
    '4_5' => 'Dragon Assault',
    '4_6' => 'Dalian Assault',
    '4_7' => 'Mashtuur Assault',
    '4_8' => 'Myanmar Assault',
    '5_1' => 'Karkand Rush',
    '5_2' => 'Oman Rush',
    '5_3' => 'Sharqi Rush',
    '5_4' => 'Basra Rush',
    '5_5' => 'Dragon Rush',
    '5_6' => 'Dalian Rush',
    '5_7' => 'Mashtuur Rush',
    '5_8' => 'Myanmar Rush',
  );
  
  foreach($data->targets as $target)
  {
    $none = array();
    $soldiers++;
    
    $s = scoreStats($target->nucleusId, $target->soldierId, @$_GET['all'] ? true : false, $data->statsTS);
  ?>
  <table class="data" style="width: 24%;float: left;margin-right: 5px;">
    <thead>
      <tr>
        <td colspan="2"><img src="http://bfp4f.tools4games.com/img/icons/class_<?php echo strToLower($s->soldier->kitName) ?>.png" /></td>
      </tr>
      <tr>
        <th colspan="2"><?php echo $s->soldier->name ?> (<?= $s->soldier->level ?: '?' ?>) <a href="http://battlefield.play4free.com/en/profile/<?php echo $target->nucleusId . '/' . $target->soldierId; ?>">&raquo;</a></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($s->scores as $key => $value): 
        
        $q = -100;
        
        foreach($scoreCol as $sKey => $sValue)
        {
          if($value >= $q && $value < $sKey)
          {
            $col = $sValue; 
          }
          
          $q = $sKey;
        }
        
        ?>
      <tr>
        <td><?php echo $scoreLabels[$key] ?></td>
        <td><span style="color: <?php echo $col ?>"><?php echo 1 - $value ?></span></td>
      </tr>
      <?php endforeach ?>
      
      <?php foreach($s->diff as $key => $diff): 
      
        $i = 0;
        
        if(array_key_exists($key, $s->prob))
        {
          foreach($s->prob[$key] as $item)
          {
            if($item[1] == 0) { continue; }
          
            $i++;
          }
        }
        
        if($i == 0)
        {
          $none[] = $key;  
        }
        
        if($i <= 2) continue;
        
        $dd = round($diff > 0 ? $diff : 1 - $diff);
        
        $q = 0;
        
        foreach($averageCol as $aKey => $aValue)
        {
          if($dd >= $q && $value < $aKey)
          {
            $col = $aValue;
          }
          
          $q = $aKey;
        }
        
      ?>
      <tr>
        <td><?php echo $conLabels[$key] ?></td>
        <td><span style="color: <?php echo $col ?>"><?php echo number_format($dd, 0) ?></span></td>
      </tr>
      <?php endforeach ?>
      
      <?php foreach($s->prob as $key => $value): if(in_array($key, $none)) continue; ?>
      <tr class="stats_all_<?php echo $key; ?>_dblclick">
        <td class="hl" colspan="2"><?php echo $scoreLabels[$key] ?> <span style="float: right"><?php echo $s->avg2->{$key} ?>%</span></td>
      </tr>
        <?php foreach($value as $item): if($item[1] == 0 && !@$_GET['all']) { continue; } ?>
        <tr class="stats_dblclick stats_<?php echo $key; ?>">
          <td><?php echo $item[0] ?><span style="float:left;text-indent:-1999px; width:0"> [<?php echo $scoreLabels[$key] == 'Accuracy' ? 'ACCURACY' : $item[3] ?>]</span></td>
          <td>
          <!--<div class="score score_<?php echo $item[1] ?>" title="<?php echo $item[3] ?>"></div>-->
          <span class="score_<?php echo $item[1] ?>" title="<?php echo $item[3] ?>"><?php echo number_format($item[2], 0) ?>%</span>
          </td>
        </tr>
        <?php endforeach ?>
      <?php endforeach ?>
      
      <?php if(count($s->maxScore) > 0 or $s->killStreak): ?>
      <tr class="stats_all_padding_dblclick">
        <td class="hl" colspan="2">Stats Padding</td>
      </tr>
      <?php endif ?>
      
      <?php foreach($s->maxScore as $k => $ms): ?>
      <tr class="stats_dblclick stats_padding">
        <td><?php echo @$bestScoreLabels[$k] ?></td>
        <td><span class="score_<?php echo $ms[0] ?>"><?php echo number_format($ms[1], 0, '.', '\'') ?></span></td>
      </tr>
      <?php endforeach ?>
      <?php if($s->killStreak): ?>
      <tr class="stats_dblclick stats_padding">
        <td>KillStreak</td>
        <td><span class="score_<?php echo $s->killStreak[0] ?>"><?php echo number_format($s->killStreak[1], 0, '.', '\'') ?></span></td>
      </tr>
      <?php endif ?>
      
    </tbody>
  </table>
  <?php
  
  if($soldiers%4 == 0)
  {
    echo '<div style="clear: both"></div>';
  }
  
  }
  
?>
<div style="clear: both"></div>

<script type="text/javascript">
  var act = navigator.userAgent.match(/iPad|iPhone|Android/i) || navigator.platform.match(/Linux armv7l/i) ? 'click' : 'dblclick';

  $$('tr.stats_dblclick').addEvent(act, function()
  {
	if($('voteMessage'))
	{
	  var cells = this.getElements('td');
	  var text = cells[0].get('text') + ' ' + cells[1].get('html');
	  text = text.replace(/\n/g, ' ').replace(/ title="(.*)"/g, '').replace(/\s{2,}/g, ' ').replace();
	  text = text.replace(/\[(.*)\]/, '[stats][$1][/stats]');
	  text = text.replace(/<span class="score_(.*)">(.*)<\/span>/g, '[score=$1]$2[/score]') + '\n';
	  var scroll = window.getScroll();
	  $('voteMessage').insertAtCursor(text, false);
	  window.scrollTo(scroll.x, scroll.y);
	  (function(){ window.scrollTo(scroll.x, scroll.y); }).delay(0);
	}
  });
  
  $$('tr.stats_all_hsRatio_dblclick').addEvent(act, function()
  {
	this.getParent('table.data').getElements('tr.stats_hsRatio').each(function(item) {
	  item.fireEvent(act);
	});
  });
  
  $$('tr.stats_all_accuracy_dblclick').addEvent(act, function()
  {
	this.getParent('table.data').getElements('tr.stats_accuracy').each(function(item) {
	  item.fireEvent(act);
	});
  });
  
  $$('tr.stats_all_padding_dblclick').addEvent(act, function()
  {
	this.getParent('table.data').getElements('tr.stats_padding').each(function(item) {
	  item.fireEvent(act);
	});
  });
</script>

<?php endif ?>