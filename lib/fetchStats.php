<?php

function getJSON($url)
{
  $con = file_get_contents($url);
  
  return json_decode($con);
}

function db($qry)
{
  return alxDatabaseManager::query($qry);
}

function check($nucleusId)
{

$date = time();

$soldiers = getJSON("http://battlefield.play4free.com/en/profile/soldiers/{$nucleusId}");

foreach($soldiers->data as $soldier)
{
  $isMain = $soldier->isMain ? '1' : '0';
  
  db
  ("
    INSERT INTO profile_soldiers 
    SET soldierId = '{$soldier->id}', date = '{$date}', level = '{$soldier->level}',
    xp = '{$soldier->xp}', xpForNextLevel = '{$soldier->xpForNextLevel}', isMain = '{$isMain}',
    name = '{$soldier->name}'
  ");
  
  $coreStats = getJSON("http://battlefield.play4free.com/en/profile/stats/{$nucleusId}/{$soldier->id}?g=[%22CoreStats%22,%22BadPlayerStats%22,%22GameEventStats%22,%22GameModeStats%22,%22GameModeMapStats%22,%22WeaponStats%22,%22MapStats%22,%22VehicleStats%22,%22RushMapStats%22]&_={$date}");
  $gameEvents = $coreStats;
  $gameModes = $coreStats;
  $weaponStats = $coreStats;
  $vehicleStats = $coreStats;
  $mapStats = $coreStats;
  $gameModeMapStats = $coreStats;
  $rushMapStats = $coreStats;
  
  
  $c = $coreStats->data->CoreStats;

    db
    ("
      INSERT INTO profile_stats 
      SET soldierId = '{$soldier->id}', date = '{$date}', games = '{$c->games}', timePlayed = '{$c->timePlayed}', score = '{$c->score}', infantryScore = '{$c->infantryScore}',
      vehicleScore = '{$c->vehicleScore}', teamScore = '{$c->teamScore}', teamPct  = '{$c->teamPct}', vehiclePct = '{$c->vehiclePct}', infantryPct = '{$c->infantryPct}',
      ispm = '{$c->ispm}', vspm = '{$c->vspm}', tspm = '{$c->tspm}', spm = '{$c->spm}', spg = '{$c->spg}', kills = '{$c->kills}', deaths = '{$c->deaths}',
      killStreak = '{$c->killStreak}', deathStreak = '{$c->deathStreak}', headshots = '{$c->headshots}', headshotratio = '{$c->headshotratio}', kpm = '{$c->kpm}',
      kpg = '{$c->kpg}', cpcaps = '{$c->cpcaps}', cpneut = '{$c->cpneut}', bestScore = '{$c->bestScore}', wins = '{$c->wins}', losses = '{$c->losses}', 
      suicides = '{$c->suicides}', hits = '{$c->hits}', shots = '{$c->shots}', misses = '{$c->misses}', accuracy = '{$c->accuracy}', killratio = '{$c->killratio}', 
      winratio = '{$c->winratio}', meleeKills = '{$c->meleeKills}', vehicleKills = '{$c->vehicleKills}', roadKills = '{$c->roadKills}', runover = '{$c->runover}', 
      destroyedVehicles = '{$c->destroyedVehicles}', killedByMelee = '{$c->killedByMelee}', killedByVehicles = '{$c->killedByVehicles}', bestRangedKill = '{$c->bestRangedKill}',
      quits = '{$c->quits}', quitratio = '{$c->quitratio}', suicideratio = '{$c->suicideratio}', damageDealt = '{$c->damageDealt}', damageTaken = '{$c->damageTaken}'
    ");

  $bp = $coreStats->data->BadPlayerStats;

  db
  ("
    INSERT INTO profile_badPlayerStats 
    SET soldierId = '{$soldier->id}', date = '{$date}', timesKicked = '{$bp->timesKicked}', timesBanned = '{$bp->timesBanned}', timesStatsReset = '{$bp->timesStatsReset}', teamKills = '{$bp->teamKills}'
  ");

  //$gameEvents = getJSON("http://battlefield.play4free.com/en/profile/stats/{$nucleusId}/{$soldier->id}?g=[%22GameEventStats%22]&_={$date}");

  foreach($gameEvents->data->GameEventStats as $g)
  {
    db
    ("
      INSERT INTO profile_gameEventStats 
      SET soldierId = '{$soldier->id}', date = '{$date}', eventId = '{$g->id}', `count` = '{$g->count}'
    ");
  }

  //$gameModes = getJSON("http://battlefield.play4free.com/en/profile/stats/{$nucleusId}/{$soldier->id}?g=[%22GameModeStats%22]&_={$date}");
 
  foreach($gameModes->data->GameModeStats as $gm)
  {
    db
    ("
      INSERT INTO profile_gameModeStats 
      SET soldierId = '{$soldier->id}', date = '{$date}', timePlayed = '{$gm->timePlayed}', games = '{$gm->games}', wins = '{$gm->wins}', losses = '{$gm->losses}',
      score = '{$gm->score}', spm = '{$gm->spm}', spg = '{$gm->spg}', bestScore = '{$gm->bestScore}', winratio = '{$gm->winratio}', kills = '{$gm->kills}',
      deaths = '{$gm->deaths}', kpm = '{$gm->kpm}', kpg = '{$gm->kpg}', killratio = '{$gm->killratio}', quits = '{$gm->quits}', quitratio = '{$gm->quitratio}',
      cpcaps = '{$gm->cpcaps}', cpneut = '{$gm->cpneut}', gameMode = '{$gm->gameMode}'
      
    ");
  }

  //$weaponStats = getJSON("http://battlefield.play4free.com/en/profile/stats/{$nucleusId}/{$soldier->id}?g=[%22WeaponStats%22]&_={$date}");

  foreach($weaponStats->data->WeaponStats as $wp)
  {
    $op = $wp->description->ownedPermanent ? '1' : '0';
    $ow = $wp->description->owned ? '1' : '0';
    
    db
    ("
      INSERT INTO profile_weaponStats 
      SET soldierId = '{$soldier->id}', date = '{$date}', timeUsed = '{$wp->timeUsed}', timesUsed = '{$wp->timesUsed}', kills = '{$wp->kills}', kpm = '{$wp->kpm}',
      deaths = '{$wp->deaths}', deathsBy = '{$wp->deathsBy}', hits = '{$wp->hits}', shots = '{$wp->shots}', misses = '{$wp->misses}', accuracy = '{$wp->accuracy}',
      headshots = '{$wp->headshots}', headshotratio = '{$wp->headshotratio}', bestRangedKill = '{$wp->bestRangedKill}', damageDealt = '{$wp->damageDealt}',
      damageTaken = '{$wp->damageTaken}', dpb = '{$wp->dpb}', weaponId = '{$wp->id}', usecount = '{$wp->description->usecount}', owned = '{$ow}',
      ownedPermanent = '{$op}'
    ");
  }

  //$vehicleStats = getJSON("http://battlefield.play4free.com/en/profile/stats/{$nucleusId}/{$soldier->id}?g=[%22VehicleStats%22]&_={$date}");

  foreach($vehicleStats->data->VehicleStats as $v)
  {
    db
    ("
      INSERT INTO profile_vehicleStats 
      SET soldierId = '{$soldier->id}', date = '{$date}', timeUsed = '{$v->timeUsed}', kills = '{$v->kills}', deaths = '{$v->deaths}', destroyed = '{$v->destroyed}',
      vehiclesDestroyed = '{$v->vehiclesDestroyed}', killedBy = '{$v->killedBy}', roadKills = '{$v->roadKills}', runOver = '{$v->runOver}', damageDealt = '{$v->damageDealt}',
      damageTaken = '{$v->damageTaken}', vehicleId = '{$v->id}'
    ");
  }

  //$mapStats = getJSON("http://battlefield.play4free.com/en/profile/stats/{$nucleusId}/{$soldier->id}?g=[%22MapStats%22]&_={$date}");
  
  foreach($mapStats->data->MapStats as $v)
  {
    db
    ("
      INSERT INTO profile_mapStats 
      SET soldierId = '{$soldier->id}', date = '{$date}', timePlayed = '{$v->timePlayed}', games = '{$v->games}', wins = '{$v->wins}', losses = '{$v->losses}',
      score = '{$v->score}', spm = '{$v->spm}', spg = '{$v->spg}', bestScore = '{$v->bestScore}', winratio = '{$v->winratio}', kills = '{$v->kills}', deaths = '{$v->deaths}',
      kpm = '{$v->kpm}', kpg = '{$v->kpg}', killratio = '{$v->killratio}', quits = '{$v->quits}', quitratio = '{$v->quitratio}', cpcaps = '{$v->cpcaps}', cpneut = '{$v->cpneut}',
      gameMode = '{$v->gameMode}', mapId = '{$v->id}'
    ");
  }

  //$gameModeMapStats = getJSON("http://battlefield.play4free.com/en/profile/stats/{$nucleusId}/{$soldier->id}?g=[%22GameModeMapStats%22]&_={$date}");
  
  foreach($gameModeMapStats->data->GameModeMapStats as $v)
  {
    db
    ("
      INSERT INTO profile_mapStats 
      SET soldierId = '{$soldier->id}', date = '{$date}', timePlayed = '{$v->timePlayed}', games = '{$v->games}', wins = '{$v->wins}', losses = '{$v->losses}',
      score = '{$v->score}', spm = '{$v->spm}', spg = '{$v->spg}', bestScore = '{$v->bestScore}', winratio = '{$v->winratio}', kills = '{$v->kills}', deaths = '{$v->deaths}',
      kpm = '{$v->kpm}', kpg = '{$v->kpg}', killratio = '{$v->killratio}', quits = '{$v->quits}', quitratio = '{$v->quitratio}', cpcaps = '{$v->cpcaps}', cpneut = '{$v->cpneut}',
      gameMode = '{$v->gameMode}', mapId = '{$v->id}'
    ");
  }
  
  
  foreach($rushMapStats->data->RushMapStats as $rm)
  {
	db
	("
	  INSERT INTO profile_rushMapStats
	  SET soldierId = '{$soldier->id}', date = '{$date}', mcomarm = '{$rm->mcomarm}', mcomdisarm = '{$rm->mcomdisarm}', mcomdest = '{$rm->mcomdest}',
	  attacker = '{$rm->attacker}', defender = '{$rm->defender}', attackerwins = '{$rm->attackerwins}', defenderwins = '{$rm->defenderwins}',
	  attackerlosses = '{$rm->attackerlosses}', defenderlosses = '{$rm->defenderlosses}', mapId = '{$rm->id}'
	");
  }
}

return $date;

}