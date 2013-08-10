<div class="sub extended">

  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th colspan="99">Add User Vote</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Target User</td>
        <td>
          <select id="userId" style="width:99%">
            <option value="">Choose...</option>
            <?php 
            
            $users = alxDatabaseManager::query("SELECT userId, username FROM users WHERE `mod` = '1' ORDER BY username ASC");
            
            while($user = $users->fetch())
            {
              echo "<option value=\"{$user->userId}\">{$user->username}</option>";
            }
            
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Type</td>
        <td>
          <select id="type" style="width:99%">
            <option value="">Choose...</option>
            <option value="submission">Submission</option>
            <option value="appeal">Appeal</option>
            <option value="forum">Forum</option>
            <option value="general">General</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Vote ID</td>
        <td>
          <input type="text" id="voteId" disabled style="width:98%" />
        </td>
      </tr>
      <tr>
        <td>Link</td>
        <td>
          <input type="text" id="link" style="width:98%" />
        </td>
      </tr>
      <tr>
        <td>Score</td>
        <td>
          <select id="score" style="width:99%">
            <option value="">Choose...</option>
            <option value="3">+3</option>
            <option value="2">+2</option>
            <option value="1">+1</option>
            <option value="0">0</option>
            <option value="-1">-1</option>
            <option value="-2">-2</option>
            <option value="-3">-3</option>
          </select>
        </td>
      </tr>
      <tr>
        <td style="vertical-align: top">Reason</td>
        <td>
          <textarea id="reason" style="width: 98.5%;height:100px"></textarea>
        </td>
      </tr>
      <tr>
        <td style="vertical-align: top">Notify User</td>
        <td><input type="checkbox" id="notify" /></td>
      </tr>
      <tr>
        <td colspan="99">
          <input type="button" id="save" value="Save" />
        </td>
      </tr>
    </tbody>
  </table>
  
</div>

<div class="sub extended" id="error">
<h2>Error</h2>
<span id="errorText"></span>
</div>

<script>
window.addEvent('domready', function()
{
  $('error').hide();
  
  <?php if(@$_GET['userId']): ?>
  $('userId').set('value', '<?=$_GET['userId'] ?>');
  <?php endif ?>
  
  <?php if(@$_GET['userVoteType']): ?>
  $('type').set('value', '<?=$_GET['userVoteType'] ?>');
  <?php endif ?>
  
  <?php if(@$_GET['voteId']): ?>
  $('voteId').set('value', '<?=$_GET['voteId'] ?>');
  <?php endif ?>
  
  <?php if(@$_GET['submissionId']): ?>
  $('link').set('value', 'http://blacklist.tools4games.com/en/modPanel/submissionDetail?submissionId=<?=$_GET['submissionId'] ?><?= @$_GET['voteId'] ? '#vote' . $_GET['voteId'] : ''  ?>');
  <?php endif ?>
  
  $('type').addEvent('change', function()
  {
    $('voteId').set('value', '');
  });
  
  $('save').addEvent('click', function()
  {
    var data = new Hash
    ({
      userId: $('userId').get('value'),
      type: $('type').get('value'),
      reason: encodeURIComponent($('reason').get('value')),
      voteId: $('voteId').get('value') || '0',
      link: $('link').get('value'),
      score: $('score').get('value')
    });
    
    if($('notify').checked)
    {
      data.set('notify', 'yes');
    }
    
    var valid = true;
    
    data.each(function(value, key)
    {
      $(key).removeClass('error');
      
      if(value == '') 
      {
        $(key).addClass('error');
        valid = false;  
      }
    });
    
    if(!valid) return;
    
    new Request.JSON
    ({
      url: 'addUserVoteDo',
      onSuccess: function(response)
      {
        if(response.success)
        {
          t4g.url.action = 'userVoting';
          t4g.url.redirect();
        }
        else
        {
          $('errorText').set('text', 'A vote already exists for the specified user/link.');
          $('error').show();
        }
      }
    }).get(data.toQueryString());
  });
});
</script>
