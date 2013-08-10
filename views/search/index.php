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

<script type="text/javascript">
window.addEvent('domready', function()
{
  $('searchResult').hide();
  
  var search = function()
  {
    $('searchResult').hide();
    $$('#searchResult table tbody tr').dispose();
    
    new Request.JSON
    ({
      url: 'search/result',
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
            'Name': item.name,
            'Date': item.date,
            'Blacklist': item.label
          }, function(value, key)
          {
            var cell = new Element('td');
            cell.set('text', value);
            
            row.grab(cell);
          });
          
          $$('#searchResult table tbody')[0].grab(row);
        });
      }
    }).get('name=' + encodeURIComponent($('playerName').get('value')));
  };
  
   $('search').addEvent('click', search);
   $('playerName').addEvent('change', search);
   
  <?php if(@$_GET['q']): ?>
  search();
  <?php endif ?>
});
</script>
