<?php

class SdbController extends alxController
{
  function profileCharts($get)
  {
    $soldierId = (int) $get->soldierId;
    
    $keys = array
    (
      'kills', 
      'kpm',
      'kpg',
      'headshotratio',
      'accuracy',
      'killratio',
      array('infantryPct', 'vehiclePct', 'teamPct'),
      'killStreak',
      'bestScore',
      'bestRangedKill'
    );
    
    $last = alxDatabaseManager::query
    ("
      SELECT date FROM profile_stats WHERE soldierId = '{$soldierId}' ORDER BY date DESC LIMIT 1
    ")->fetch();
    
    $profile = alxDatabaseManager::query
    ("
      SELECT * FROM profiles WHERE soldierId = '{$soldierId}' LIMIT 1
    ")->fetch();
    
    if($last->date < time()-604800)
    {
      file_get_contents('http://blacklist.tools4games.com/bl/fetchStatsDo.php?n=' . $profile->nucleusId);
    }
    
    $profiles = alxDatabaseManager::query
    ("
      SELECT * FROM profiles WHERE nucleusId = '{$profile->nucleusId}'
    ");
    
    $this->add('keys', $keys);
    $this->add('profile', $profile);
    $this->add('profiles', $profiles);
    $this->add('soldierId', $soldierId);
    
    $this->render();
  }
  
  function profileStats($get)
  {
    $soldierId = (int) $get->soldierId;
    
    $keys = array
    (
      'games' => 'Games',
      'timePlayed' => 'Time',
      'kills' => 'Kills',
      'headshotratio' => 'HS %',
      'accuracy' => 'Acc.',
      'bestScore' => 'Best Score',
      'killStreak' => 'Kill Streak',
      'suicides' => 'Suicides',
      'deaths' => 'Deaths',
      'deathStreak' => 'Death Streak',
      'kpm' => 'K/M',
      'kpg' => 'K/G',
      'killratio' => 'K/D',
      'headshots' => 'Headshots',
      'hits' => 'Hits',
      'shots' => 'Shots',
      'misses' => 'Misses',
      'score' => 'Score',
      'infantryScore' => 'Inf. Score',
      'vehicleScore' => 'Veh. Score',
      'teamScore' => 'Team Score',
      'teamPct' => 'Team %',
      'vehiclePct' => 'Veh. %',
      'infantryPct' => 'Inf. %',
      'ispm' => 'Inf. S/M',
      'vspm' => 'Veh. S/M',
      'tspm' => 'Team S/M',
      'spm' => 'S/M',
      'spg' => 'S/G',
      'cpcaps' => 'Flag Caps.',
      'cpneut' => 'Flag Neut.',
      'winratio' => 'W/G',
      'wins' => 'Wins',
      'losses' => 'Losses',
    );
    
    $callbacks = array
    (
      'default' => function($v)
      {
        return number_format($v, 0, '.', '\'');
      },
      'timePlayed' => function($v)
      {
        return number_format($v / 3600, 0, '.', '\'') . 'h';
      },
      'teamPct' => function($v)
      {
        return number_format($v, 1, '.', '\'') . '%';
      },
      'infantryPct' => function($v)
      {
        return number_format($v, 1, '.', '\'') . '%';
      },
      'vehiclePct' => function($v)
      {
        return number_format($v, 1, '.', '\'') . '%';
      },
      'headshotratio' => function($v)
      {
        return number_format($v * 100, 1, '.', '\'') . '%';
      },
      'accuracy' => function($v)
      {
        return number_format($v * 100, 1, '.', '\'') . '%';
      }
    );
    
    $last = alxDatabaseManager::query("SELECT date FROM profile_stats WHERE soldierId = '{$soldierId}' ORDER BY date DESC LIMIT 1")->fetch();
    
    $profile = alxDatabaseManager::query("SELECT * FROM profiles WHERE soldierId = '{$soldierId}' LIMIT 1")->fetch();
    
    if($last->date < time()-604800)
    {
      file_get_contents('http://blacklist.tools4games.com/bl/fetchStatsDo.php?n=' . $profile->nucleusId);
    }
    
    $profiles = alxDatabaseManager::query("SELECT * FROM profiles WHERE nucleusId = '{$profile->nucleusId}'");

    $stats = alxDatabaseManager::query("SELECT * FROM profile_stats WHERE soldierId = '{$soldierId}' ORDER BY date DESC LIMIT 10");
    
    $this->add('keys', $keys);
    $this->add('callbacks', $callbacks); 
    $this->add('stats', $stats); 
    $this->add('profile', $profile);
    $this->add('profiles', $profiles);
    $this->add('soldierId', $soldierId);
    
    $this->render();
  }
}
