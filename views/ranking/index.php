
<?php if($data->soldierNotFound): ?>
<div style="width: 100%; text-align: center; margin: 5px 0px; color: #ee0000">
  Soldier cannot be found, the Ranking Cache probably hasn't updated this Soldier yet, or the Soldier hasn't seen enough Action yet.
</div>
<?php endif ?>

<div class="sub extended" style="margin: 0px !important; width: 100% !important; margin-left: -10px !important;">
  
  <div style="float: left">
  Kit <select id="kit">
    <option value="">All</option>
    <option value="4">Recon</option>
    <option value="1">Assault</option>
    <option value="2">Medic</option>
    <option value="3">Engineer</option>
  </select>
  </div>
  
  <?php if(isAdmin() or isMod()): ?>
  <div style="margin-left: 10px; float: left">
    Filter <select id="filter">
      <option value="all">All</option>
      <option value="nbl">Only Non-Blacklist Banned</option>
    </select>
  </div>
  <?php endif ?>
  
  <div style="margin-left: 10px; float: left">
    Type <select id="type">
      <option value="">Default</option>
      <option value="round">per Round</option>
      <option value="day">per Day</option>
      <option value="hour">per Hour</option>
      <option value="minute">per Minute</option>
    </select>
    </span>
  </div>
  
  <div style="margin-left: 10px; float: left">
    Search 
    <input type="text" id="search" value="<?=@$_GET['search'] ?>" style="width: 200px" />
    <a href="/en/ranking">[ Reset ]</a>
  </div>
  
  <script>
  window.addEvent('domready', function()
  {
    $('search').addEvent('keyup', function(e)
    {
      $$('div.searchResult').dispose();
      
      if(this.get('value').length <= 4)  return;
      
      var pos = this.getPosition();
      var height = 20;
      var i = 0;
      var padding = 3;
      
      new Request.JSON
      ({
        url: 'ranking/searchName',
        onSuccess: function(response)
        {
          response.soldiers.each(function(soldier)
          {
            var div = new Element('div.searchResult');
            div.setStyle('height', (height) + 'px');
            div.setStyle('position', 'absolute');
            div.setStyle('left', pos.x + 'px');
            div.setStyle('top', ((pos.y + (height + 0) + ((height + (padding * 2) + 1) * i)) + 1) + 'px');
            div.setStyle('width', '200px');
            div.setStyle('background-color', '#fff');
            div.setStyle('border', '1px solid #444');
            div.setStyle('border-top', 'none');
            div.setStyle('color', '#eee');
            div.setStyle('padding', padding + 'px');
            div.setStyle('padding-top', (padding + 1) + 'px');
            div.setStyle('padding-bottom', (padding - 1) + 'px');
            div.setStyle('cursor', 'pointer');
            
            div.set('text', soldier.name.toUpperCase());
            
            div.addEvent('click', function()
            {
              $('search').set('value', soldier.name);
              
              t4g.url.query.set('soldierId', soldier.soldierId);
              t4g.url.query.set('search', $('search').get('value'));
              t4g.url.redirect();
            });
            
            $$('body')[0].grab(div);
            
            i++;
          });
        }
      }).get('name=' + this.get('value'));
    });
  });
  </script>
  
  <br /><br />
  <table class="data ranking" style="width: 100%">
    <thead>
      <tr>
        <th style="width: 20px" class="noSort">Rank</th>
        <th style="width: 20px" class="noSort">Kit</th>
        <th class="noSort" style="width: 150px">Player</th>
        <th class="noSort">Level</th>
        <th key="games">Games</th>
        <th style="width: 100px;" key="timePlayed">Time Played</th>
        <th key="timePlayed">Avg. Hours / Day</th>
        <th key="kills">Total Kills</th>
        <th key="infKills">Inf. Kills</th>
        <th key="vehicleKills">Vehicle Kills</th>
        <th key="deaths">Deaths</th>
        <th key="killratio">K/D</th>
        <th key="cpcaps">Flag Caps</th>
        <th key="cpneut">Flag Uncaps</th>
        <th key="ispm">Inf. Score / Minute</th>
        <th key="vspm">Vehicle Score / Minute</th>
        <th key="tspm">Team Score / Minute</th>
        <th key="wins">Wins</th>
        <th key="losses">Losses</th>
        <th key="winratio">W/L</th>
        <th key="accuracy">Accuracy</th>
        <th key="headshotratio">HS Ratio</th>
        <th key="meleeKills">Melee</th>
        <th key="killedByMelee">Killed By Melee</th>
        <th key="roadKills">Road Kills</th>
        <th key="runover">Runover</th>
        <th key="suicides">Suicides</th>
        <th key="destroyedVehicles">Destroyed Vehicles</th>
        <th key="bestScore">Best Score</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = ($data->start + 1); $i2 = 1; foreach($data->items as $item): 
        
        $ban = alxDatabaseManager::query
        ("
          SELECT banId FROM bans WHERE nucleusId = '{$item->nucleusId}' AND active = '1' LIMIT 1
        ")->fetch();
        
        $s = mktime(0, 0, 0, 10, 9, 2012);
        $d = (time() - $s) / 86400;
        $d2 = (time() - $item->date) / 86400;
        
        $clr = '#999';
        
        if($d2 >= 30)
        {
          $clr = '#a50';
        }
        
        if($d2 >= 60)
        {
          $clr = '#a00';
        }
        
        ?>
      <tr banId="<?=@$ban->banId ?>" class="<?=@$ban->banId > 0 ? 'banned' : '' ?> <?=(@$_GET['soldierId'] == $item->soldierId) ? 'searchResult' : '' ?>">
        <td style="text-align: center">#<?=f($i++, 0, $item, true)?></td>
        <td style="text-align: center"><?php if($item->level <= 0){ echo '-'; }else{ ?><img src="/img/class_<?=strToLower($GLOBALS['kits'][$item->kit])?>.png" /><?php } ?></td>
        <td>
          <a href="http://battlefield.play4free.com/en/profile/<?=$item->nucleusId?>/<?=$item->soldierId?>" target="_blank"><?=$item->name?></a>
          
          <br />
          <span style="color: <?=$clr ?>; font-size: 8pt"><?=date('d.m.Y H:i', $item->date) ?></span>
          <?php if(isTech() or isAdmin() or isMod() or @$_GET['wtf']): $id = md5(rand(0, 999999999)); ?>
          <a style="cursor: pointer" id="<?=$id ?>">[U]</a>
          <script>
            $('<?=$id?>').addEvent('click', function()
            {
              new Request.JSON
              ({
                url: '/bl/fetchStatsDo.php',
                onSuccess: function()
                {
                  self.location.reload();
                }
              }).get('n=<?=$item->nucleusId ?>');
            });
          </script>
          <?php endif ?>
        </td>
        <td><?=$item->level <= 0 ? '-' : $item->level ?></td>
        <td><?=f($item->games, 0, $item, true)?></td>
        <td><?=sec2hms($item->timePlayed)?></td>
        <td><?=f(($item->timePlayed / 3600) / $d, 1, $item, true) ?>h</td>
        <td><?=f($item->kills, 0, $item)?></td>
        <td><?=f($item->kills - $item->vehicleKills, 0, $item)?></td>
        <td><?=f($item->vehicleKills, 0, $item)?></td>
        <td><?=f($item->deaths, 0, $item)?></td>
        <td><?=f($item->kills / $item->deaths, 1, $item, true)?></td>
        <td><?=f($item->cpcaps, 0, $item)?></td>
        <td><?=f($item->cpneut, 0, $item)?></td>
        <td><?=f($item->ispm, 1, $item, true) ?></td>
        <td><?=f($item->vspm, 1, $item, true) ?></td>
        <td><?=f($item->tspm, 1, $item, true) ?></td>
        <td><?=f($item->wins, 0, $item, true)?></td>
        <td><?=f($item->losses, 0, $item, true)?></td>
        <td><?=f($item->winratio, 1, $item, true)?></td>
        <td><?=f($item->accuracy * 100, 1, $item, true)?>%</td>
        <td><?=f($item->headshotratio * 100, 1, $item, true)?>%</td>
        <td><?=f($item->meleeKills, 0, $item)?></td>
        <td><?=f($item->killedByMelee, 0, $item)?></td>
        <td><?=f($item->roadKills, 0, $item)?></td>
        <td><?=f($item->runover, 0, $item)?></td>
        <td><?=f($item->suicides, 0, $item)?></td>
        <td><?=f($item->destroyedVehicles, 0, $item)?></td>
        <td><?=f($item->bestScore, 0, $item, true)?></td>
      </tr>
      <?php if(($i2) % 22 == 0): ?>
        <tr>
        <th style="width: 20px" class="noSort">Rank</th>
        <th style="width: 20px" class="noSort">Kit</th>
        <th class="noSort" style="width: 150px">Player</th>
        <th class="noSort">Level</th>
        <th key="games">Games</th>
        <th key="timePlayed">Time Played</th>
        <th key="timePlayed">Avg. Hours / Day</th>
        <th key="kills">Total Kills</th>
        <th key="infKills">Inf. Kills</th>
        <th key="vehicleKills">Vehicle Kills</th>
        <th key="deaths">Deaths</th>
        <th key="killratio">K/D</th>
        <th key="cpcaps">Flag Caps</th>
        <th key="cpneut">Flag Uncaps</th>
        <th key="infantryPct">Inf %</th>
        <th key="vehiclePct">Vehicle %</th>
        <th key="teamPct">Team %</th>
        <th key="wins">Wins</th>
        <th key="losses">Losses</th>
        <th key="winratio">W/L</th>
        <th key="accuracy">Accuracy</th>
        <th key="headshotratio">HS Ratio</th>
        <th key="meleeKills">Melee</th>
        <th key="killedByMelee">Killed By Melee</th>
        <th key="roadKills">Road Kills</th>
        <th key="runover">Runover</th>
        <th key="suicides">Suicides</th>
        <th key="destroyedVehicles">Destroyed Vehicles</th>
        <th key="bestScore">Best Score</th>
      </tr>
      <?php endif ?>
      <?php $i2++; endforeach ?>
    </tbody>
  </table>
  
</div>

<script>
window.addEvent('domready', function()
{
  $('kit').addEvent('change', function()
  {
    t4g.url.query.set('kit', this.get('value'));
    t4g.url.redirect();
  });
  
  $('type').addEvent('change', function()
  {
    t4g.url.query.set('mod', this.get('value'));
    t4g.url.redirect();
  });
  
  if($('filter')) 
  {
    $('filter').addEvent('change', function()
    {
      t4g.url.query.set('filter2', this.get('value'));
      t4g.url.redirect();
    });
  }
  
  $('kit').set('value', '<?=@$_GET['kit'] ?>');
  $('type').set('value', '<?=@$_GET['mod'] ?>');
  if($('filter')) $('filter').set('value', '<?=@$_GET['filter2'] ?>');
  
  <?php if(!@$_GET['filter2']): ?>
  if($('filter')) $('filter').set('value', 'nbl');
  if($('filter')) $('filter').fireEvent('change');
  <?php endif ?>
  
  $$('th[key=<?=$data->sortKey?>]')[0].addClass('hl');
  
  $$('table.ranking thead tr th:not(.noSort)').each(function(item)
  {
    item.addEvent('click', function()
    {
      t4g.url.query.set('sort', item.get('key'));
      t4g.url.redirect();
    });
  });
});  
</script>
