<div class="sub extended">
  
  <h2>Total: <?php echo number_format($data->count, 0, '.', '\'') ?></h2>
  
  <h2>Imported Submissions/Bans: <?php echo number_format($data->countImport, 0, '.', '\'') ?></h2>
  <h2>Done Submissions Valid: <?php echo number_format($data->countValid, 0, '.', '\'') ?></h2>
  <h2>Done Submissions Invalid: <?php echo number_format($data->countInvalid, 0, '.', '\'') ?></h2>
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th></th>
        <th style="width: 15px">#</th>
        <th style="width: 120px">Created</th>
        <th>Player</th>
        <th style="width: 100px">Type</th>
        <?php if(@$data->showDoneDate){ ?>
        <th style="width: 120px">Done</th>
        <?php } ?>
      </tr>
    </thead>
    <tbody>
      <?php $i = 0; while($item = $data->submissions->fetch()): 

        $profiles = alxDatabaseManager::query("SELECT name, level FROM profiles WHERE nucleusId = '{$item->targetNucleusId}' ORDER BY name ASC");
        
        $names = array();
        
        while($profile = $profiles->fetch())
        {
          $names[] = $profile->name . " ({$profile->level})";
        }
        
        $check = alxDatabaseManager::query("SELECT banId FROM bans WHERE submissionId = '{$item->submissionId}' LIMIT 1")->fetch();
        
        ?>
      <tr onclick="t4g.url.controller = 'modPanel'; t4g.url.action = 'submissionDetail'; t4g.url.query.set('submissionId', '<?php echo $item->submissionId ?>'); t4g.url.redirect()" style="cursor: pointer">
        <td class="<?php echo @$check->banId > 0 ? 'alreadyVotedYes' : 'alreadyVotedNo' ?>"></td>
        <td><?php echo $item->submissionId ?></td>
        <td><?php echo date('d.m.Y H:i', $item->created) ?></td>
        <td><?php echo implode(' / ', $names) ?></td>
        <td><?php echo $GLOBALS['types'][$item->type] ?></td>
        <?php if(@$data->showDoneDate){ ?>
        <td><?php echo date('d.m.Y H:i', $item->date) ?></td>
        <?php } ?>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
</div>
