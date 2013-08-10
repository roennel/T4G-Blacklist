<?php

$event->addCallback(function()
{
	alxApplication::$systemLog->addLog('Finalize');
});