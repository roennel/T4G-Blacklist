<?php

class PbuconController extends alxController
{
  function index($get)
  {
    $items = alxDatabaseManager::fetchMultiple
    ("SELECT s.name, pbl.date, pbl.msg FROM pb_log AS pbl, servers AS s WHERE pbl.serverId = s.serverId ORDER BY pbl.date DESC LIMIT 100");
    
    $this->add('items', $items);
    
    $this->render();
  }
}
