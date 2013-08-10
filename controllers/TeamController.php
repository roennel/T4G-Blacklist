<?php

class TeamController extends alxController
{
  function index($get)
  {
    $this->render(@$get->debug ? 'index2' : 'index2');
  }
}
