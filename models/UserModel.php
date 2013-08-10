<?php

class UserModel extends alxModel
{
  function __construct()
  {
    $this->idKey = 'userId';
    $this->table = 'users';
  }
}
