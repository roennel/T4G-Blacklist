<div class="sub extended">
  
  <?php $date = time(); $i = 0; while($weapon = $data->weapons->fetch()): if($i > 99999) continue; ?>
  <div class="weaponGraph">
    <span><?php echo getNiceWeaponName($GLOBALS['weaponIds'][$weapon->weaponId]) ?></span>
    <img src="/img/img_weaponGraph.php?weaponId=<?php echo $weapon->weaponId ?>&date=<?php echo $date ?>&key=bestRangedKill" />
  </div>
  <?php $i++; endwhile ?>
  
</div>

<style>
  div.weaponGraph
  {
    width: 440px;
    height: 250px;
    padding: 10px;
    float: left;
  }
  
  div.weaponsGraph span
  {
    display: block;
    float: left;
    width: 100%;
  }
  
  div.weaponsGraph div
  {
    float: left;
    clear: left;
  }
</style>