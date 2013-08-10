<div class="sub extended">
  
  <p>
    The T4G Blacklist is a non-profit Project from <a href="http://tools4games.com" target="_blank">Tools4Games</a>. T4G is a Project started by roennel, CriscoSpectrum and Sforek.
    <br /><br />
    It's a community driven Project mainly supported by the <a href="http://honey-badgers.com" target="_blank">Honey Badgers Community</a> and the <a href="http://oldguys.eu" target="_blank">Old Guys Clan</a>.
    <!--<br /><br />
    T4G Blacklist is also part of the <a href="http://alliance.tools4games.com" target="_blank">T4G Anti Cheat Alliance</a>.-->
  </p>
    
</div>

<?php $w = '279.5' ?>

<?php

$moderators = array
(
  'Ruemel' => 'de',
  // 'Prinz-Einsam' => 'de',
  // 'ObeeG' => 'uk',
  'Pellets' => 'uk',
  // 'samanaslt' => 'uk',
  'Monotreme' => 'us',
  // 'SHOCKED.e' => 'us',
  //'3Stan2112' => 'us',
  //'JonSwift' => 'us',
  'De[v]' => 'us',
  // 'Petrusim' => 'hr',
  // 'Skithawk' => 'fi',
  'crazycanuck4' => 'ca',
  // 'SoulsReaper' => 'il',
  // 'TaxmanB' => 'fr',
  'Mrozinka' => 'pl',
  'Bizzu' => 'pl',
  'TonyVercetti' => 'pl',
  //'Romin' => 'pl',
  //'S3ci0r' => 'pl',
  //'YoZza' => 'ro',
  'DeltaForce' => 'de',
  // 'MurrayD' => 'scotland',
  // 'LazzDaMojo' => 'de',
  // 'VirtualEmbrace' => 'de',
  //'lisdexyc' => 'us',
  //'RoyalPaine' => 'de',
  //'Tamfanu' => 'de',
  //'Indi' => 'de',
  // 'Onemeter' => 'de',
  // 'LordDarryl' => 'nl',
  // 'Jihad' => 'us',
  'GrzechuPL' => 'pl',
  // 'TacticalS' => 'nl',
  'Viking-DK' => 'dk',
  'R4ging' => 'fr',
  'salsita01' => 'at',
  'Chany' => 'de',
  'Chronox' => 'co',
  // 'MedicAlert' => 'us',
  'NoobZik' => 'fr',
  // 'NeoSan' => 'fr',
  //'Chizo' => 'de',
  //'H0LY-SHi7' => 'de',
  // 'Blacklight' => 'de',
  // 'Vodka' => 'de',
  //'TriviumF22' => 'us',
  'John_MacTavish25' => 'de',
  // 'KLOKRIEGER' => 'de',
  'Dani023' => 'rs',
  // 'Sir Blacklord' => 'de',
  'EnvyS' => 'sk',
  //'Abdo totti' => 'sa',
  'pjfem' => 'de',
  //'Sgt.Mexon' => 'ba',
  'Juzernejm' => 'yu',
  // '[TAF]favourite' => 'de',
  'kayack1' => 'fr',
  'Grunilg' => 'de',
  //'LittleBigCannon' => 'de',
  'p3rf0rat0r' => 'de',
  'strangers123' => 'no',
  'VSAF' => 'hu',
  // 'Dr.rulz' => 'ro',
  'Raul' => 'am',
  //'captainslow147' => 'uk',
  'DocOlds' => 'us',
  //'EnvoyEnd' => 'us',
  'HomeSen' => 'de',
  'Landstander' => 'us',
  'CHRISR1' => 'uk',
  'batwing' => 'pl',
  'kollez' => 'rs',
  'Pauli' => 'de',
  //'OperatorFox' => 'us',
  'VNKsnaiperis' => 'lv',
  'Jura666' => 'pl',
  '-Tigar-' => 'rs',
  'Kasiek' => 'pl'
);

$admins = array
(
  // 'Shureshot' => 'be',
  'RPGforYOU' => 'be',
  'Nord' => 'at',
  'Cpt.Carrot' => 'uk',
  'RIC0H' => 'uk',
  'sirphunkee' => 'us',
  'UPS-i-did-it-again' => 'de',
  'Sunbeam' => 'de',
  'BuckRaven' => 'se',
  'Soulman' => 'us',
  'ElReyDelMundo' => 'de',
  'Gazza' => 'de'
);

uksort($moderators, 'strcasecmp');
uksort($admins, 'strcasecmp');
// ksort($moderators, SORT_STRING | SORT_FLAG_CASE);
// ksort($admins, SORT_STRING | SORT_FLAG_CASE);

?>

<div class="sub" style="width: <?php echo $w ?>px">
  
  <h2>Moderators (<?php echo count($moderators) ?>)</h2>
  
  <ul>
    <?php foreach($moderators as $name => $flag): ?>
    
    <li><div class="flag flag_<?php echo $flag ?>"></div><?php echo $name ?></li>
  
    <?php endforeach ?>
  </ul>
</div>

<div class="sub" style="width: <?php echo $w ?>px">
  
  <h2>Admins (<?php echo count($admins) ?>)</h2>
  
  <ul>
    <?php foreach($admins as $name => $flag): ?>
    
    <li><div class="flag flag_<?php echo $flag ?>"></div><?php echo $name ?></li>
  
    <?php endforeach ?>
  </ul>
  
</div>

<div class="sub" style="width: <?php echo $w ?>px">
  
  <h2>Tech (3)</h2>
  
  <ul>
    
    <li><div class="flag flag_ch"></div>roennel</li>
    <li><div class="flag flag_in"></div>ay.j</li>
    <li><div class="flag flag_de"></div>my-ouZo</li>
    
  </ul>
  
</div>

