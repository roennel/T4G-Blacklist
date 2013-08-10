<?php

class DevController extends alxController
{
	function availableEvents()
	{
		$eventCollection = alxEvents::getEventCollection();
		
		$this->add('eventCollection', $eventCollection);
		
		$this->render();
	}
}