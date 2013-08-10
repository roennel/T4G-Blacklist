<table class="data" style="width: 100%">
  <thead>
    <tr>
      <th colspan="99">Submission Preview</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Links</td>
      <td>
        <?php
          $this->addContainer
          (
            'submissionLinks',
            new alxView('submissionLinks'),
            array
            (
              'nucleusId' => $data->nucleusId,
              'names' => $data->targetNames,
              'soldierIds' => $data->targetSoldierIds
            )
          );
        
          $this->insertContainer('submissionLinks');
        ?>
      </td>
    </tr>
    <tr>
      <td style="vertical-align: top;margin-bottom: 20px">Score
        <br />
        <span style="font-weight: bold; color: #bb0000">
          These are only Indicators! 
          <br />
          Not concrete evidence.
        </span>
        
        </td>
      <td>
        
        <div style="float: left;">
          
          <div style="float: left; margin-right: 15px">
            <div class="score score_0"></div> Legit
          </div>
          
          <div style="float: left; margin-right: 15px">
            <div class="score score_1"></div> Suspicious
          </div>
          
          <div style="float: left; margin-right: 15px">
            <div class="score score_2"></div> Very Suspicious
          </div>
            
          <div style="float: left; margin-right: 15px">
            <div class="score score_3"></div> Nearly Definite
          </div>
        </div>
        <?php
          $sdbDates = array();
          
          $sd = alxDatabaseManager::query("SELECT DISTINCT(date) AS date FROM profile_stats WHERE soldierId IN ('" . implode(',', $data->targetSoldierIds) . "') ORDER BY date DESC");
          
          while($sdi = $sd->fetch())
          {
            $sdbDates[] = $sdi->date;
          }
          
          $statsTS = @$_GET['statsTS'];
          
          if($statsTS != '0' and !in_array($statsTS, $sdbDates))
          {
            if($sdbDates[0] >= (time()-604800)){
              $statsTS = $sdbDates[0];
            }else{
              $statsTS = null;
              
              // Update SDB with fresh stats
              $cmd = "nice php /var/www/t4g_blacklist/bl/fetchStatsDo.php {$data->nucleusId} 2>&1 & echo $!";
              pclose(popen($cmd, 'r'));
            }
          }
        ?>
        <div style="float: right;">
          <select id="sdbSelect">
            <option value="0">Current</option>
          <?php $i = 0;
            foreach($sdbDates as $d): ?>
            <option value="<?php echo $d; ?>"<?php echo ($d == $statsTS) ? ' selected="selected"' : ''; ?>>SDB: <?php echo date('d.m.Y', $d); ?></option>
          <?php $i++;
            endforeach; ?>
          </select>
        </div>
        
        <div style="clear: both;margin-bottom: 20px"></div>
        
        <?php
          $this->addContainer
          (
            'scoreStats',
            new alxView('scoreStats'),
            array
            (
              'targets' => $data->targets,
              'statsTS' => $statsTS
            )
          );
        ?>
        <div id="scoreStats">
          <?php $this->insertContainer('scoreStats'); ?>
        </div>
      </td>  
    </tr>
  </tbody>
</table>

<script type="text/javascript">
  $('sdbSelect').addEvent('change', function()
  {
    $$('#scoreStats table').setStyle('opacity', '0.75');
    $('scoreStats').load('scoreStats?statsTS=' + this.value + '&nucleusId=<?php echo $data->nucleusId; ?>');
  });
</script>
