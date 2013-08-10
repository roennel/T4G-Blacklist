<?php

$w = '33%';

?>
<div class="sub extended">
  
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th style="width:<?php echo $w ?>;border-right: 1px solid #333">Level Limiter</th>
        <th style="width:<?php echo $w ?>;border-right: 1px solid #333">Player Requirements</th>
        <th style="width:<?php echo $w ?>">Item Limiter</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <!-- PLUGIN: LEVEL_LIMITER -->
          <table style="width: 100%">
            <tbody>
              <tr>
                <td>Enable</td>
                <td>
                  <input type="checkbox" />
                </td>
              </tr>
              <tr>
                <td>Lower Limit</td>
                <td>
                  <input type="text" style="width: 25px; text-align: center" value="0" />
                </td>
              </tr>
              <tr>
                <td>Upper Limit</td>
                <td>
                  <input type="text" style="width: 25px; text-align: center" value="0" />
                </td>
              </tr>
            </tbody>
          </table>
          <!-- END PLUGIN: LEVEL_LIMITER -->
        </td>
        <td>
          <!-- PLUGIN: PLAYER_REQUIREMENTS -->
          <table style="width: 100%">
            <tbody>
              <tr>
                <td>Enable</td>
                <td>
                  <input type="checkbox" />
                </td>
              </tr>
              <tr>
                <td>Type</td>
                <td>
                  <select>
                    <option value="">Score</option>
                    <option value="">K/D</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>Value</td>
                <td>
                  <select>
                    <option value="">&gt;</option>
                    <option value="">&lt;</option>
                  </select>
                  <input type="text" style="width: 50px; text-align: center" value="" />
                </td>
              </tr>
              <tr>
                <td>Delay</td>
                <td>
                  <input type="text" style="width: 28px; text-align: center" value="60" /> sec
                </td>
              </tr>
            </tbody>
          </table>
          <!-- END PLUGIN: PLAYER_REQUIREMENTS -->
        </td>
         <td>
          <!-- PLUGIN: ITEM_LIMITER -->
          <table style="width: 100%">
            <tbody>
              <tr>
                <td>Enable</td>
                <td>
                  <input type="checkbox" <?=$data->itemLimiter->active == '1' ? ' checked="checked"' : '' ?> />
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <table cellspacing="0" style="width: 100%">
                    <thead>
                      <tr>
                        <th>Item</th>
                        <th>Active?</th>
                        <th>Options</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      
                      $addItemLimiterItem = function($_weaponId, $active=true)
                      {
                        
                      ?>
                        
                      <tr>
                        <td>
                          <select class="itemLimiter_item">
                            <option value="">Please Choose...</option>
                            <?php $a = $GLOBALS['weaponIds']; asort($a); foreach($a as $weaponId => $weaponCode): $name = getNiceWeaponName($weaponCode); ?>
                            <option value="<?=$weaponId ?>"<?=($_weaponId == $weaponId ? ' selected="selected"' : '') ?>><?=$name ?></option>
                            <?php endforeach ?>
                          </select>
                        </td>
                        <td>
                          <input type="checkbox" class="itemLimiter_item_active"<?=$active ? ' checked="checked"' : '' ?> />
                        </td>
                        <td>
                          <div>[R]</div>
                        </td>
                      </tr>
                      <?php 
                      
                      };
                      
                      foreach($data->items as $item)
                      {
                        $addItemLimiterItem($item->itemId, $item->active == '1' ? true : false);
                      }
                      
                      if(@$_GET['addItem'])
                      {
                        $addItemLimiterItem(0, false);
                      }
                      
                      ?>
                      <tr>
                        <td colspan="3">
                          <a href="?serverId=<?=$data->serverId ?>&addItem=true">Add Item</a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
          <!-- END PLUGIN: ITEM_LIMITER -->
          </td>
      </tr>
    </tbody>
  </table>
  
</div>

<style>
  table tr td table tr td:last-child
  {
    text-align: right;
  }
</style>
