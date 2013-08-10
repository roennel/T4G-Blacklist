<?php if($data->showHeader): ?>
<div class="sub extended">
  <div style="float:left">
    <a href="#" onclick="t4g.url.action='listUsers'; t4g.url.redirect();">List Users</a> | <a href="#" onclick="t4g.url.action='editUser'; t4g.url.redirect();">Edit User</a> | <a href="#" onclick="t4g.url.action='adminLog'; t4g.url.query.set('actionFilter', 'setUserAs'); t4g.url.redirect();">User Log</a>
  </div>
</div>

<div class="sub extended">
  <h2>Users (<?= $data->usersCount ?>)</h2>
</div>
<?php endif; ?>

<div style="display: none">
  <select id="clanTemplate" class="clan" style="width: 90px">
    <option value="0">Unspecified</option>
    <?php
    foreach($data->clans as $item):
      $disabled = $item->editable != '1' ? 'disabled="disabled"' : '';
    ?>
      <option <?= $disabled ?> value="<?=$item->clanId?>"><?=$item->label?></option>
    <? endforeach; ?>
  </select>
</div>

<?php if(!$data->users): ?>
<div class="sub extended">No Users found</div>
<?php endif ?>

<?php foreach($data->users as $type => $users): ?> 
<div class="sub extended">
  <h3><?= $type ?></h3>
  <table class="data sortable" cellspacing="0" style="width: 100%">
    <thead style="cursor: default">
      <tr>
        <th style="width: 30px">ID</th>
        <th style="width: 18px">&nbsp;</th>
        <th style="width: 100px">User Name</th>
        <th>BF Profiles</th>
        <th style="width: 90px">Clan</th>
        <th style="width: 90px">Join Date</th>
        <th style="width: 20px" title="Actions / Day for the last month">A/D</th>
        <th style="width: 90px">Forum</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($users as $user): ?>
      <tr id="user<?= $user->userId ?>" userId="<?= $user->userId ?>" userType="<?= $user->type ?>">
        <td style="font-size: 10pt"><?= $user->userId ?></td>
        <td>
          <div style="background: url(http://blacklist.tools4games.com/img/flags/<?= strtolower($user->country) ?>.png) no-repeat center left; " title="<?= @$GLOBALS['countries'][$user->country] ?>">&nbsp;</div>
        </td>
        <td><?php echo $user->username ?></td>
        <td style="font-size: 9pt">
        <?php
          $nids = array();
          
          foreach($user->names as $nid => $names)
          {
            $nids[] = '<a href="http://battlefield.play4free.com/en/profile/' . $nid . '">' . $names . '</a>';
          }
          
          $nids[] = '<a class="addNucleusId" style="cursor:pointer">Add</a>';
          
          echo implode(' | ', $nids);
        ?>
        </td>
        <td sorttable_customkey="<?= $user->clanId; ?>" clanId="<?= $user->clanId ?>">
          <select class="clan" style="width: 90px">
          </select>
        </td>
        <td sorttable_customkey="<?= $user->joined ? date('YmdHis', $user->joined) : ''; ?>"><?= $user->joined ? date('d.m.Y', $user->joined) : ''; ?></td>
        <td><?= number_format($user->ad, 0, '.', '\''); ?></td>
        <td sorttable_customkey="<?= $user->forum ? date('YmdHis', $user->forum['user_lastvisit']) : ''; ?>">
        <?php if($user->forum): ?>
          <a class="forumId" href="http://forum.tools4games.com/memberlist.php?mode=viewprofile&u=<?= $user->forum['user_id'] ?>"><?= $user->forum['username'] ?></a>
          <br />
          <span style="font-size: 9pt; color: #aaa"><?= $user->forum ? date('d.m.Y', $user->forum['user_lastvisit']) : ''; ?></span>
        <?php else: ?>
          <input class="forumId" type="text" size="3" />
        <?php endif; ?>
        </td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>
<br /><br />
<?php endforeach ?>

<script type="text/javascript">
window.addEvent('domready', function()
{
  var clanTemplate = $('clanTemplate');
  
  $$('.clan').each(function(e)
  {
    var select = clanTemplate.clone();
    select.set('value', e.getParent().get('clanId'));
    
    select.replaces(e);
  });

  $$('.addNucleusId').addEvent('click', function(e)
  {
    var userId = this.getParent().getParent().get('userId');
    
    var text = prompt('Enter Nucleus Id or Profile URL here:', '') || '';
    var match = text.match(/^(?:(\d+)|.*\/profile\/(\d+).*)$/);
    
    if(match)
    {
      var nucleusId = match[1] || match[2];
      
      new Request.JSON
      ({
        url: 'addNucleusId',
        onSuccess: function(response)
        {
          alert('Added.');
        }
      }).get('userId=' + userId + '&nucleusId=' + nucleusId);
    }
  });

  $$('.clan').addEvent('change', function(e)
  {
    var select = this;
    
    var userId = this.getParent().getParent().get('userId');
    select.disabled = true;
    
    new Request.JSON
    ({
      url: 'setClanForUser',
      onSuccess: function(response)
      {
        select.disabled = false;
        alert('Updated.');
      }
    }).get('userId=' + userId + '&clanId=' + this.value);
  });
  
  
  $$('input.forumId').addEvent('keydown', function(event){
    if(event.key == 'enter') this.fireEvent('change');
  });
  $$('input.forumId').addEvent('change', function(e)
  {
    var input = this;
    
    if(input.disabled) return;
    
    var userId = this.getParent().getParent().get('userId');
    input.disabled = true;
    
    new Request.JSON
    ({
      url: 'setForumIdForUser',
      onSuccess: function(response)
      {
        if(response.success == true)
        {
          var link = new Element('a');
          link.set('href', 'http://forum.tools4games.com/memberlist.php?mode=viewprofile&u=' + response.forumUser.id);
          link.set('text', response.forumUser.name);
          link.addClass('forumId');
          input.getParent().grab(link);
          input.dispose();
        }
        else
        {
          alert('Error. Please make sure that the forum user id you\'ve entered is correct.');
          input.value = '';
          input.disabled = false;
          input.focus();
        }
        // alert('Updated.');
      }
    }).get('userId=' + userId + '&forumUserId=' + this.value);
  });
  
});
</script>
