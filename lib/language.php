<?php

$defaultLang = 'en';

function getLang()
{
  global $defaultLang;
  
  if(array_key_exists('t4gLang', $_SESSION))
  {
    $lang = $_SESSION['t4gLang'];
  }
  elseif(array_key_exists('lang', $_GET))
  {
    $lang = $_GET['lang'];
  }
  else
  {
    $lang = $defaultLang;
  }
  
  return $lang;
}


function loadLanguage($lang)
{
  $ini = parse_ini_file("lang/{$lang}.ini");
  $GLOBALS['t4gLang'] = $ini;  
}

function t()
{
  echo call_user_func_array('ts', func_get_args());
}

function ts()
{
  $args = func_get_args();
  
  $item = array_shift($args);
  
  if(!array_key_exists($item, $GLOBALS['t4gLang']))
  {
    return 'undefined';
  }
  
  $con = $GLOBALS['t4gLang'][$item];
  
  if(count($args) > 0)
  {
    $i = 1;
    
    foreach($args as $arg)
    {
      $con = str_replace("%{$i}", $arg, $con);
    }
  }
  
  $con = str_replace('$', '&', $con);
  
  return $con;
}
