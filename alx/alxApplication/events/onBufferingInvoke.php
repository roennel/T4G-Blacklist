<?php

$event->addCallback(function()
{
	ob_start(function($content)
	{
		$doc = new DOMDocument;
    $doc->formatOutput = true;
    $doc->substituteEntities = true;
        
    $doc->loadHTML($content);
				
    $com = strftime(self::GLOBALVIEW_TOP_COMMENT);
    $com = html_entity_decode($com, ENT_COMPAT, 'UTF-8');

    $res = utf8_encode($com . "\n" . $doc->saveHTML());

    if(alxApplication::getConfigVar('minifyCode', 'app') == true)
    {
        $res = str_replace("\n", null, $res);
        $res = str_replace("\r", null, $res);
        $res = str_replace("\t", null, $res);
        $res = str_replace("\0", null, $res);
        $res = str_replace("\x0B", null, $res);
        $res = str_replace("  ", null, $res);
    }
        
    return (string) $res;
	});
		
	return true;
});