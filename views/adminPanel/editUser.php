<div class="sub extended">
  <div style="float:left">
  </div>
    <a href="#" onclick="t4g.url.action='listUsers'; t4g.url.redirect();">List Users</a> | <a href="#" onclick="t4g.url.action='editUser'; t4g.url.redirect();">Edit User</a> | <a href="#" onclick="t4g.url.action='adminLog'; t4g.url.query.set('actionFilter', 'setUserAs'); t4g.url.redirect();">User Log</a>
</div>

<div class="sub extended">
  <h2>Edit User</h2>
</div>

<div class="sub extended">
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th colspan="99">Search User</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="99">
          <input type="text" id="playerName" value="<?php echo mysql_real_escape_string(@$_GET['q']) ?>" style="width: 100%" />
        </td>
      </tr>
      <tr>
        <td colspan="99" style="text-align: right">
          <input type="button" value="Search" id="search" />
        </td>
      </tr>
    </tbody>
  </table>
</div>

<div class="sub extended" id="searchResult">
  <table class="data" style="width: 100%" style="display: none">
    <thead style="cursor: default">
      <tr>
        <th style="width: 30px">ID</th>
        <th style="width: 18px">&nbsp;</th>
        <th style="width: 100px">User Name</th>
        <th>BF Profiles</th>
        <th style="width: 90px">Clan</th>
        <th style="width: 90px">Join Date</th>
        <th style="width: 90px">Forum</th>
      </tr>
    </thead>
    <tbody>
      
    </tbody>
  </table>
</div>

<div id="userResults"></div>
<div id="actionsTemplate" style="display: none; float: right">
  <input type="button" class="setUserType" value="Make Moderator" /> <input type="button" class="setUserType" value="Remove Moderator" />
</div>

<div style="display: none">
  <select id="userType">
    <option value="user">Normal User</option>
    <option value="mod">Moderator</option>
    <option value="admin" disabled="disabled">Admin</option>
    <option value="tech" disabled="disabled">Tech</option>
  </select>
</div>

<script type="text/javascript">
window.addEvent('domready', function()
{
  var search = function()
  {
    $('userResults').load('listUsers?q=' + encodeURIComponent($('playerName').get('value')) + '&xhr=1');
  };
  
  $('userResults').set('load', {
    onSuccess: function()
    {
      var table = $$('#userResults table tbody')[0];
      if(!table) return;
      
      var userId = table.getElement('tr').get('userId'), userType = table.getElement('tr').get('userType');
      
      var cell = new Element('td');
      cell.set('colspan', '99');
      cell.setStyle('text-align', 'right');
      cell.appendText('Change To: ');
      
      var select = $('userType').clone(true, false);
      select.set('value', userType);
      
      if(userType != 'user' && userType != 'mod')
        select.disabled = true;
      else{
        select.addEvent('change', function(event)
        {
          table.getElements('select, input').removeClass('error');
          
          var newUserType = this.getSelected().get('text');
          
          var forumName = table.getElement('a.forumId');
          forumName = forumName && forumName.get('text');
          
          if(!forumName)
          { // Check if Forum ID is set
            table.getElement('input.forumId').addClass('error');
            select.set('value', userType);
            return;
          }
          
          if(table.getElement('.clan').value == '0')
          { // Check if Clan is set
            table.getElement('.clan').addClass('error');
            select.set('value', userType);
            return;
          }
          
          var c = confirm('Are you sure you want to make this user a Blacklist ' + newUserType + ' [Forum Name: ' + forumName + ']?');
          if(!c)
          {
            select.set('value', userType);
            return;
          }
          select.disabled = true;
          
          setUserAs(userId, this.get('value'), function(response){ search(); });
        });
      }
      
      cell.grab(select);
      
      var row = new Element('tr');
      row.grab(cell);
      
      table.grab(row);
    }
  });
  
  function setUserAs(userId, type, callback)
  {
    new Request.JSON
    ({
      url: 'setUserAs',
      onSuccess: function(response)
      {
        callback(response);
      }
    }).get('userId=' + userId + '&type=' + type);
  }
  
  $('search').addEvent('click', search);
 
  $('searchResult').hide();
  
  if($('playerName').get('value'))
  {
    search();
  }
});
</script>
