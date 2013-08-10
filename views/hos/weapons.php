<?php 

$f = function($v, $dec=0)
{
  if(is_string($v)) return $v;
  
  return number_format($v, $dec, '.', '\'');
};

$h = function($v)
{
  $new = '';
  $opt = '*';
  
  for($i=0,$c=strlen($v);$i<$c;$i++)
  {
    if($i %4 == 0)
    {
      $new.= '<span style="color: #e22">' . $opt . '</span>';
    }
    else
    {
      $new.= $v[$i];
    }
  }
  
  return $new;
};


$w = 100;

?>

<div class="sub extended">
  Please note that most of the Data we got is from submitted People at the T4G Blacklist. So a there's a potential of a large portion of them being actual Cheaters, which can be seen quite dramatically in the 'Average Headshot Ratio' Section.
  <br /><br />
  However, there are 'legit' People mixed in on purpose.
</div>

<div class="sub extended">
  <a href="#" onclick="t4g.url.query.set('type', ''); t4g.url.redirect();">Hall of Shame</a> 
   |
  <a href="#" onclick="t4g.url.query.set('type', 'weaponsPopularity'); t4g.url.redirect();">Weapon Popularity</a> 
   | 
  <a href="#" onclick="t4g.url.query.set('type', 'weaponsAccuracy'); t4g.url.query.set('key', ''); t4g.url.redirect();">Average Accuracy</a>
   | 
  <a href="#" onclick="t4g.url.query.set('type', 'weaponsAccuracy'); t4g.url.query.set('key', 'hs'); t4g.url.redirect();">Average Headshot-Ratio</a>
   | 
  <a href="#" onclick="t4g.url.query.set('type', 'weaponsAccuracy'); t4g.url.query.set('key', 'brk'); t4g.url.redirect();">Average Best Ranged Kill</a>
   | 
  <a href="#" onclick="t4g.url.query.set('type', 'weaponsAccuracy'); t4g.url.query.set('key', 'dpb'); t4g.url.redirect();">Average Damage / Bullet</a>
  
  <br /><br />
  
  Type: 
  <a href="#" onclick="t4g.url.query.set('cat', 'AR'); t4g.url.redirect()">Assault Rifles</a>
   | 
  <a href="#" onclick="t4g.url.query.set('cat', 'SMG'); t4g.url.redirect()">Sub Machine Guns</a>
   | 
  <a href="#" onclick="t4g.url.query.set('cat', 'LMG'); t4g.url.redirect()">Light Machine Guns</a>
   | 
  <a href="#" onclick="t4g.url.query.set('cat', 'SRIFLE'); t4g.url.redirect()">Sniper Rifles</a>
   | 
  <a href="#" onclick="t4g.url.query.set('cat', 'SG'); t4g.url.redirect()">Shotguns</a>
   | 
  <a href="#" onclick="t4g.url.query.set('cat', 'PISTOL'); t4g.url.redirect()">Pistols</a>
</div>

<div class="sub extended">

  <table class="data" style="width: <?php echo $w ?>%; float: left;margin-bottom:10px;">
    <thead>
      <tr>
        <th><?php echo $data->label ?></th>
        <th style="text-align: right"><?php echo $data->noTime ? $f($data->total) . (@$data->perc) : sec2hms($data->total) ?></th>
        <?php if(@$data->total != '-') :?>
        <th style="text-align: center">100%</th>
        <?php endif ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($data->stats as $label => $value): ?>
      <tr>
        <td><?php echo $label ?></td>
        <td style="text-align: right"><?php echo $data->noTime ? $f($value) . (@$data->perc) : sec2hms($value) ?></td>
        <?php if(@$data->total != '-') :?>
        <td style="text-align: center"><?php echo $f(($value / $data->total) * 100, 1) ?></td>
        <?php endif ?>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  
</div>
