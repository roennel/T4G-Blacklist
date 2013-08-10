<?php

class StaticController extends alxController
{
  function tos()
  {
    header('content-type: text/plain; charset=UTF-8');
    
    ob_start(function($content)
    {
      return $content;
    });
    
    $this->respond();
  }
  
  function privacyPolicy()
  {
    header('content-type: text/plain; charset=UTF-8');
    
    ob_start(function($content)
    {
      return $content;
    });
    
    $this->respond();
  }
}
