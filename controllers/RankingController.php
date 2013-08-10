<?php
//require '/var/www/t4g_blacklist/bl/fetchStats.php';

class RankingController extends alxController
{
  private $updateTime = 604800;
  
  function fetchStats($get)
  {
    if(!@$get->nucleusId) return;
    
    $nucleusId = (int) mysql_real_escape_string($get->nucleusId);
    
    $check = alxDatabaseManager::query
    ("SELECT ps.date FROM profile_stats AS ps, profiles AS p WHERE p.nucleusId = '{$nucleusId}' AND p.soldierId = ps.soldierId ORDER BY ps.date DESC LIMIT 1")->fetch();
    
    $done = false;
    
    if((int) @$check->date <= time() - $this->updateTime)
    {
      //check($nucleusId);

      $done = true;
    }
    
    $this->respondString(json_encode(array('success' => $done)));
  }
  
  function searchName($get)
  {
    if(!@$get->name) return;
    
    if(strlen($get->name) <= 3) return;
    
    $json = array();
    
    $search = mysql_real_escape_string($get->name);
    
    $check = alxDatabaseManager::fetchMultiple
    ("
      SELECT DISTINCT name, soldierId FROM profiles WHERE name LIKE '%{$search}%' ORDER BY name ASC
    ");
    
    $ex = array();
    $json['soldiers'] = array();
    
    foreach($check as $soldier)
    {
      if(in_array($soldier->name, $ex)) continue;
      
      $json['soldiers'][] = $soldier;
      
      $ex[] = $soldier->name;
    }
    
    $this->respondString(json_encode($json));
  }
  
  function search($get)
  {
    header('context-type: text/plain');
    
    if(!@$get->name && !@$get->soldierId) return;
    
    $json = array();
    
    $name = @mysql_real_escape_string($get->name);
    $soldierId = @mysql_real_escape_string($get->soldierId);
    
    if(@$get->name)
    {
      $soldierId = alxDatabaseManager::query
      ("
        SELECT soldierId FROM profiles WHERE name = '{$name}' LIMIT 1
      ")->fetch();
      
      $soldierId = $soldierId->soldierId;
    }
    
    $json['soldierId'] = $soldierId;
    $json['rankings'] = array();
    
    $ranks = alxDatabaseManager::fetchMultiple
    ("
      SELECT * FROM ranking_cache WHERE soldierId = '{$soldierId}' ORDER BY date DESC
    ");
    
    $rankingIds = array();
    
    foreach($ranks as $rank)
    {
      if(in_array($rank->rankingId, $rankingIds)) continue;
      
      $_ = new stdClass;
      
      $_->rankingId = $rank->rankingId;
      $_->rank = $rank->rank;
      $_->update = $rank->date;
      
      $rankingIds[] = $rank->rankingId;
      
      $json['rankings'][] = $_;
    }
    
    $this->respondString(json_encode($json));
  }
  
  function index($get)
  {
    $sortKey = @$get->sort ?: 'kills';
    
    $sortKeys = 
    [
      'games',
      'timePlayed',
      'kills',
      'deaths',
      'killratio',
      'cpcaps',
      'cpneut',
      'ispm',
      'vspm',
      'tspm',
      'wins',
      'losses',
      'accuracy',
      'winratio',
      'meleeKills',
      'vehicleKills',
      'roadKills',
      'destroyedVehicles',
      'headshotratio',
      'killedByMelee',
      'runover',
      'suicides',
      'bestScore'
    ];
    
    $minGames = 50;
    
    if(!in_array($sortKey, $sortKeys))
    {
      $sortKey = 'games';
    }
    
    $w = '';
    
    if(@$get->kit)
    {
      $kit = (int) mysql_real_escape_string($get->kit);
      
      if($get->kit == 4) $kit = 0;
      
      $w = " AND p.kit = '{$kit}' ";
    }
    
    $w2 = '';
    
    switch(@$get->filter2)
    {
      case 'bl':
        //$w2 = " AND (SELECT COUNT(*) FROM bans AS b WHERE b.nucleusId = p.nucleusId) = 1 ";
      break;
        
      case 'nbl':
        $w2 = " AND (SELECT COUNT(*) FROM bans AS b WHERE b.nucleusId = p.nucleusId AND b.active = '1') = 0 ";
      break;
    }
    
    if(!isMod() && !isAdmin())
    {
      $w2 = " AND (SELECT COUNT(*) FROM bans AS b WHERE b.nucleusId = p.nucleusId AND b.active = '1') = 0 ";
    }
    
    $weapons = "3114,3008,3106,3065,3053,3080,3100,3033,3117,3014,3063,3075,3089,3092,3067,3010,3077,3048,3086,3058,3099,3011,3038,3025,3129,3082,3116,3087,3030,3081,3042,3090,3126,3094,3101,3036,3031,3072,3121,3103,3000,3066,3026,3049,3032,3127,3999,3012,3076,3073,3045,3107,3111,3078,3128,3003,3097,3110,3071,3104,3034,3084,3050,3015,3093,3052,3016,3068,3070,3047,3098,3055,3095,3024,3017,3022,3001,3054,3105,3085,3002,3040,3039,3060,3069,3035,3112,3088,3109,3113,3059,3044,3064,3122,3007,3119,3028,3023,3096,3091,3061,3102,3108,3056,3074,3120,3062,3046,3057,3029,3079,3083,3018,3130";
    
    if(@$get->onlyFreeloaders)
    {
      $w2.= "AND p.soldierId NOT IN (SELECT DISTINCT p2.soldierId FROM profiles AS p2, profile_weaponStats AS pws WHERE p2.soldierId = pws.soldierId AND pws.weaponId NOT IN({$weapons}))";

    }
    
    $additionalSort = '';
    
    if(@$get->mod == 'round')
    {
      $additionalSort = ' / ps.games';
      $minGames = 100;
    }
    
    if(@$get->mod == 'day')
    {
      $additionalSort = ' / (ps.timePlayed / 86400)';
      $minGames = 100;
    }
    
    if(@$get->mod == 'hour')
    {
      $additionalSort = ' / (ps.timePlayed / 3600)';
      $minGames = 100;
    }
    
    if(@$get->mod == 'minute')
    {
      $additionalSort = ' / (ps.timePlayed / 60)';
      $minGames = 100;
    }
    
    $start = 0;
    $limit = 100;
    
    
    $this->add('soldierNotFound', false);
        
    if(@$get->soldierId)
    {
      $soldierId = mysql_real_escape_string($get->soldierId);
      
      $rankingId = "global_{$minGames}_{$sortKey}";
      
      $rank = alxDatabaseManager::query
      ("
        SELECT rank FROM ranking_cache WHERE soldierId = '{$soldierId}' AND rankingId = '{$rankingId}' ORDER BY date DESC LIMIT 1
      ")->fetch();
      
      if((int) @$rank->rank <= 0)
      {
        $this->add('soldierNotFound', true);
      }
      
      $start = @$rank->rank - 11;
      if($start < 0) $start = 0;
      
      $limit = 21;
    }
    
    $items = alxDatabaseManager::fetchMultiple
    ("
      SELECT 
        ps.*, 
        p.name, 
        p.kit, 
        p.nucleusId,
        p.soldierId,
        p.level
      FROM 
        profile_stats AS ps, 
        profiles AS p
      WHERE 
        ps.soldierId = p.soldierId 
      AND 
        ps.games > {$minGames}
      AND 
        ps.kills > 100 
      AND 
        NOT EXISTS
        (
          SELECT * FROM profile_stats AS pw WHERE pw.soldierId = ps.soldierId AND pw.date > ps.date
        )
      {$w2}
      {$w}
      GROUP BY 
        ps.soldierId 
      ORDER BY 
        (ps.{$sortKey}{$additionalSort}) DESC, 
        ps.date DESC 
      LIMIT {$start}, {$limit}
    ");
    
    foreach($items as $item)
    {
      $t = time() - (86400 * 7);
      
      if($item->date <= $t)
      {
        $cmd = "nice php /var/www/t4g_blacklist/bl/fetchStatsDo.php {$item->nucleusId} 2>&1 & echo $!";
        pclose(popen($cmd, 'r'));
      }
    }
    
    $this->add('sortKey', $sortKey);
    
    $this->add('items', $items);
    $this->add('start', $start);
    
    $this->render();
  }
  
  function weapons($get)
  {
    $l = @$get->weapon ? 100 : 0;
    
    $sortKey = @$get->sort ?: 'kills';
    
    $sortKeys = 
    [
      'timeUsed',
      'kills',
      'kpm',
      'deaths',
      'deathsBy',
      'shots',
      'accuracy',
      'headshotratio',
      'bestRangedKill',
      'dpb',
      'damageDealt'
    ];
    
    if(!in_array($sortKey, $sortKeys))
    {
      $sortKey = 'kills';
    }
    
    $w = '';
    
    if(@$get->kit)
    {
      $kit = (int) mysql_real_escape_string($get->kit);
      
      if($get->kit == 4) $kit = 0;
      
      $w = " AND p.kit = '{$kit}' ";
    }
    
    $w2 = '';
    
    switch(@$get->filter2)
    {
      case 'bl':
        //$w2 = " AND (SELECT COUNT(*) FROM bans AS b WHERE b.nucleusId = p.nucleusId) = 1 ";
      break;
        
      case 'nbl':
        $w2 = " AND (SELECT COUNT(*) FROM bans AS b WHERE b.nucleusId = p.nucleusId AND b.active = '1') = 0 ";
      break;
    }
    
    if(!isMod() && !isAdmin())
    {
      $w2 = " AND (SELECT COUNT(*) FROM bans AS b WHERE b.nucleusId = p.nucleusId AND b.active = '1') = 0 ";
    }
    
    $w3 = '';
    
    if(@$get->weapon)
    {
      $weapon = (int) mysql_real_escape_string($get->weapon);
      
      $w3 = " AND ps.weaponId = '{$weapon}'";
    }
    
    $items = alxDatabaseManager::fetchMultiple
    ("
      SELECT 
        ps.*, 
        p.name, 
        p.kit, 
        p.nucleusId,
        p.soldierId,
        p.level
      FROM 
        profile_weaponStats AS ps, 
        profiles AS p
      WHERE 
        ps.soldierId = p.soldierId  
      AND 
        ps.kills > 100 
      AND 
        NOT EXISTS
        (
          SELECT * FROM profile_weaponStats AS pw WHERE pw.soldierId = ps.soldierId AND pw.date > ps.date
        )
      {$w2}
      {$w} 
      {$w3} 
      GROUP BY 
        ps.soldierId 
      ORDER BY 
        ps.{$sortKey} DESC, 
        ps.date DESC 
      LIMIT {$l}
    ");
    /*
    foreach($items as $item)
    {
      $t = time() - (86400 * 7);
      
      if($item->date <= $t)
      {
        $cmd = "nice php /var/www/t4g_blacklist/bl/fetchStatsDo.php {$item->nucleusId} 2>&1 & echo $!";
        pclose(popen($cmd, 'r'));
      }
    }
    */
    $this->add('sortKey', $sortKey);
    
    $this->add('items', $items);
    
    $this->render();
  }
}
