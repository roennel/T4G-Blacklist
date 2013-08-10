<?php

function f($v, $dec=0, $item=null, $exclude = false)
{
  if(@$_GET['mod'] == 'round' && !$exclude)
  {
    $v = $v / ($item->games == 0 ? 1 : $item->games);
  }
  
  if(@$_GET['mod'] == 'day' && !$exclude)
  {
    $t = $item->timePlayed / 86400;
    
    $v = $v / ($t == 0 ? 1 : $t);
  }
  
  if(@$_GET['mod'] == 'hour' && !$exclude)
  {
    $t = $item->timePlayed / 3600;
    
    $v = $v / ($t == 0 ? 1 : $t);
  }
  
  if(@$_GET['mod'] == 'minute' && !$exclude)
  {
    $t = $item->timePlayed / 60;
    
    $v = $v / ($t == 0 ? 1 : $t);
  }
  
  return number_format($v, $dec, '.', '\'');
};

$GLOBALS['kits'] = 
[
  0 => 'Recon',
  1 => 'Assault',
  2 => 'Medic',
  3 => 'Engineer'
];

?>
<!DOCTYPE html>
<html>
<head><?php

if(alxRequestHandler::getController() == 'admin')
{
  $t = ts('nav_' . alxRequestHandler::getController()) . ' > ' . ts('nav_' . alxRequestHandler::getController() . '_' . alxRequestHandler::getAction());
}
else
{
  $t = ts('nav_' . alxRequestHandler::getController());
}

$this->insertTitle('T4G Leaderboard Battlefield Play4Free ' . (!empty($t) ? ' > ' . $t : ''));

$this->insertCSS('reset');
$this->insertCSS('t4g_bl_2012');

$this->insertJS('mootools');
$this->insertJS('prototypes');
$this->insertJS('sorttable');

$this->insertJS('t4g-url');
$this->insertJS('t4g-language');
$this->insertJS('t4g-request');

$this->insertJS('t4g');
$this->insertJS('t4g-init');

?>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="/favicon.png" />
<link href="http://fonts.googleapis.com/css?family=Strait" rel="stylesheet" type="text/css">
<script type="text/javascript">

t4g.url.host = 'ranking.tools4games.com';
t4g.url.base = '/';
t4g.url.lang = '<?php echo getLang() ?>';
t4g.url.controller = '<?php echo alxRequestHandler::getController() ?>';
t4g.url.action = '<?php echo alxRequestHandler::getAction() != 'index' ? alxRequestHandler::getAction() : '' ?>';
t4g.url.game = '<?php echo @alxApplication::getConfigVar('id', 'game') ?>';

<?php
  foreach($_GET as $key => $val)
  {
    $valid = true;
    
    switch($key)
    {
      case 'lang':
      case 'controller':
      case 'action':
        $valid = false;
      break;
    }

    if(!$valid) continue;
    
    echo "t4g.url.query.set('{$key}', '{$val}');\n";
  }
?>

window.addEvent('domready', function()
{
  t4g.language.languages = ['en', 'de'];
  t4g.language.active = '<?php echo getLang() ?>';
});
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36889284-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body style="margin-top: 23px;">
  
  <div style="position: absolute; right: 20px; top: 35px;">
    <img src="http://bfp4f2.tools4games.com/img/page/t4g_logo.png" />
  </div>
  
<div class="sub extended" style="margin: 0px !important; width: 100% !important; margin-left: -10px !important;">
<?php

    $profiles = alxDatabaseManager::query
    ("
      SELECT COUNT(DISTINCT soldierId) AS c FROM profiles
    ")->fetch();
    
    $soldiers = alxDatabaseManager::query
    ("
      SELECT COUNT(DISTINCT nucleusId) AS c FROM profiles
    ")->fetch();
    
?>
Unique Soldiers in Database: <span class="hl"><?=f($profiles->c, 0, new stdClass, true) ?></span> | 
Unique Profiles in Database: <span class="hl"><?=f($soldiers->c, 0, new stdClass, true) ?></span>

<br /><br />
<span>Rankings:</span> 
<a class="hl" href="/en/ranking?filter2=nbl">Global</a> | <a class="hl" href="/en/ranking/weapons?filter2=nbl">Weapons</a>
</div>

<?php $this->insertContainer('content') ?>


<style>
  
  table.ranking thead tr th.hl,
  div.searchResult:hover,
  table.ranking tbody tr.searchResult td
  {
    background: #b7deed; /* Old browsers */
background: -moz-linear-gradient(top, #b7deed 0%, #71ceef 50%, #21b4e2 51%, #b7deed 100%) !important; /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#b7deed), color-stop(50%,#71ceef), color-stop(51%,#21b4e2), color-stop(100%,#b7deed)) !important; /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top, #b7deed 0%,#71ceef 50%,#21b4e2 51%,#b7deed 100%) !important; /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top, #b7deed 0%,#71ceef 50%,#21b4e2 51%,#b7deed 100%) !important; /* Opera 11.10+ */
background: -ms-linear-gradient(top, #b7deed 0%,#71ceef 50%,#21b4e2 51%,#b7deed 100%) !important; /* IE10+ */
background: linear-gradient(to bottom, #b7deed 0%,#71ceef 50%,#21b4e2 51%,#b7deed 100%) !important; /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b7deed', endColorstr='#b7deed',GradientType=0 ) !important; /* IE6-9 */

    color: #111 !important;
  }
  
  table.ranking tbody tr.searchResult td a,
  table.ranking tbody tr.searchResult td span
  {
    color: #111 !important;
  }
  
  table.ranking tbody tr:not(.banned):hover td
  {

  }
  
  div.extended
  {
    width: 97% !important;
  }

  #container
  {
    width: 1600px;
  }

  table.ranking 
  {
    font-size: 9pt !important;
    border-left: 1px solid #333;
  }
  
  table.ranking tbody tr td
  {
    border-right: 1px solid #333;
    vertical-align: middle;
    padding: 5px !important;
  }
  
  table.ranking tbody tr td,
  div.searchResult
  {
    background: #292929; /* Old browsers */
background: -moz-linear-gradient(top,  #292929 0%, #272727 50%, #242424 51%, #272727 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#292929), color-stop(50%,#272727), color-stop(51%,#242424), color-stop(100%,#272727)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #292929 0%,#272727 50%,#242424 51%,#272727 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #292929 0%,#272727 50%,#242424 51%,#272727 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #292929 0%,#272727 50%,#242424 51%,#272727 100%); /* IE10+ */
background: linear-gradient(to bottom,  #292929 0%,#272727 50%,#242424 51%,#272727 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#292929', endColorstr='#272727',GradientType=0 ); /* IE6-9 */
  }
  
  table.ranking tbody tr:nth-child(odd) td,
  div.searchResult:nth-child(odd)
  {
background: #333333; /* Old browsers */
background: -moz-linear-gradient(top,  #333333 0%, #303030 50%, #2d2d2d 51%, #323232 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#333333), color-stop(50%,#303030), color-stop(51%,#2d2d2d), color-stop(100%,#323232)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #333333 0%,#303030 50%,#2d2d2d 51%,#323232 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #333333 0%,#303030 50%,#2d2d2d 51%,#323232 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #333333 0%,#303030 50%,#2d2d2d 51%,#323232 100%); /* IE10+ */
background: linear-gradient(to bottom,  #333333 0%,#303030 50%,#2d2d2d 51%,#323232 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#333333', endColorstr='#323232',GradientType=0 ); /* IE6-9 */


  }
  
  table.ranking tbody tr.banned td
  {
background: #333333; /* Old browsers */
background: -moz-linear-gradient(top,  #333333 0%, #303030 50%, #2d2d2d 51%, #323232 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#333333), color-stop(50%,#303030), color-stop(51%,#2d2d2d), color-stop(100%,#323232)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #333333 0%,#303030 50%,#2d2d2d 51%,#323232 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #333333 0%,#303030 50%,#2d2d2d 51%,#323232 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #333333 0%,#303030 50%,#2d2d2d 51%,#323232 100%); /* IE10+ */
background: linear-gradient(to bottom,  #333333 0%,#303030 50%,#2d2d2d 51%,#323232 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#333333', endColorstr='#323232',GradientType=0 ); /* IE6-9 */


  }
  
  table.ranking thead tr th:not(.noSort)
  {
    cursor: pointer;
  }
</style>

<script src="http://tools4games.com/topbar.js"></script>

</body>
</html>

