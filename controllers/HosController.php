<?php

function weaponName($l)
{
  $l = str_replace('WEAPON', '', $l);
          $l = str_replace('DEFAULT', '', $l);
          $l = str_replace('_LMG_', '', $l);
          $l = str_replace('_SRIFLE_', '', $l);
          $l = str_replace('_SMG_', '', $l);
          $l = str_replace('_SG_', '', $l);
          $l = str_replace('_PISTOL_', '', $l);
          $l = str_replace('_AR_', '', $l);
          $l = str_replace('_I', ' +3', $l);
          $l = str_replace('_', ' ', $l);
          
  return $l;
}

function getProfileBySoldierId($soldierId)
{
  return alxDatabaseManager::query
  ("
    SELECT * FROM profiles WHERE soldierId = '{$soldierId}' LIMIT 1
  ")->fetch();
}

class HosController extends alxController
{
  function index($get)
  {
    $limit = 5;
    $nr = 5;
    
    $add = array
    (
      'bestRangedKill' => 'ps.bestRangedKill < 500'
    );

    $simple = function($key, $w='', $tbl='profile_stats') use($limit, $add)
    {
      $a = '';
      
      if(array_key_exists($key, $add))
      {
        $a = 'AND ' . $add[$key] . ' ';
      }
      
      if($w != '')
      {
        $w = 'AND ' . $w;  
      }
      
      $avg = alxDatabaseManager::query
      ("
        SELECT SUM(ps.{$key}) AS sum, COUNT(ps.{$key}) AS count  
        FROM {$tbl} AS ps 
        WHERE 1 = 1 {$w} {$a} 
        GROUP BY ps.soldierId 
      ")->fetch();

      return array($avg->sum / $avg->count, alxDatabaseManager::query
      ("
        SELECT ps.{$key} AS v, ps.soldierId 
        FROM {$tbl} AS ps, profiles AS p 
        WHERE ps.soldierId = p.soldierId 
        {$w} {$a}
        GROUP BY ps.soldierId 
        ORDER BY ps.{$key} DESC 
        LIMIT {$limit}
      "));
    };
    
    $this->add('noTime', false);
    
    $check = function($l) use($get)
    {
      if(@$get->cat)
      {
        if(strpos($l, "_{$get->cat}_") === false)
        {
          return false;
        }
      }
      
      return true;
    };
    
    
    switch(@$get->type)
    {
      case 'weaponsAccuracy':
        
        $m = true;
        $p = '%';
        $t = '-';
    
        switch(@$get->key)
        {
          case 'hs':
            $key = 'headshotratio';
            $label = 'Headshot Ratio';
          break;
          
          case 'brk':
            $key = 'bestRangedKill';
            $label = 'Best Ranged Kill';
            $m = false;
            $p = 'm';
          break;
          
          case 'dpb':
            $key = 'dpb';
            $label = 'Damage / Bullet';
            $m = false;
            $p = '';
          break;
            
          default:
            $key = 'accuracy';
            $label = 'Accuracy';
        }
        
        $s = array();
        
        foreach($GLOBALS['weaponIds'] as $weaponId => $weaponLabel)
        {
          $stat = alxDatabaseManager::query
          ("
            SELECT SUM({$key}) AS sum, COUNT({$key}) AS count
            FROM profile_weaponStats 
            WHERE weaponId = '{$weaponId}' AND {$key} > 0
            ORDER BY sum DESC
          ")->fetch();
          
          $q = $stat->sum / ($stat->count == 0 ? 1 : $stat->count);  
          if($m) $q = $q * 100;
          
          if($check($weaponLabel))
          {
            $s[weaponName($weaponLabel)] = $q;
          }
        }
        
        arsort($s);
        
        $this->add('perc', $p);
        $this->add('noTime', true);
        $this->add('label', "Average Weapons {$label}");
        $this->add('stats', $s);
        $this->add('total', $t);
        
        
        $this->render('weapons');
        return;
        
      case 'weaponsPopularity':
        
        $s = array();
        $t = 0;
        
        foreach($GLOBALS['weaponIds'] as $weaponId => $weaponLabel)
        {
          $stats = alxDatabaseManager::query
          ("
            SELECT SUM(timeUsed) AS sum 
            FROM profile_weaponStats 
            WHERE weaponId = '{$weaponId}' 
            GROUP BY soldierId 
            ORDER BY sum DESC
          ");
          
          $q = 0;
          
          while($stat = $stats->fetch())
          {
            $q+= $stat->sum;  
            if($check($weaponLabel))
            {
              $t+= $stat->sum;
            }
          }
          
          if($check($weaponLabel))
          {
            $s[weaponName($weaponLabel)] = $q;
          }
        }
        
        arsort($s);
        
        $this->add('label', 'Weapons Popularity (Time Used)');
        $this->add('stats', $s);
        $this->add('total', $t . '%');
        
        
        $this->render('weapons');
        return;
        
      default:
        
        $stats = array
        (
          'Best Score in a Round' => $simple('bestScore', "ps.games > {$nr}"),
          'Best Killstreak' => $simple('killStreak', "ps.games > {$nr}"),
          'Average Score / Minute' => $simple('spm', "ps.games > {$nr}"),
          'Average Kills / Minute' => $simple('kpm', "ps.games > {$nr}"),
          'Average Score / Game' => $simple('spg', "ps.games > {$nr}"),
          'Average Kills / Game' => $simple('kpg', "ps.games > {$nr}")
        );
    }


    $this->add('stats', $stats);
    
    $this->render();
  }
}
