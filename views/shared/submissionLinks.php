<a target="_blank" href="http://battlefield.play4free.com/en/profile/<?php echo $data->nucleusId ?>">BF Stats</a>
<br /><br />

<table class="data submissionLinks" style="width: 100%">
  <thead>
    <tr>
    <th>&nbsp;</th>
    <?php
    if(count($data->names) <= 5):
      foreach($data->names as $name):
    ?>
      <th><?= $name ?></th>
    <?php
      endforeach;
    else: ?>
      <th colspan="5"><?= count($data->names) . " Soldiers" ?></th>
    <?php
    endif;
    ?>
      <th colspan="99">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="">Google:</td>
      <?php
      $i = 0;
      foreach($data->names as $name):
        if($i && !($i % 5)) echo '<td>&nbsp;</td></tr><tr><td>&nbsp;</td>';
        $i++;
      ?>
      <td><a class="link_google" target="_blank" href="https://www.google.com/#q=&quot;<?= urlencode($name) ?>&quot;+cheat"><?= $name ?></a></td>
      <?php endforeach; ?>
      <td colspan="99"><a href="#" title="Open All Links (Disable your Popup Blocker)" onclick="return openLinks('google')">&raquo;</a></td>
    </tr>
    <tr>
      <td class="">GGC:</td>
      <?php
      $i = 0;
      foreach($data->names as $name):
        if($i && !($i % 5)) echo '<td>&nbsp;</td></tr><tr><td>&nbsp;</td>';
        $i++;
      ?>
      <td><a class="link_ggc" target="_blank" href="http://www.ggc-stream.net/search/server/nickname/game/bfp4f/nick/<?= str_replace('%', '%25', urlencode($name)) ?>"><?= $name ?></a></td>
      <?php endforeach; ?>
      <td colspan="99"><a href="#" title="Open All Links (Disable your Popup Blocker)" onclick="return openLinks('ggc')">&raquo;</a></td>
    </tr>
    <tr>
      <td class="">PBBans:</td>
      <?php
      $i = 0;
      foreach($data->names as $name):
        if($i && !($i % 5)) echo '<td>&nbsp;</td></tr><tr><td>&nbsp;</td>';
        $i++;
      ?>
      <td><a class="link_pbbans" target="_blank" href="http://www.pbbans.com/mbi.php?searchtype=ALIAS&game_id=39&bantype=ALL&action=8&country_code=all&searchdata=<?= urlencode($name) ?>"><?= $name ?></a></td>
      <?php endforeach; ?>
      <td colspan="99"><a href="#" title="Open All Links (Disable your Popup Blocker)" onclick="return openLinks('pbbans')">&raquo;</a></td>
    </tr>
    <tr>
      <td class="">T4G SDB:</td>
      <?php
       $i = 0;
       foreach($data->names as $name):
        $sid = $data->soldierIds[$i];
        if($i && !($i % 5)) echo '<td>&nbsp;</td></tr><tr><td>&nbsp;</td>';
        $i++;
       ?>
      <td><a target="_blank" href="http://blacklist.tools4games.com/en/sdb/profileCharts?soldierId=<?= $sid ?>"><?= $name ?></a></td>
      <?php endforeach; ?>
      <td colspan="99">&nbsp;</td>
    </tr>
  </tbody>
</table>

<script type="text/javascript">
function openLinks(type)
{
  $$('.link_' + type).each(function(link)
  {
    window.open(link.href, '_blank', 'width=800, height=600');
  });
  
  return false;
}
</script>
