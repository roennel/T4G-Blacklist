<div class="sub extended">
  
  <h2>Total: <?php echo number_format($data->count, 0, '.', '\'') ?></h2>
  
  <h2>Done Appeals Valid: <?php echo number_format($data->countValid, 0, '.', '\'') ?></h2>
  <h2>Done Appeals Invalid: <?php echo number_format($data->countInvalid, 0, '.', '\'') ?></h2>
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th></th>
        <th style="width: 15px">#</th>
        <th style="width: 120px">Created</th>
        <th>Player</th>
        <th style="width: 120px">Type</th>
        <?php if(@$data->showDoneDate){ ?>
        <th style="width: 120px">Done</th>
        <?php } ?>
      </tr>
    </thead>
    <tbody>
      <?php $i = 0; while($item = $data->appeals->fetch()): 

        $ban = alxDatabaseManager::query("SELECT * FROM bans WHERE banId = '{$item->banId}' LIMIT 1")->fetch();
        
        if(@$ban->banId <= 0) continue;
        
        $profiles = alxDatabaseManager::query("SELECT name, level FROM profiles WHERE nucleusId = '{$ban->nucleusId}' ORDER BY name ASC");
        
        $names = array();
        
        while($profile = $profiles->fetch())
        {
          $names[] = $profile->name . " ({$profile->level})";
        }
        
        $clickFn = function() use($item)
        {
          $s = "t4g.url.controller = 'modPanel';";
          $s.= "t4g.url.action = 'appealDetail';";
          $s.= "t4g.url.query.set('appealId', '{$item->appealId}');";
          $s.= "event.ctrlKey ? window.open(t4g.url.build(), '_blank') : t4g.url.redirect()";
          
          echo $s;
        };
        ?>
      <tr onclick="<?php $clickFn() ?>" style="cursor: pointer">
        <td class="<?php echo $ban->active ? 'alreadyVotedNo' : 'alreadyVotedYes' ?>"></td>
        <td><?php echo $item->appealId ?></td>
        <td><?php echo date('d.m.Y H:i', $item->created) ?></td>
        <td><?php echo implode(' / ', $names) ?></td>
        <td><?php echo $GLOBALS['types'][$GLOBALS['idTypes'][$ban->blacklistId]] ?></td>
        <?php if(@$data->showDoneDate){ ?>
        <td><?php echo date('d.m.Y H:i', $item->date) ?></td>
        <?php } ?>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
</div>
