<?php

class ServerModel extends alxModel
{
  function __construct()
  {
    $this->idKey = 'serverId';
    $this->table = 'servers';
  }
}
