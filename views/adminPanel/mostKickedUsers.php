<div class="sub extended">

  <h2>Most Kicked Users</h2>
  
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th>User</th>
        <th style="width: 100px">Kicks</th>
        <th style="width: 100px">Options</th>
      </tr>
    </thead>
    <tbody>
      <?php while($item = $data->bans->fetch()): 
        
        if($item->banId == 0) continue;
        
        $ban = alxDatabaseManager::query("SELECT * FROM bans WHERE banId = '{$item->banId}' LIMIT 1")->fetch();
        
        if(@$ban->banId <= 0) continue;
        
        $_profiles = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$ban->nucleusId}'");
        $profiles = array();
        
        while($profile = $_profiles->fetch())
        {
          $profiles[] = $profile->name;
        }
        
        
        ?>
      <tr>
        <td><?php echo implode(' / ', $profiles) ?></td>
        <td><?php echo number_format($item->c, 0, '.', '\'') ?></td>
        <td>
          <a href="#" onclick="t4g.url.action = 'userKickHistory'; t4g.url.query.set('banId', '<?php echo $ban->banId ?>'); t4g.url.redirect();">Kick History</a>
          
        </td>  
      </tr>
      <?php endwhile ?>
    </tbody>
  </table>
  
</div>
