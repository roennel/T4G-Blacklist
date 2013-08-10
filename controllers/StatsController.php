<?php

class StatsController extends alxController
{
  function freeloaders($get)
  {
    $kills = @$get->kills ?: 500;
    
    $get = function($weapons) use($kills)
    {
      $f = alxDatabaseManager::query("SELECT DISTINCT p.soldierId, pws.weaponId FROM profiles AS p, profile_weaponStats AS pws WHERE p.soldierId = pws.soldierId AND pws.kills > '{$kills}' AND pws.weaponId NOT IN({$weapons})");
      $p = alxDatabaseManager::query("SELECT DISTINCT p.soldierId, pws.weaponId FROM profiles AS p, profile_weaponStats AS pws WHERE p.soldierId = pws.soldierId AND pws.kills > '{$kills}' AND pws.weaponId IN({$weapons})");
      
      $_f = alxDatabaseManager::rows($f->getRawQuery());
      $_p = alxDatabaseManager::rows($p->getRawQuery());
      
      $r = new stdClass;
      $r->total = $_f + $_p;
      $r->f = $_f;
      $r->p = $_p;
      
      return $r; 
    };
    
    //$all = $get('3114,3008,3041,3106,3021,3065,3053,3080,3100,3033,3117,3014,3063,3075,3043,3089,3092,3067,3010,3077,3048,3086,3058,3099,3011,3038,3025,3129,3082,3116,3087,3030,3081,3042,3090,3126,3094,3101,3036,3051,3031,3131,3072,3121,3103,3000,3066,3026,3049,3032,3127,3999,3012,3076,3020,3073,3124,3045,3107,3111,3078,3009,3128,3003,3097,3123,3110,3071,3104,3034,3084,3050,3015,3093,3052,3016,3068,3070,3047,3098,3055,3095,3024,3017,3022,3019,3001,3054,3105,3085,3002,3040,3039,3060,3069,3035,3112,3088,3109,3113,3059,3044,3125,3064,3122,3007,3119,3028,3023,3096,3091,3061,3102,3037,3108,3056,3118,3074,3120,3062,3046,3057,3029,3079,3083,3018,3115,3130');
    $all = $get('3114,3008,3106,3065,3053,3080,3100,3033,3117,3014,3063,3075,3089,3092,3067,3010,3077,3048,3086,3058,3099,3011,3038,3025,3129,3082,3116,3087,3030,3081,3042,3090,3126,3094,3101,3036,3031,3072,3121,3103,3000,3066,3026,3049,3032,3127,3999,3012,3076,3073,3045,3107,3111,3078,3128,3003,3097,3110,3071,3104,3034,3084,3050,3015,3093,3052,3016,3068,3070,3047,3098,3055,3095,3024,3017,3022,3001,3054,3105,3085,3002,3040,3039,3060,3069,3035,3112,3088,3109,3113,3059,3044,3064,3122,3007,3119,3028,3023,3096,3091,3061,3102,3108,3056,3074,3120,3062,3046,3057,3029,3079,3083,3018,3130');

    $this->add('all', $all);
    
    $this->render(); 
  }

  function check($get)
  {
    if(!isLogged() && !isMod()) return;
    
    if(@$get->nucleusId)
    {
      $ts = @check($get->nucleusId);
      
      $this->redirect('stats', 'check', null, array
      (
        'soldierId' => $get->soldierId,
        'ts' => $ts
      ));
      exit();
      return;
    }
    
    if(@$get->soldierId)
    {
      $soldierId = (int) $get->soldierId;
      $ts = (int) $get->ts;
      
      $key = @$get->key ?: 'kills';
      $keyLabel = ucFirst($key);
      
      $profile = alxDatabaseManager::query("SELECT * FROM profiles WHERE soldierId = '{$soldierId}' LIMIT 1")->fetch();
      $core = alxDatabaseManager::query("SELECT {$key} AS c FROM profile_stats WHERE soldierId = '{$soldierId}' AND date = '{$ts}' LIMIT 1")->fetch();
      $weapon = alxDatabaseManager::query("SELECT SUM({$key}) AS c FROM profile_weaponStats WHERE soldierId = '{$soldierId}' AND date = '{$ts}'")->fetch();
      $vehicle = alxDatabaseManager::query("SELECT SUM({$key}) AS c FROM profile_vehicleStats WHERE vehicleId != '12' AND soldierId = '{$soldierId}' AND date = '{$ts}'")->fetch();
      $map = alxDatabaseManager::query("SELECT SUM({$key}) AS c FROM profile_mapStats WHERE soldierId = '{$soldierId}' AND date = '{$ts}'")->fetch();
      $gameMode = alxDatabaseManager::query("SELECT SUM({$key}) AS c FROM profile_gameModeStats WHERE soldierId = '{$soldierId}' AND date = '{$ts}'")->fetch();
 
      $weapons = alxDatabaseManager::query("SELECT * FROM profile_weaponStats WHERE soldierId = '{$soldierId}' AND date = '{$ts}'");
      $vehicles = alxDatabaseManager::query("SELECT * FROM profile_vehicleStats WHERE vehicleId != '12' AND soldierId = '{$soldierId}' AND date = '{$ts}'");
      
      
      $this->add('ts', $ts);
      $this->add('key', $key);
      $this->add('keyLabel', $keyLabel);
      $this->add('profile', $profile);
      $this->add('core', $core);
      $this->add('weapon', $weapon);
      $this->add('vehicle', $vehicle);
      $this->add('map', $map);
      $this->add('gameMode', $gameMode);
      $this->add('weapons', $weapons);
      $this->add('vehicles', $vehicles);
    }
    
    $this->add('soldierId', @$get->soldierId);
    
    $this->render(); 
  }
  
  function weapons($get)
  {
    if(!isLogged() && !isMod()) return;
    
    $weapons = alxDatabaseManager::query
    ("
      SELECT weaponId 
      FROM profile_weaponStats 
      WHERE weaponId < 4000 AND weaponId >= 3000 
      GROUP BY weaponId
      ORDER BY weaponId ASC 
    ");
    
    $this->add('weapons', $weapons);
     
    $this->render();
  }
  
  function weaponCharts($get)
  {
    $this->respond();
  }
  
  function events($get)
  {
    if(!isLogged() && !isMod()) return;
    
    $eventId = @$get->eventId ?: false;
    
    $w_eventId = '';
    
    if($eventId)
    {
      $w_eventId = " AND ges.eventId = '{$eventId}'";
    }
    
    $limit = 100;
    $order = 'count';
    $dir = 'DESC';
    
    $events = alxDatabaseManager::query("SELECT ges.*, p.name FROM profile_gameEventStats AS ges, profiles AS p WHERE p.soldierId = ges.soldierId{$w_eventId} ORDER BY {$order} {$dir} LIMIT {$limit}");
    
    $this->add('events', $events);
    $this->add('eventId', $eventId);
    
    $this->render();
  }
  
  function updates($get)
  {
    if(!isLogged() && !isMod()) return;
    
    $updates = alxDatabaseManager::query
    ("
      SELECT COUNT(ps.profileSoldierId) AS c, ps.date, p.name 
      FROM profile_soldiers AS ps, profiles AS p 
      WHERE ps.soldierId = p.soldierId
      ORDER BY ps.date DESC
    ");
    
    $this->add('updates', $updates);
    
    $this->render();
  }
  
  function core($get)
  {
    if(!isLogged() && !isMod()) return;
    
    $key = @$get->key ?: 'games';
    
    $limit = 100;
    $dir = 'DESC';
    
    $keys = array
    (
      'games' => 'Games',
      'timePlayed' => 'Time',
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
      'kills' => 'Kills',
      'deaths' => 'Deaths',
      'killStreak' => 'Kill Streak',
      'deathStreak' => 'Death Streak',
      'headshots' => 'HS',
      'headshotratio' => 'HS %',
      'kpm' => 'K/M',
      'kpg' => 'K/G',
      'cpcaps' => 'Flag Caps.',
      'cpneut' => 'Flag Neut.',
      'bestScore' => 'Best Score',
      'wins' => 'Wins',
      'losses' => 'Losses',
      'suicides' => 'Suicides',
      'hits' => 'Hits',
      'shots' => 'Shots',
      'misses' => 'Misses',
      'accuracy' => 'Acc.',
      'killratio' => 'K/D',
      'winratio' => 'W/G'
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
    
    $core = alxDatabaseManager::query("SELECT st.*, p.name FROM profile_stats AS st, profiles AS p WHERE p.soldierId = st.soldierId ORDER BY {$key} {$dir} LIMIT {$limit}");
    
    $this->add('core', $core);
    $this->add('key', $key);
    $this->add('keys', $keys);
    $this->add('callbacks', $callbacks); 
   
    $this->render();
  }
}
