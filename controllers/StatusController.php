<?php

class StatusController extends alxController
{
  function index()
  {
    $todayStart = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    $todayEnd = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
    
    $today = alxDatabaseManager::query("SELECT COUNT(k.kickLogId) AS kicks, bl.label AS label FROM bans AS b, blacklists AS bl, kickLog AS k 
    WHERE b.banId = k.banId AND b.blacklistId = bl.blacklistId AND k.date > '{$todayStart}' AND k.date < '{$todayEnd}' GROUP BY b.blacklistId");
    
    $todayG = alxDatabaseManager::query("SELECT COUNT(k.kickLogId) AS kicks FROM kickLog AS k 
    WHERE k.banId = '0' AND k.date > '{$todayStart}' AND k.date < '{$todayEnd}'")->fetch();
    
    $todayB = alxDatabaseManager::query("SELECT COUNT(b.banId) AS bans, bl.label AS label FROM bans AS b, blacklists AS bl 
    WHERE b.active = '1' AND b.blacklistId = bl.blacklistId AND b.created > '{$todayStart}' AND b.created < '{$todayEnd}' GROUP BY b.blacklistId");

    $monthStart = mktime(0, 0, 0, date('m'), 1, date('Y'));
    $monthEnd = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
    
    $month = alxDatabaseManager::query("SELECT COUNT(k.kickLogId) AS kicks, bl.label AS label FROM bans AS b, blacklists AS bl, kickLog AS k 
    WHERE b.banId = k.banId AND b.blacklistId = bl.blacklistId AND k.date > '{$monthStart}' AND k.date < '{$monthEnd}' GROUP BY b.blacklistId");
    
    $monthG = alxDatabaseManager::query("SELECT COUNT(k.kickLogId) AS kicks FROM kickLog AS k 
    WHERE k.banId = '0' AND k.date > '{$monthStart}' AND k.date < '{$monthEnd}'")->fetch();
    
    $monthB = alxDatabaseManager::query("SELECT COUNT(b.banId) AS bans, bl.label AS label FROM bans AS b, blacklists AS bl 
    WHERE b.active = '1' AND b.blacklistId = bl.blacklistId AND b.created > '{$monthStart}' AND b.created < '{$monthEnd}' GROUP BY b.blacklistId");
    
    $total = alxDatabaseManager::query("SELECT COUNT(k.kickLogId) AS kicks, bl.label AS label FROM bans AS b, blacklists AS bl, kickLog AS k 
    WHERE b.banId = k.banId AND b.blacklistId = bl.blacklistId GROUP BY b.blacklistId");
    
    $totalG = alxDatabaseManager::query("SELECT COUNT(k.kickLogId) AS kicks FROM kickLog AS k 
    WHERE k.banId = '0'")->fetch();
    
    $totalB = alxDatabaseManager::query("SELECT COUNT(b.banId) AS bans, bl.label AS label FROM bans AS b, blacklists AS bl 
    WHERE b.active = '1' AND b.blacklistId = bl.blacklistId GROUP BY b.blacklistId");
    
    $this->add('today', $today);
    $this->add('month', $month);
    $this->add('total', $total);
    
    $this->add('todayG', $todayG->kicks);
    $this->add('monthG', $monthG->kicks);
    $this->add('totalG', $totalG->kicks);
    
    $this->add('todayB', $todayB);
    $this->add('monthB', $monthB);
    $this->add('totalB', $totalB);
    
    $this->render();
  }
}
