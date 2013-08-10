<div class="sub extended">
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th colspan="99">Player Name</th>
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
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th colspan="99">Search Result</th>
      </tr>
    </thead>
    <tbody>
      
    </tbody>
  </table>
</div>

<div class="sub extended" id="advSearch">
  <form action="../<?php echo (isMod() && isAdmin()) ? "adminPanel" : "modPanel"; ?>/submissions" method="GET">
  <table class="data" style="width: 100%">
    <thead>
      <tr>
        <th colspan="99">Search Submissions</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Type</td>
        <td>
          <select name="type">
            <option value="">-</option>
            <option value="ch">Cheating</option>
            <option value="sp">Statspadding</option>
            <option value="gl">Glitching</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Min. Level</td>
        <td>
          <select name="minLevel">
            <option value="">-</option>
          <?php for($i = 1; $i <= 30; $i++): ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
          <?php endfor; ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Min. Headshot %</td>
        <td>
          <select name="minHSRatio">
            <option value="">-</option>
            <option value="0.3">30%</option>
            <option value="0.35">35%</option>
          <?php for($i = 40; $i <= 80; $i+=10): ?>
            <option value="<?php echo $i/100; ?>"><?php echo $i; ?>%</option>
          <?php endfor; ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Min. Best Score</td>
        <td>
          <select name="minBestScore">
            <option value="">-</option>
          <?php for($i = 30000; $i <= 80000; $i+=10000): ?>
            <option value="<?php echo $i; ?>"><?php echo number_format($i, 0, '.', '\''); ?></option>
          <?php endfor; ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Min. Votes</td>
        <td>
          <select name="minVotes">
            <option value="">-</option>
          <?php for($i = 1; $i <= 4; $i++): ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
          <?php endfor; ?>
          </select>
        </td>
      </tr>
      <?php if(isMod() and isAdmin()): ?>
      <tr>
        <td>Show Delayed Subs</td>
        <td>
          <input name="showDelayed" value="1" type="checkbox" />
        </td>
      </tr>
      <tr>
        <td>Show ModPanel Subs</td>
        <td>
          <input name="showModSubs" value="1" type="checkbox" />
        </td>
      </tr>
      <?php endif ?>
      <tr>
        <td>Sort By</td>
        <td>
          <select name="sort">
            <!-- <option value="">Default</option> -->
            <option value="-lastSeen">Last Seen Playing</option>
            <option value="type">Type</option>
            <option value="created" selected="selected">Date (oldest first)</option>
            <option value="-created">Date (newest first)</option>
            <option value="votesCount">Vote Count</option>
            <option value="sourceNucleusId">Submitter</option>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="99" style="text-align: right">
          <input type="hidden" name="hideVoted" value="1" />
          <input type="submit" value="Search" id="search" />
        </td>
      </tr>
    </tbody>
  </table>
  </form>
</div>

<script type="text/javascript">
window.addEvent('domready', function()
{  
  var search = function()
  {
    $('advSearch').hide();
    $('searchResult').hide();
    $$('#searchResult table tbody tr').dispose();
    
    new Request.JSON
    ({
      url: 'searchResult',
      onSuccess: function(response)
      {
        $('searchResult').show();
        
        if(response.data.length == 0)
        {
          var row = new Element('tr');
          var cell = new Element('td[colspan=99]');
          cell.set('text', 'No Entries found');
          
          row.grab(cell);
          
          $$('#searchResult table tbody')[0].grab(row);
        }
        
        response.data.each(function(item)
        {
          var row = new Element('tr');
          
          Object.each
          ({
            'Type': item.type,
            'Name': item.name,
            'Date': item.date,
            'Blacklist': item.label,
            'Done': item.done=='1' ? 'Done' : 'Pending'
          }, function(value, key)
          {
            var cell = new Element('td');
            cell.set('text', value);
            
            row.grab(cell);
          });
          
          row.addEvent('click', function(event){
            if(item.type == 'Appeal')
            {
              t4g.url.action = 'appealDetail';
              t4g.url.query.set('appealId', item.id);
            }else
            {
              t4g.url.action = 'submissionDetail';
              t4g.url.query.set('submissionId', item.id);
            }
            t4g.url.query.set('redirTo', '<?php echo alxRequestHandler::getAction(); ?>');
            
            event.control ? window.open(t4g.url.build(), '_blank') : t4g.url.redirect();
          });
          row.style.cursor = 'pointer';
          
          $$('#searchResult table tbody')[0].grab(row);
        });
      }
    }).get('name=' + encodeURIComponent($('playerName').get('value')));
  };
  
   $('search').addEvent('click', search);
   // $('playerName').addEvent('change', search);
   
  $('searchResult').hide();

  if($('playerName').get('value'))
  {
    search();
    $('advSearch').hide();
  }
    
});
</script>
