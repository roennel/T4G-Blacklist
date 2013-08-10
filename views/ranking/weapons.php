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
    Weapon <select id="weapon">
      <option value="">Please Choose...</option>
      <?php $a = $GLOBALS['weaponIds']; asort($a); foreach($a as $weaponId => $weaponCode): if($weaponId < 2000) { continue; } ?>
      <option value="<?=$weaponId ?>"><?=getNiceWeaponName($weaponCode) ?></option>
      <?php endforeach ?>
    </select>
  </div>
  
  <div style="margin-left: 10px; float: left; display: none" id="update">
    Updating Profiles -&gt; <span id="updateDone"></span> / <span id="updateTotal"></span>
  </div>
  
  <div style="margin-left: 10px; float: left; display: none" id="updateComplete">
  Updating Complete! Reloading Ranking...  
  </div>
  
  <br /><br />
  <table class="data ranking" style="width: 100%">
    <thead>
      <tr>
        <th style="width: 20px" class="noSort">Rank</th>
        <th style="width: 20px" class="noSort">Kit</th>
        <th class="noSort" style="width: 180px">Player</th>
        <th class="noSort">Level</th>
        <th class="noSort">Weapon</th>
        <th key="timeUsed" style="width: 300px;" >Time Used</th>
        <th key="kills">Kills</th>
        <th key="deathsBy">Deaths By</th>
        <th key="shots">Shots</th>
        <th key="accuracy">Accuracy</th>
        <th key="headshotratio">HS Ratio</th>
        <th key="bestRangedKill">Best Ranged Kill</th>
        <th key="dpb">Damage / Bullet</th>
        <th key="damageDealt">Damage</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; $toUpdate = []; foreach($data->items as $item): 
        
        $t = time() - (86400 * 7);
      
        if($item->date <= $t)
        { 
          $toUpdate[] = $item->nucleusId;
        }
        
        $ban = alxDatabaseManager::query
        ("
          SELECT banId FROM bans WHERE nucleusId = '{$item->nucleusId}' AND active = '1' LIMIT 1
        ")->fetch();
        
        ?>
      <tr banId="<?=$ban->banId ?>" class="<?=$ban->banId > 0 ? 'banned' : '' ?>">
        <td style="text-align: center">#<?=$i++?></td>
        <td style="text-align: center"><?php if($item->level <= 0){ echo '-'; }else{ ?><img src="/img/class_<?=strToLower($GLOBALS['kits'][$item->kit])?>.png" /><?php } ?></td>
        <td>
          <a href="http://battlefield.play4free.com/en/profile/<?=$item->nucleusId?>/<?=$item->soldierId?>" target="_blank"><?=$item->name?></a>
          <br />
          <span style="color: #999; font-size: 8pt"><?=date('d.m.Y H:i', $item->date) ?></span>
          
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
        <td><?=getNiceWeaponName($GLOBALS['weaponIds'][$item->weaponId]) ?></td>
        <td><?=sec2hms($item->timeUsed)?></td>      
        <td><?=f($item->kills)?></td>
        <td><?=f($item->deathsBy)?></td>
        <td><?=f($item->shots)?></td>
        
        <td><?=f($item->accuracy * 100, 1)?>%</td>
        <td><?=f($item->headshotratio * 100, 1)?>%</td>
        <td><?=f($item->bestRangedKill)?>m</td>
        <td><?=f($item->dpb, 1)?></td>
        <td><?=f($item->damageDealt)?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  
</div>

<script>
window.addEvent('domready', function()
{
  <?php if(count($toUpdate) > 0 && false): ?>
  $('update').show();
  
  $('updateDone').set('text', '0');
  $('updateTotal').set('text', '<?=count($toUpdate) ?>');
  
  var todo = 
  [
    <?=implode(', ', $toUpdate) ?>
  ];
  
  var todo = [2547320513, 231754322];
  
  var done = 0;
  var i = 0;
  
  todo.each(function(nucleusId)
  {
    (function()
    { 
    new Request.JSON
    ({
      url: 'fetchStats',
      onSuccess: function(response)
      {
        done++;
        $('updateDone').set('text', done);
        
        if(done >= <?=count($toUpdate) ?>)
        {
          $('update').hide();
          $('updateComplete').show();
          
          self.location.reload();
        }      
      }
    }).get('nucleusId=' + nucleusId);
    
    i++;
    }).delay(i * 10000);
  });
  
  <?php endif ?>
  
  $('kit').addEvent('change', function()
  {
    t4g.url.query.set('kit', this.get('value'));
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
  
  $('weapon').addEvent('change', function()
  {
    t4g.url.query.set('weapon', this.get('value'));
    t4g.url.redirect();
  });
  
  $('kit').set('value', '<?=@$_GET['kit'] ?>');
  if($('filter')) $('filter').set('value', '<?=@$_GET['filter2'] ?>');
  $('weapon').set('value', '<?=@$_GET['weapon'] ?>');
  
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
