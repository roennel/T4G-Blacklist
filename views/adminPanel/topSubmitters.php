<div class="sub extended">

  <h2>Top Submitters</h2>
  
  <span>Click on the Table Headers to Sort by Success Ratio</span>
  <br /><br />
  
  <table class="data sortable" style="width: 100%">
    <thead>
      <tr>
        <th>User</th>
        <th style="width: 100px">Cheating</th>
        <th style="width: 100px">Statspadding</th>
        <th style="width: 100px">Glitching</th>
        <th style="width: 100px">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php while($item = $data->sub->fetch()): 
        
        $_profiles = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$item->sourceNucleusId}'");
        $profiles = array();
        
        while($profile = $_profiles->fetch())
        {
          if(!in_array($profile->name, $profiles))
          {
            $profiles[] = $profile->name;
          }
        }
        
        $ch = alxDatabaseManager::query("SELECT COUNT(submissionId) AS c FROM submissions WHERE sourceNucleusId = '{$item->sourceNucleusId}' AND type = 'ch'")->fetch();
        $sp = alxDatabaseManager::query("SELECT COUNT(submissionId) AS c FROM submissions WHERE sourceNucleusId = '{$item->sourceNucleusId}' AND type = 'sp'")->fetch();
        $gl = alxDatabaseManager::query("SELECT COUNT(submissionId) AS c FROM submissions WHERE sourceNucleusId = '{$item->sourceNucleusId}' AND type = 'gl'")->fetch();
        
        $ch_s = alxDatabaseManager::query
        ("
          SELECT COUNT(*) AS c FROM submissions AS s, bans AS b WHERE b.submissionId = s.submissionId AND  s.sourceNucleusId = '{$item->sourceNucleusId}' AND type = 'ch'
        ")->fetch();
        
        $sp_s = alxDatabaseManager::query
        ("
          SELECT COUNT(*) AS c FROM submissions AS s, bans AS b WHERE b.submissionId = s.submissionId AND  s.sourceNucleusId = '{$item->sourceNucleusId}' AND type = 'sp'
        ")->fetch();
        
        $gl_s = alxDatabaseManager::query
        ("
          SELECT COUNT(*) AS c FROM submissions AS s, bans AS b WHERE b.submissionId = s.submissionId AND  s.sourceNucleusId = '{$item->sourceNucleusId}' AND type = 'gl'
        ")->fetch();
        
        $t_s = $ch_s->c + $sp_s->c + $gl_s->c;
        
        ?>
      <tr class="xls">
        <td><?php echo implode(' / ', $profiles) ?></td>
        <td sorttable_customkey="<?php echo ($ch_s->c / ($ch->c == 0 ? 1 : $ch->c)) ?>"><?php echo number_format($ch->c, 0, '.', '\'') . ' <span>(' . number_format(($ch_s->c / ($ch->c == 0 ? 1 : $ch->c)) * 100, 1, '.', '\'') . '%)</span>' ?></td>
        <td sorttable_customkey="<?php echo ($sp_s->c / ($sp->c == 0 ? 1 : $sp->c)) ?>"><?php echo number_format($sp->c, 0, '.', '\'') . ' <span>(' . number_format(($sp_s->c / ($sp->c == 0 ? 1 : $sp->c)) * 100, 1, '.', '\'') . '%)</span>' ?></td>
        <td sorttable_customkey="<?php echo ($gl_s->c / ($gl->c == 0 ? 1 : $gl->c)) ?>"><?php echo number_format($gl->c, 0, '.', '\'') . ' <span>(' . number_format(($gl_s->c / ($gl->c == 0 ? 1 : $gl->c)) * 100, 1, '.', '\'') . '%)</span>' ?></td>
        <td sorttable_customkey="<?php echo ($t_s / ($item->c == 0 ? 1 : $item->c)) ?>" style="font-weight: bold"><?php echo number_format($item->c, 0, '.', '\'') . ' <span>(' . number_format(($t_s / ($item->c == 0 ? 1 : $item->c)) * 100, 1, '.', '\'') . '%)</span>' ?></td>
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
  
</div>

<style>
  tr.xls td span
  {
    color: #aaa;
    font-size: 8pt;
    font-weight: normal !important;
  }
</style>